import { api } from './api.js';
import { getOfflineQuestions, getUnsyncedQuestions, cacheQuestion } from './db.js';
import { toast } from './toast.js';

function matchesFilters(question, filters) {
  return (!filters.type || question.type === filters.type)
    && (!filters.subject_id || String(question.subject_id) === String(filters.subject_id))
    && (!filters.class_id || String(question.class_id) === String(filters.class_id))
    && (!filters.term_id || String(question.term_id) === String(filters.term_id))
    && (!filters.search || String(question.body || '').toLowerCase().includes(filters.search.toLowerCase()));
}

async function fetchServerQuestions(filters) {
  const limit = 100;
  const questions = [];
  let offset = 0;
  while (true) {
    const params = new URLSearchParams({ limit: String(limit), offset: String(offset) });
    Object.entries(filters).forEach(([key, value]) => {
      if (value) params.set(key, value);
    });
    const result = await api(`/questions?${params}`);
    if (!Array.isArray(result.data)) throw new Error('The server returned an invalid question list');
    questions.push(...result.data);
    if (result.data.length < limit) return questions;
    offset += limit;
  }
}

export async function getQuestionBank(filters = {}, { onlineOnly = false, serverOnly = false } = {}) {
  if (!navigator.onLine || !localStorage.getItem('gs_token')) {
    if (onlineOnly) throw new Error('Question selection requires an online authenticated session.');
    return (await getOfflineQuestions()).filter((question) => matchesFilters(question, filters));
  }

  try {
    const serverQuestions = await fetchServerQuestions(filters);
    if (serverOnly) return serverQuestions;
    const pending = await getUnsyncedQuestions();
    const questionsById = new Map();
    for (const question of serverQuestions) {
      const cached = await cacheQuestion(question);
      const key = String(question.offline_id || `server:${question.id}`);
      questionsById.set(key, {
        ...question,
        offline_id: question.offline_id || `server:${question.id}`,
        images: question.images || cached?.images,
      });
    }

    for (const question of pending) {
      if (question.id) {
        for (const [key, serverQuestion] of questionsById) {
          if (String(serverQuestion.id) === String(question.id)) questionsById.delete(key);
        }
      }
      if (question.deleted) {
        questionsById.delete(String(question.offline_id));
      } else if (matchesFilters(question, filters)) {
        questionsById.set(String(question.offline_id), question);
      }
    }
    return [...questionsById.values()];
  } catch (error) {
    if (onlineOnly) throw error;
    toast(error.message || 'Could not load questions from the server', 'error');
    const offlineQuestions = error.offline
      ? await getOfflineQuestions()
      : await getUnsyncedQuestions();
    return offlineQuestions
      .filter((question) => !question.deleted && matchesFilters(question, filters));
  }
}
