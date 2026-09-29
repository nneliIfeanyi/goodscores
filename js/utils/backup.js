import { api, getUser, saveUser, storageUrl } from './api.js';
import { getUnsyncedQuestions, getOfflineQuestions, getOfflinePapers, saveOfflinePaper, markQuestionBackedUp, getOfflineQuestionSummary, mergeRestoredQuestions, mergeRestoredPapers } from './db.js?v=25';
import { toast } from './toast.js';
import { requireAuthentication } from './authGate.js';
import { loadMetaData } from './meta.js';

let backupInProgress = false;
let restoreInProgress = false;

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

export async function backupQuestionBank({ offlineId = null } = {}) {
  if (!localStorage.getItem('gs_token')) {
    requireAuthentication('Question bank backup', () => backupQuestionBank({ offlineId }));
    return;
  }
  if (backupInProgress) return;
  if (!navigator.onLine) {
    toast('Backup requires an internet connection. Your questions remain saved successfully.', 'warn');
    return;
  }
  backupInProgress = true;
  try {
    const unsyncedQuestions = await getUnsyncedQuestions();
    const questions = offlineId
      ? unsyncedQuestions.filter((question) => question.offline_id === offlineId)
      : unsyncedQuestions;
    if (questions.length) {
      const result = await api('/backup/questions', {
        method: 'POST',
        body: JSON.stringify({ questions }),
      });
      const processed = result.data?.processed || [];
      for (const item of processed) {
        const local = questions.find((question) => question.offline_id === item.offline_id);
        if (local) await markQuestionBackedUp(item.offline_id, item.id, result.data?.revision);
      }
      const cost = result.data?.cost ?? processed.filter((item) => !item.deleted).length;
      const balance = result.data?.credits_left;
      const user = getUser();
      if (user && balance !== undefined && balance !== -1) saveUser({ ...user, credits: balance });
      const balanceText = balance === undefined ? '' : ` ${balance} credits remaining.`;
      toast(`Backed up ${processed.length} question${processed.length === 1 ? '' : 's'} for ${cost} credit${cost === 1 ? '' : 's'}.${balanceText}`, 'success');
    }
    await backupPendingPapers();
    if (!questions.length) toast('Your question bank is already backed up.', 'info');
    window.dispatchEvent(new CustomEvent('gs-backup-status-refresh'));
  } catch (error) {
    toast(error.message || 'Question bank backup failed', 'error');
  } finally {
    backupInProgress = false;
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
