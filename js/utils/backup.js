import { api, getUser, saveUser, storageUrl } from './api.js';
import { getUnsyncedQuestions, markQuestionBackedUp, getOfflineQuestionSummary, mergeRestoredQuestions } from './db.js';
import { toast } from './toast.js';
import { requireAuthentication } from './authGate.js';

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
    if (!questions.length) {
      toast('Your question bank is already backed up.', 'info');
      return;
    }
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
    window.dispatchEvent(new CustomEvent('gs-backup-status-refresh'));
  } catch (error) {
    toast(error.message || 'Question bank backup failed', 'error');
  } finally {
    backupInProgress = false;
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
    toast(`Restored ${outcome.added + outcome.updated} question${outcome.added + outcome.updated === 1 ? '' : 's'}; kept ${outcome.skipped} local change${outcome.skipped === 1 ? '' : 's'}.`, 'success');
    window.dispatchEvent(new CustomEvent('gs-backup-status-refresh'));
    window.dispatchEvent(new CustomEvent('gs-questions-refresh'));
    return outcome;
  } catch (error) {
    toast(error.message || 'Question bank restore failed', 'error');
    return null;
  } finally {
    restoreInProgress = false;
  }
}
