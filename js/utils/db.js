/** Local-first question bank storage. The server is only written by backup.js. */
const DB_NAME = 'goodscores';
const DB_VERSION = 3;

function ownerKey() {
  let key = localStorage.getItem('gs_device_key');
  if (!key) {
    key = `device:${crypto.randomUUID()}`;
    localStorage.setItem('gs_device_key', key);
  }
  return key;
}

function openDB() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open(DB_NAME, DB_VERSION);
    request.onupgradeneeded = (event) => {
      const db = event.target.result;
      let questions;
      if (!db.objectStoreNames.contains('questions')) {
        questions = db.createObjectStore('questions', { keyPath: 'offline_id' });
      } else {
        questions = event.target.transaction.objectStore('questions');
      }
      ['owner_key', 'updated_at', 'deleted', 'backup_revision'].forEach((index) => {
        if (!questions.indexNames.contains(index)) questions.createIndex(index, index, { unique: false });
      });
      if (!db.objectStoreNames.contains('papers')) db.createObjectStore('papers', { keyPath: 'offline_id' });
      if (!db.objectStoreNames.contains('meta')) db.createObjectStore('meta', { keyPath: 'key' });
    };
    request.onsuccess = () => resolve(request.result);
    request.onerror = () => reject(request.error);
  });
}

function putQuestion(record) {
  return openDB().then((db) => new Promise((resolve, reject) => {
    const tx = db.transaction('questions', 'readwrite');
    tx.objectStore('questions').put(record);
    tx.oncomplete = () => resolve(record);
    tx.onerror = () => reject(tx.error);
  }));
}

export async function saveOfflineQuestion(question) {
  const existing = question.offline_id ? await getOfflineQuestion(question.offline_id) : null;
  const now = new Date().toISOString();
  return putQuestion({
    ...(existing || {}), ...question,
    offline_id: question.offline_id || crypto.randomUUID(),
    owner_key: ownerKey(),
    deleted: false,
    source: question.source || existing?.source || 'manual',
    created_at: question.created_at || existing?.created_at || now,
    updated_at: now,
    backup_state: 'pending',
    backup_revision: (existing?.backup_revision || 0) + 1,
  });
}

export async function cacheQuestion(question) {
  const now = question.updated_at || question.created_at || new Date().toISOString();
  return putQuestion({
    ...question,
    offline_id: question.offline_id || `server:${question.id}`,
    owner_key: ownerKey(),
    deleted: false,
    backup_state: 'backed_up',
    backup_revision: question.backup_revision || 0,
    updated_at: now,
  });
}

export async function mergeRestoredQuestions(serverQuestions) {
  const localQuestions = await getOfflineQuestions({ includeDeleted: true });
  const localByOfflineId = new Map(localQuestions.map((question) => [question.offline_id, question]));
  let added = 0;
  let updated = 0;
  let skipped = 0;

  for (const question of serverQuestions || []) {
    const offlineId = question.offline_id || `server:${question.id}`;
    const existing = localByOfflineId.get(offlineId);
    if (existing && existing.backup_state !== 'backed_up') {
      skipped += 1;
      continue;
    }

    const serverTime = Date.parse(question.updated_at || question.created_at || '') || 0;
    const localTime = Date.parse(existing?.updated_at || existing?.created_at || '') || 0;
    if (existing && localTime > serverTime) {
      skipped += 1;
      continue;
    }

    await putQuestion({
      ...(existing || {}),
      ...question,
      offline_id: offlineId,
      owner_key: ownerKey(),
      deleted: false,
      backup_state: 'backed_up',
      backup_revision: existing?.backup_revision || 0,
      last_backup_at: existing?.last_backup_at || new Date().toISOString(),
    });
    if (existing) updated += 1;
    else added += 1;
    localByOfflineId.set(offlineId, question);
  }

  return { added, updated, skipped };
}

export async function getOfflineQuestion(offlineId) {
  const db = await openDB();
  return new Promise((resolve, reject) => {
    const request = db.transaction('questions', 'readonly').objectStore('questions').get(offlineId);
    request.onsuccess = () => resolve(request.result?.owner_key === ownerKey() ? request.result : null);
    request.onerror = () => reject(request.error);
  });
}

export async function getOfflineQuestions({ includeDeleted = false } = {}) {
  const db = await openDB();
  return new Promise((resolve, reject) => {
    const request = db.transaction('questions', 'readonly').objectStore('questions').getAll();
    request.onsuccess = () => resolve((request.result || []).filter((q) => q.owner_key === ownerKey() && (includeDeleted || !q.deleted)));
    request.onerror = () => reject(request.error);
  });
}

export async function getUnsyncedQuestions() {
  return (await getOfflineQuestions({ includeDeleted: true })).filter((q) => q.backup_state !== 'backed_up');
}

export async function markQuestionBackedUp(offlineId, serverId, revision) {
  const record = await getOfflineQuestion(offlineId);
  if (!record) return;
  record.id = serverId || record.id;
  record.backup_state = 'backed_up';
  record.backup_revision = revision || record.backup_revision;
  record.last_backup_at = new Date().toISOString();
  delete record._pending_image;
  if (record.deleted) return deleteStoredQuestion(offlineId);
  return putQuestion(record);
}

