import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import test from 'node:test';
import vm from 'node:vm';

function fakeIndexedDB(stores) {
  const db = {
    transaction() {
      let pending = 0;
      const tx = {
        objectStore(name) {
          const records = stores[name];
          return {
            get(key) {
              const request = {};
              pending++;
              queueMicrotask(() => {
                request.result = structuredClone(records.get(key));
                request.onsuccess();
                if (!--pending) queueMicrotask(() => tx.oncomplete?.());
              });
              return request;
            },
            put(record) {
              pending++;
              queueMicrotask(() => {
                records.set(record.offline_id, structuredClone(record));
                if (!--pending) queueMicrotask(() => tx.oncomplete?.());
              });
            },
            openCursor() {
              const request = {};
              const entries = [...records.entries()];
              let index = 0;
              pending++;
              const advance = () => queueMicrotask(() => {
                if (index === entries.length) {
                  request.result = null;
                  request.onsuccess();
                  if (!--pending) queueMicrotask(() => tx.oncomplete?.());
                  return;
                }
                const [key, value] = entries[index++];
                request.result = {
                  value: structuredClone(value),
                  update: (record) => records.set(key, structuredClone(record)),
                  continue: advance,
                };
                request.onsuccess();
              });
              advance();
              return request;
            },
          };
        },
      };
      return tx;
    },
  };
  return {
    open() {
      const request = {};
      queueMicrotask(() => {
        request.result = db;
        request.onsuccess();
      });
      return request;
    },
  };
}

test('local references survive server caching and are remapped without touching other devices', async () => {
  const questions = new Map([
    ['q1', { offline_id: 'q1', id: 1, owner_key: 'device:a', backup_state: 'backed_up', subject_id: 'local-s', class_id: 'local-c', term_id: 1 }],
    ['q2', { offline_id: 'q2', owner_key: 'device:b', backup_state: 'pending', subject_id: 'local-s' }],
  ]);
  const passages = new Map([
    ['p1', { offline_id: 'p1', owner_key: 'device:a', backup_state: 'backed_up', subject_id: 'local-s' }],
  ]);
  const context = vm.createContext({
    indexedDB: fakeIndexedDB({ questions, passages }),
    localStorage: { getItem: () => 'device:a' },
    structuredClone,
  });

  const source = await readFile(new URL('./db.js', import.meta.url), 'utf8');
  const module = new vm.SourceTextModule(source, { context });
  await module.link(() => {});
  await module.evaluate();
  await module.namespace.cacheQuestion({ id: 1, offline_id: 'q1', subject_id: null, class_id: null, term_id: null });
  assert.equal(questions.get('q1').subject_id, 'local-s');
  assert.equal(questions.get('q1').term_id, 1);
  await module.namespace.remapOfflineMetadata({
    subject_id: new Map([['local-s', 10]]),
    class_id: new Map([['local-c', 20]]),
    term_id: new Map([['1', 30]]),
  });
  assert.equal(questions.get('q1').subject_id, 10);
  assert.equal(questions.get('q1').class_id, 20);
  assert.equal(questions.get('q1').term_id, 30);
  assert.equal(questions.get('q2').subject_id, 'local-s');
  assert.equal(passages.get('p1').subject_id, 10);
  assert.equal(passages.get('p1').backup_state, 'pending');
});

test('caching a server question preserves missing numeric categories for explicit recovery', async () => {
  const questions = new Map([
    ['q1', { offline_id: 'q1', id: 1, owner_key: 'device:a', backup_state: 'backed_up', subject_id: 10, class_id: 20, term_id: 30 }],
  ]);
  const context = vm.createContext({
    indexedDB: fakeIndexedDB({ questions, passages: new Map() }),
    localStorage: { getItem: () => 'device:a' },
    structuredClone,
  });
  const source = await readFile(new URL('./db.js', import.meta.url), 'utf8');
  const module = new vm.SourceTextModule(source, { context });
  await module.link(() => {});
  await module.evaluate();
  await module.namespace.cacheQuestion({ id: 1, offline_id: 'q1', subject_id: null, class_id: null, term_id: null });
  assert.deepEqual(questions.get('q1').category_recovery, { subject_id: 10, class_id: 20, term_id: 30 });
  assert.equal(questions.get('q1').subject_id, null);
});
