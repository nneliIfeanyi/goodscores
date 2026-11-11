import { api, getUser, storageUrl } from './api.js';
import { saveOfflineQuestion, getOfflineQuestion, saveOfflinePassage, getOfflinePassage, getOfflinePassages, getUnsyncedPassages, markPassageBackedUp, replaceOfflinePassageReferences, getUnsyncedQuestions, getOfflineQuestions, getQuestionsForRecovery, prepareQuestionsForRecovery, getOfflinePapers, saveOfflinePaper, markQuestionBackedUp, getOfflineQuestionSummary, mergeRestoredQuestions, mergeRestoredPapers, getCachedMeta, cacheMeta, ownerKey } from './db.js?v=28';
import { toast } from './toast.js';
import { requireAuthentication } from './authGate.js';
import { loadMetaData } from './meta.js';
import { confirmModal } from './modal.js';
import { fetchServerQuestions } from './questionBank.js';

let backupInProgress = false;
let backupRequested = false;
let restoreInProgress = false;
let legacyRecoveryDeferredKey = null;

const LEGACY_QUESTION_RECOVERY_KEY = 'legacy-question-recovery-v1';

async function cacheRestoreImages(question) {
  if (!Array.isArray(question.images) || !question.images.length) return question;
  const images = await Promise.all(question.images.map(async (image) => {
    if (image.data_url || !image.file_path) return image;
    try {
      const response = await fetch(storageUrl(image.file_path));
      if (!response.ok) return image;
      const blob = await response.blob();
      const dataUrl = await new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result);
        reader.onerror = () => reject(reader.error);
        reader.readAsDataURL(blob);
      });
      return { ...image, data_url: dataUrl };
    } catch (_) {
      return image;
    }
  }));
  return { ...question, images };
}

export async function backupQuestionBank({ offlineId = null, silent = false } = {}) {
  if (!localStorage.getItem('gs_token')) {
    requireAuthentication('Question bank backup', () => backupQuestionBank({ offlineId }));
    return;
  }
  if (backupInProgress) {
    backupRequested = true;
    return false;
  }
  if (!navigator.onLine) {
    if (!silent) toast('Sync requires an internet connection. Your questions remain saved successfully.', 'warn');
    return false;
  }
  backupInProgress = true;
  let recoveredCount = 0;
  try {
    recoveredCount = await recoverLegacyQuestions();
    await backupPendingPassages();
    const unsyncedQuestions = await getUnsyncedQuestions();
    const targetOfflineId = recoveredCount ? null : offlineId;
    const questions = targetOfflineId
      ? unsyncedQuestions.filter((question) => question.offline_id === targetOfflineId)
      : unsyncedQuestions;
    let processedCount = 0;
    const batchSize = 100;
    for (let offset = 0; offset < questions.length; offset += batchSize) {
      const batch = questions.slice(offset, offset + batchSize);
      const result = await api('/backup/questions', {
        method: 'POST',
        body: JSON.stringify({ questions: batch }),
      });
      const processed = result.data?.processed;
      const processedIds = new Set(Array.isArray(processed) ? processed.map((item) => String(item?.offline_id ?? '')) : []);
      if (!Array.isArray(processed) || processed.length !== batch.length || batch.some((question) => !processedIds.has(String(question.offline_id)))) {
        throw new Error('The server did not confirm every question in the backup batch. Unconfirmed questions remain on this device.');
      }
      for (const item of processed) {
        const local = batch.find((question) => String(question.offline_id) === String(item.offline_id));
        if (local) await markQuestionBackedUp(local.offline_id, item.id, result.data?.revision);
      }
      processedCount += processed.length;
    }
    await backupPendingPapers();
    if (recoveredCount) {
      toast(`Recovered and synced ${recoveredCount} local question${recoveredCount === 1 ? '' : 's'}.`, 'success');
    } else if (processedCount && !silent) {
      toast(`Synced ${processedCount} question${processedCount === 1 ? '' : 's'}.`, 'success');
    } else if (!questions.length && !silent) {
      toast('Your question bank is already synced.', 'info');
    }
    window.dispatchEvent(new CustomEvent('gs-backup-status-refresh'));
    window.dispatchEvent(new CustomEvent('gs-questions-refresh'));
    window.dispatchEvent(new CustomEvent('gs-papers-refresh'));
    return true;
  } catch (error) {
    const recoveryNote = recoveredCount
      ? ` ${recoveredCount} recovered question${recoveredCount === 1 ? '' : 's'} remain saved on this device.`
      : '';
    toast((error.message || 'Question bank backup failed') + recoveryNote, 'error');
    return false;
  } finally {
    backupInProgress = false;
    if (backupRequested && navigator.onLine && localStorage.getItem('gs_token')) {
      backupRequested = false;
      void backupQuestionBank({ silent: true });
    }
  }
}