async function deleteStoredQuestion(offlineId) {
  const db = await openDB();
  return new Promise((resolve, reject) => {
    const tx = db.transaction('questions', 'readwrite');
    tx.objectStore('questions').delete(offlineId);
    tx.oncomplete = resolve;
    tx.onerror = () => reject(tx.error);
  });
}

export async function deleteOfflineQuestion(offlineId) {
  const record = await getOfflineQuestion(offlineId);
  if (!record) return;
  record.deleted = true;
  record.updated_at = new Date().toISOString();
  record.backup_state = 'pending';
  record.backup_revision = (record.backup_revision || 0) + 1;
  return putQuestion(record);
}

export async function getOfflineQuestionSummary() {
  const questions = await getOfflineQuestions({ includeDeleted: true });
  const pending = questions.filter((q) => q.backup_state !== 'backed_up');
  return { pending: pending.length, failed: 0, synced: questions.length - pending.length, deleted: pending.filter((q) => q.deleted).length };
}

export async function cacheMeta(key, data) {
  const db = await openDB();
  return new Promise((resolve, reject) => {
    const tx = db.transaction('meta', 'readwrite');
    tx.objectStore('meta').put({ key, data, cached_at: Date.now() });
    tx.oncomplete = resolve;
    tx.onerror = () => reject(tx.error);
  });
}

export async function getCachedMeta(key) {
  const db = await openDB();
  return new Promise((resolve, reject) => {
    const request = db.transaction('meta', 'readonly').objectStore('meta').get(key);
    request.onsuccess = () => resolve(request.result?.data ?? null);
    request.onerror = () => reject(request.error);
  });
}

export async function saveOfflinePaper(paper) {
  const db = await openDB();
  const existing = paper.offline_id ? await getOfflinePaper(paper.offline_id) : null;
  const record = {
    ...(existing || {}), ...paper,
    offline_id: paper.offline_id || crypto.randomUUID(),
    owner_key: ownerKey(),
    backup_state: paper.backup_state || 'pending',
    updated_at: new Date().toISOString(),
  };
  return new Promise((resolve, reject) => {
    const tx = db.transaction('papers', 'readwrite');
    tx.objectStore('papers').put(record);
    tx.oncomplete = () => resolve(record);
    tx.onerror = () => reject(tx.error);
  });
}

export async function getOfflinePaper(id) {
  const db = await openDB();
  return new Promise((resolve, reject) => {
    const request = db.transaction('papers', 'readonly').objectStore('papers').get(id);
    request.onsuccess = () => resolve(request.result?.owner_key === ownerKey() ? request.result : null);
    request.onerror = () => reject(request.error);
  });
}

export async function getOfflinePapers({ includeDeleted = false } = {}) {
  const db = await openDB();
  return new Promise((resolve, reject) => {
    const request = db.transaction('papers', 'readonly').objectStore('papers').getAll();
    request.onsuccess = () => resolve((request.result || []).filter((paper) => paper.owner_key === ownerKey() && (includeDeleted || !paper.deleted)));
    request.onerror = () => reject(request.error);
  });
}

export async function clearOfflineStorage() {
  const db = await openDB();
  return new Promise((resolve, reject) => {
    const tx = db.transaction(['questions', 'papers', 'meta'], 'readwrite');
    tx.objectStore('questions').clear();
    tx.objectStore('papers').clear();
    tx.objectStore('meta').clear();
    tx.oncomplete = () => {
      localStorage.removeItem('gs_device_key');
      localStorage.removeItem('gs_exam_header');
      resolve();
    };
    tx.onerror = () => reject(tx.error);
    tx.onabort = () => reject(tx.error || new Error('Could not clear offline storage'));
  });
}

export async function mergeRestoredPapers(serverPapers) {
  const localPapers = await getOfflinePapers({ includeDeleted: true });
  const localByServerId = new Map(localPapers.filter((paper) => paper.id).map((paper) => [String(paper.id), paper]));
  const localByOfflineId = new Map(localPapers.map((paper) => [paper.offline_id, paper]));
  let added = 0;
  let updated = 0;
  let skipped = 0;

  for (const paper of serverPapers || []) {
    const offlineId = `server:paper:${paper.id}`;
    const existing = localByServerId.get(String(paper.id)) || localByOfflineId.get(offlineId);
    if (existing && existing.backup_state !== 'backed_up') {
      skipped += 1;
      continue;
    }

    const serverTime = Date.parse(paper.updated_at || paper.created_at || '') || 0;
    const localTime = Date.parse(existing?.updated_at || existing?.created_at || '') || 0;
    if (existing && localTime > serverTime) {
      skipped += 1;
      continue;
    }

    await saveOfflinePaper({
      ...paper,
      offline_id: existing?.offline_id || offlineId,
      backup_state: 'backed_up',
    });
    if (existing) updated += 1;
    else added += 1;
  }

  return { added, updated, skipped };
}

export async function deleteOfflinePaper(id) {
  const paper = await getOfflinePaper(id);
  if (!paper) return;
  paper.deleted = true;
  paper.backup_state = 'pending';
  paper.updated_at = new Date().toISOString();
  const db = await openDB();
  return new Promise((resolve, reject) => {
    const tx = db.transaction('papers', 'readwrite');
    tx.objectStore('papers').put(paper);
    tx.oncomplete = resolve;
    tx.onerror = () => reject(tx.error);
  });
}

export { ownerKey };
