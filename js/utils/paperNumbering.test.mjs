import assert from 'node:assert/strict';
import test from 'node:test';
import { getSectionQuestionLabels } from './paperNumbering.js';

const questions = new Map([
  ['1', { id: 1, type: 'mcq' }],
  ['2', { id: 2, type: 'mcq' }],
  ['3', { id: 3, type: 'theory' }],
  ['4', { id: 4, type: 'theory' }],
  ['5', { id: 5, type: 'mcq' }],
]);

test('sections can continue numbering from the previous section', () => {
  const sections = [
    { question_ids: ['1', '2'] },
    { numbering_mode: 'continue', question_ids: ['3', '4'] },
  ];
  const labels = getSectionQuestionLabels(sections, questions, [
    { question_id: '3', level: 0 },
    { question_id: '4', level: 1 },
  ]);

  assert.deepEqual([...labels], [['1', '1'], ['2', '2'], ['3', '3(a)'], ['4', '3(b)']]);
});

test('fresh numbering resets a section and subsequent continuation follows its new sequence', () => {
  const sections = [
    { question_ids: ['1', '2'] },
    { numbering_mode: 'restart', question_ids: ['3'] },
    { numbering_mode: 'continue', question_ids: ['5'] },
  ];
  const labels = getSectionQuestionLabels(sections, questions);

  assert.deepEqual([...labels], [['1', '1'], ['2', '2'], ['3', '1'], ['5', '2']]);
});