async function recoverLegacyQuestions() {
  const user = getUser();
  const recoveryAccount = user?.id ?? user?.email ?? 'signed-in';
  const recoveryKey = `${LEGACY_QUESTION_RECOVERY_KEY}:${String(recoveryAccount)}`;
  if (legacyRecoveryDeferredKey === recoveryKey) return 0;
  const recoveryState = await getCachedMeta(recoveryKey);
  if (recoveryState?.completed) return 0;

  const localQuestions = await getQuestionsForRecovery();
  if (!localQuestions.length) {
    await cacheMeta(recoveryKey, { completed: true, recovered: 0 });
    return 0;
  }

  const currentOwner = ownerKey();
  const hasLegacyOwner = localQuestions.some((question) => !question.deleted && question.owner_key !== currentOwner);
  if (!hasLegacyOwner) {
    await cacheMeta(recoveryKey, { completed: true, recovered: 0 });
    return 0;
  }

  let serverQuestions;
  try {
    serverQuestions = await fetchServerQuestions({});
  } catch (error) {
    toast('Could not check the online question bank for older local questions. Your local data was not changed; recovery will retry.', 'warn');
    return 0;
  }

  const serverByOfflineId = new Map(
    serverQuestions
      .filter((question) => question.offline_id)
      .map((question) => [String(question.offline_id), question])
  );
  const serverById = new Map(serverQuestions.map((question) => [String(question.id), question]));
  const findServerMatch = (question) => {
    const byOfflineId = question.offline_id
      ? serverByOfflineId.get(String(question.offline_id))
      : null;
    if (byOfflineId) return byOfflineId;

    const byId = question.id ? serverById.get(String(question.id)) : null;
    if (!byId) return null;
    const sameBody = String(question.body || '').trim() === String(byId.body || '').trim();
    const sameType = String(question.type || 'mcq') === String(byId.type || 'mcq');
    const sameSubject = String(question.subject_id ?? '') === String(byId.subject_id ?? '');
    const sameClass = String(question.class_id ?? '') === String(byId.class_id ?? '');
    const sameTerm = String(question.term_id ?? '') === String(byId.term_id ?? '');
    return sameBody && sameType && sameSubject && sameClass && sameTerm ? byId : null;
  };
  const recoverable = localQuestions.filter((question) => {
    if (question.deleted) return false;
    const ownerMismatch = question.owner_key !== currentOwner;
    const unsynced = question.backup_state !== 'backed_up';
    const missingServerCopy = question.backup_state === 'backed_up' && !findServerMatch(question);
    return (ownerMismatch && unsynced) || missingServerCopy;
  });

  if (!recoverable.length) {
    await cacheMeta(recoveryKey, { completed: true, recovered: 0 });
    return 0;
  }

  const confirmed = await confirmModal({
    title: 'Recover local questions?',
    message: `Found ${recoverable.length} question${recoverable.length === 1 ? '' : 's'} saved on this browser that are not in the online question bank. This device may contain data from another account. Import them into the account currently signed in? Cancel leaves them unchanged; reload the app to decide later. Local copies remain saved until the server confirms the sync.`,
    confirmLabel: 'Recover questions',
  });
  if (!confirmed) {
    legacyRecoveryDeferredKey = recoveryKey;
    return 0;
  }

  const questionsToRecover = recoverable.map((question) => ({
    ...question,
    id: findServerMatch(question)?.id ?? null,
  }));
  await prepareQuestionsForRecovery(questionsToRecover);
  window.dispatchEvent(new CustomEvent('gs-backup-status-refresh'));
  window.dispatchEvent(new CustomEvent('gs-questions-refresh'));
  await cacheMeta(recoveryKey, {
    completed: true,
    recovered: questionsToRecover.length,
    completed_at: new Date().toISOString(),
  });
  return questionsToRecover.length;
}

export async function saveQuestion(question) {
  let payload = { ...question };
  if (isLocalPassageId(payload.passage_id) && payload.passage_title && payload.passage_body) {
    const passage = await saveOfflinePassage({
      offline_id: String(payload.passage_id),
      title: String(payload.passage_title).trim(),
      body: String(payload.passage_body).trim(),
      subject_id: payload.subject_id || null,
      class_id: payload.class_id || null,
      term_id: payload.term_id || null,
    });
    payload.passage_id = passage.id || passage.offline_id;
  }

  const saved = await saveOfflineQuestion(payload);
  let synced = false;
  if (navigator.onLine && localStorage.getItem('gs_token')) {
    synced = await backupQuestionBank({ offlineId: saved.offline_id, silent: true });
  }
  const current = await getOfflineQuestion(saved.offline_id);
  return { ...(current || saved), synced };
}

function isLocalPassageId(value) {
  return typeof value === 'string' && value.startsWith('local-passage-');
}

async function queuePassagesFromLocalQuestions() {
  const questions = await getOfflineQuestions({ includeDeleted: true });
  const passages = new Map();
  for (const question of questions) {
    if (question.deleted || !isLocalPassageId(question.passage_id)) continue;
    const title = String(question.passage_title || '').trim();
    const body = String(question.passage_body || '').trim();
    if (title && body) {
      passages.set(question.passage_id, {
        offline_id: question.passage_id,
        title,
        body,
        subject_id: question.subject_id || null,
        class_id: question.class_id || null,
        term_id: question.term_id || null,
      });
    }
  }

  for (const passage of passages.values()) {
    await saveOfflinePassage(passage);
  }
}

