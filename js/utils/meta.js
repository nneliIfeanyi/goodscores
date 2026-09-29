import { api, getUser } from './api.js';
import { cacheMeta, getCachedMeta } from './db.js';

/** Terms are available by default; subjects and classes belong to each user. */
export const DEFAULT_META = {
  subjects: [],
  classes: [],
  terms: [
    { id: 1, name: '1st Term' },
    { id: 2, name: '2nd Term' },
    { id: 3, name: '3rd Term' },
    { id: 4, name: 'Mid-Term' },
    { id: 5, name: 'Mock Exam' },
  ],
};

function removeLegacyDefaults(meta) {
  return {
    ...meta,
    subjects: uniqueRows(meta.subjects),
    classes: uniqueRows(meta.classes),
  };
}

function uniqueRows(rows) {
  const seenIds = new Set();
  const seenNames = new Set();
  return (rows || []).filter((item) => {
    const id = item?.id == null ? '' : String(item.id);
    const name = String(item?.name || '').trim().toLowerCase();
    if ((id && seenIds.has(id)) || (name && seenNames.has(name))) return false;
    if (id) seenIds.add(id);
    if (name) seenNames.add(name);
    return true;
  });
}

function mergeRows(...sources) {
  return uniqueRows(sources.flat().filter(Boolean));
}

function isPendingRow(row) {
  return !!row?.local_only || String(row?.id || '').startsWith('local-');
}

async function syncPendingRows(endpoint, rows, remoteRows, toPayload) {
  const remoteNames = new Set((remoteRows || []).map((row) => String(row?.name || '').trim().toLowerCase()).filter(Boolean));
  const synced = [];
  for (const row of rows.filter(isPendingRow)) {
    const name = String(row.name || '').trim();
    const normalizedName = name.toLowerCase();
    if (!name || remoteNames.has(normalizedName)) continue;
    try {
      const response = await api(endpoint, { method: 'POST', body: JSON.stringify(toPayload(row, name)) });
      if (response.data) {
        synced.push(response.data);
        remoteNames.add(normalizedName);
      }
    } catch (_) {
      synced.push(row);
    }
  }
  return synced;
}

/**
 * Load subjects, classes, and terms from IndexedDB/defaults only.
 * Account CRUD may explicitly write changes to the server, but ordinary
 * question, paper, and AI workflows never read metadata from the server.
 */
export async function loadMetaData({ refresh = false } = {}) {
  const user = getUser();
  const cacheKey = `all-v2:${user?.id || 'anonymous'}`;
  let cached = null;
  let anonymousCached = null;
  try {
    cached = await getCachedMeta(cacheKey);
    if (user?.id) anonymousCached = await getCachedMeta('all-v2:anonymous');
  } catch (_) {}

  let fallback = removeLegacyDefaults({
    ...DEFAULT_META,
    ...(anonymousCached || {}),
    ...(cached || {}),
    subjects: [...(anonymousCached?.subjects || []), ...(cached?.subjects || [])],
    classes: [...(anonymousCached?.classes || []), ...(cached?.classes || [])],
  });

  if (refresh && user && navigator.onLine) {
    try {
      const [subjectsResponse, classesResponse, termsResponse] = await Promise.all([
        api('/meta/subjects'),
        api('/meta/classes'),
        api('/meta/terms'),
      ]);
      const syncedSubjects = await syncPendingRows(
        '/meta/subjects',
        fallback.subjects,
        subjectsResponse.data,
        (row, name) => ({ name, code: row.code || null })
      );
      const syncedClasses = await syncPendingRows(
        '/meta/classes',
        fallback.classes,
        classesResponse.data,
        (row, name) => ({ name, sort_order: Number(row.sort_order || 0) })
      );
      fallback = removeLegacyDefaults({
        ...fallback,
        subjects: mergeRows(subjectsResponse.data, fallback.subjects.filter((row) => !isPendingRow(row)), syncedSubjects),
        classes: mergeRows(classesResponse.data, fallback.classes.filter((row) => !isPendingRow(row)), syncedClasses),
        terms: mergeRows(termsResponse.data, fallback.terms),
      });
    } catch (_) {
      // Keep the cached metadata when the server is unavailable.
    }
  }

  try {
    await cacheMeta(cacheKey, fallback);
  } catch (_) {}
  return fallback;
}

export async function updateCachedMeta(changes = {}) {
  const user = getUser();
  const cacheKey = `all-v2:${user?.id || 'anonymous'}`;
  const current = await getCachedMeta(cacheKey) || { ...DEFAULT_META };
  const next = removeLegacyDefaults({ ...DEFAULT_META, ...current, ...changes });
  await cacheMeta(cacheKey, next);
  return next;
}

/** Build <option> HTML for a list of {id, name} */
export function optionsHtml(list, selectedId = null) {
  return (list || [])
    .map(
      (item) =>
        `<option value="${item.id}" ${String(item.id) === String(selectedId) ? 'selected' : ''}>${escapeHtml(item.name)}</option>`
    )
    .join('');
}

function escapeHtml(str) {
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}
