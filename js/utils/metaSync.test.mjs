import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import test from 'node:test';
import vm from 'node:vm';

async function setup({ cached = {}, questions = [], remote = {}, failRepair = false } = {}) {
  const cache = new Map(Object.entries(structuredClone(cached)));
  const localQuestions = structuredClone(questions);
  const calls = [];
  const subjects = structuredClone(remote.subjects || []);
  const classes = structuredClone(remote.classes || []);
  const terms = structuredClone(remote.terms || [{ id: 1, name: '1st Term' }]);
  const context = vm.createContext({ navigator: { onLine: true }, console });
  const modules = {
    './api.js': {
      getUser: () => ({ id: 7 }),
      api: async (endpoint, options = {}) => {
        calls.push({ endpoint, ...options });
        if (endpoint === '/backup/question-references') {
          if (failRepair) throw new Error('Repair unavailable');
          return { data: { processed: JSON.parse(options.body).questions.length } };
        }
        const rows = endpoint === '/meta/subjects' ? subjects : endpoint === '/meta/classes' ? classes : terms;
        if (options.method === 'POST') {
          const row = { ...JSON.parse(options.body), id: rows.length + 20 };
          rows.push(row);
          return { data: row };
        }
        return { data: structuredClone(rows) };
      },
    },
    './db.js': {
      getCachedMeta: async (key) => cache.get(key),
      cacheMeta: async (key, value) => cache.set(key, structuredClone(value)),
      getOfflineQuestions: async () => structuredClone(localQuestions),
      remapOfflineMetadata: async (mappings) => {
        for (const question of localQuestions) {
          for (const [field, mapping] of Object.entries(mappings)) {
            if (mapping.has(String(question[field] ?? ''))) question[field] = mapping.get(String(question[field]));
          }
        }
      },
    },
  };
  const source = await readFile(new URL('./meta.js', import.meta.url), 'utf8');
  const module = new vm.SourceTextModule(source, { context });
  await module.link((specifier) => {
    const exports = modules[specifier];
    return new vm.SyntheticModule(Object.keys(exports), function () {
      for (const [key, value] of Object.entries(exports)) this.setExport(key, value);
    }, { context });
  });
  await module.evaluate();
  return { load: module.namespace.loadMetaData, cache, localQuestions, calls };
}

test('a new device loads account categories and server term IDs', async () => {
  const app = await setup({ remote: {
    subjects: [{ id: 31, name: 'Maths' }],
    classes: [{ id: 42, name: 'JSS 1' }],
    terms: [{ id: 17, name: '1st Term' }],
  } });
  const meta = await app.load({ refresh: true });
  assert.equal(meta.subjects[0].id, 31);
  assert.equal(meta.classes[0].id, 42);
  assert.equal(meta.terms[0].id, 17);
  assert.equal(meta.terms.length, 1);
});

test('sync maps local categories, repairs backed-up questions and remaps pending ones', async () => {
  const app = await setup({
    cached: { 'all-v2:7': {
      subjects: [{ id: 'local-s', name: 'Maths', local_only: true }],
      classes: [{ id: 'local-c', name: 'JSS 1', local_only: true }],
      terms: [{ id: 1, name: '1st Term' }],
    } },
    questions: [
      { id: 9, offline_id: 'q1', backup_state: 'backed_up', subject_id: 'local-s', class_id: 'local-c', term_id: 1 },
      { offline_id: 'q2', backup_state: 'pending', subject_id: 'local-s', class_id: 'local-c', term_id: 1 },
    ],
    remote: { classes: [{ id: 42, name: 'JSS 1' }], terms: [{ id: 17, name: '1st Term' }] },
  });
  const meta = await app.load({ refresh: true });
  const repair = app.calls.find((call) => call.endpoint === '/backup/question-references');
  assert.deepEqual(JSON.parse(repair.body).questions, [
    { id: 9, offline_id: 'q1', subject_id: 20, class_id: 42, term_id: 17 },
  ]);
  assert.equal(app.localQuestions[1].subject_id, 20);
  assert.equal(app.localQuestions[1].class_id, 42);
  assert.equal(app.localQuestions[1].term_id, 17);
  assert.equal(meta.subjects[0].id, 20);
  assert.equal(app.calls.filter((call) => call.endpoint === '/meta/classes' && call.method === 'POST').length, 0);
});

test('a failed repair retains local references and reports the error', async () => {
  const app = await setup({
    cached: { 'all-v2:7': { subjects: [{ id: 'local-s', name: 'Maths', local_only: true }] } },
    questions: [{ id: 9, offline_id: 'q1', backup_state: 'backed_up', subject_id: 'local-s' }],
    remote: { subjects: [{ id: 31, name: 'Maths' }] },
    failRepair: true,
  });
  const statuses = [];
  await app.load({ refresh: true, onSyncStatus: (status) => statuses.push(status) });
  assert.equal(app.localQuestions[0].subject_id, 'local-s');
  assert.equal(app.cache.get('all-v2:7').subjects[0].id, 'local-s');
  assert.equal(statuses.at(-1).state, 'error');
  assert.equal(statuses.at(-1).error.message, 'Repair unavailable');
});

test('all local IDs with the same name are mapped to the account record', async () => {
  const app = await setup({
    cached: {
      'all-v2:anonymous': { subjects: [{ id: 'local-a', name: 'Maths', local_only: true }] },
      'all-v2:7': { subjects: [{ id: 'local-b', name: 'Maths', local_only: true }] },
    },
    questions: [
      { offline_id: 'a', backup_state: 'pending', subject_id: 'local-a' },
      { offline_id: 'b', backup_state: 'pending', subject_id: 'local-b' },
    ],
    remote: { subjects: [{ id: 31, name: 'Maths' }] },
  });
  await app.load({ refresh: true });
  assert.equal(app.localQuestions[0].subject_id, 31);
  assert.equal(app.localQuestions[1].subject_id, 31);
});