async function backupPendingPassages() {
  await queuePassagesFromLocalQuestions();

  const savedPassages = await getOfflinePassages();
  for (const passage of savedPassages) {
    if (passage.backup_state === 'backed_up' && passage.id) {
      await replaceOfflinePassageReferences(passage.offline_id, passage.id, passage);
    }
  }

  const pendingPassages = await getUnsyncedPassages();
  for (const passage of pendingPassages) {
    const result = await api('/passages', {
      method: 'POST',
      body: JSON.stringify({
        offline_id: passage.offline_id,
        title: passage.title,
        body: passage.body,
        subject_id: passage.subject_id || null,
        class_id: passage.class_id || null,
        term_id: passage.term_id || null,
      }),
    });
    const serverId = Number(result.data?.id);
    if (!Number.isSafeInteger(serverId) || serverId < 1) {
      throw new Error('The server did not confirm the passage backup. It remains saved on this device.');
    }
    await markPassageBackedUp(passage.offline_id, serverId);
    await replaceOfflinePassageReferences(passage.offline_id, serverId, passage);
  }
}

async function backupPendingPapers() {
  const localQuestions = await getOfflineQuestions();
  const questionIdMap = new Map();
  localQuestions.forEach((question) => {
    if (question.id) questionIdMap.set(String(question.offline_id), Number(question.id));
    if (question.id) questionIdMap.set(String(question.id), Number(question.id));
  });

  const pendingPapers = (await getOfflinePapers()).filter((paper) => paper.backup_state !== 'backed_up');
  for (const paper of pendingPapers) {
    const mapQuestionId = (id) => questionIdMap.get(String(id)) || (Number.isInteger(Number(id)) ? Number(id) : null);
    const questionIds = (paper.question_ids || []).map(mapQuestionId).filter(Boolean);
    const paperSettings = JSON.parse(JSON.stringify(paper.paper_settings || {}));
    if (Array.isArray(paperSettings.sections)) {
      paperSettings.sections = paperSettings.sections.map((section) => ({
        ...section,
        question_ids: (section.question_ids || []).map(mapQuestionId).filter(Boolean),
      }));
    }
    if (!questionIds.length) continue;

    try {
      const payload = {
        title: paper.title,
        subject_id: paper.subject_id || null,
        class_id: paper.class_id || null,
        term_id: paper.term_id || null,
        header_override: paper.header_override || null,
        paper_settings: paperSettings,
        question_ids: questionIds,
        status: paper.status || 'draft',
      };
      const endpoint = paper.id ? `/papers/${paper.id}` : '/papers';
      const response = await api(endpoint, {
        method: paper.id ? 'PUT' : 'POST',
        body: JSON.stringify(payload),
      });
      await saveOfflinePaper({
        ...paper,
        ...(response.data || {}),
        offline_id: paper.offline_id,
        question_ids: questionIds,
        paper_settings: paperSettings,
        backup_state: 'backed_up',
      });
    } catch (error) {
      toast(`Paper backup failed: ${error.message}`, 'warn');
    }
  }
}

export async function refreshBackupStatus() {
  const summary = await getOfflineQuestionSummary();
  window.dispatchEvent(new CustomEvent('gs-backup-status', { detail: { online: navigator.onLine, ...summary } }));
  return summary;
}

export async function restoreQuestionBank() {
  if (!localStorage.getItem('gs_token')) {
    requireAuthentication('Question bank restore', restoreQuestionBank);
    return;
  }
  if (restoreInProgress) return;
  if (!navigator.onLine) {
    toast('Restore requires an internet connection.', 'warn');
    return;
  }
  restoreInProgress = true;
  try {
    const result = await api('/backup/questions');
    const questions = await Promise.all((result.data?.questions || []).map(cacheRestoreImages));
    const outcome = await mergeRestoredQuestions(questions);
    const paperResult = await api('/backup/papers');
    const paperOutcome = await mergeRestoredPapers(paperResult.data?.papers || []);
    await loadMetaData({ refresh: true });
    const restoredCount = outcome.added + outcome.updated;
    const restoredPaperCount = paperOutcome.added + paperOutcome.updated;
    const skippedCount = outcome.skipped + paperOutcome.skipped;
    toast(`Restored ${restoredCount} question${restoredCount === 1 ? '' : 's'} and ${restoredPaperCount} paper${restoredPaperCount === 1 ? '' : 's'}; kept ${skippedCount} local change${skippedCount === 1 ? '' : 's'}.`, 'success');
    window.dispatchEvent(new CustomEvent('gs-backup-status-refresh'));
    window.dispatchEvent(new CustomEvent('gs-questions-refresh'));
    window.dispatchEvent(new CustomEvent('gs-papers-refresh'));
    window.dispatchEvent(new CustomEvent('gs-meta-refresh'));
    return outcome;
  } catch (error) {
    toast(error.message || 'Question bank restore failed', 'error');
    return null;
  } finally {
    restoreInProgress = false;
  }
}
