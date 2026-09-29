import assert from 'node:assert/strict';
import test from 'node:test';
import {
  DAILY_REQUEST_LIMIT,
  MAX_QUESTIONS_PER_REQUEST,
  isValidQuestionCount,
  normalizeDiagramRequest,
} from './aiQuestionContract.js';

test('AI limits match the backend policy', () => {
  assert.equal(MAX_QUESTIONS_PER_REQUEST, 10);
  assert.equal(DAILY_REQUEST_LIMIT, 10);
  assert.equal(isValidQuestionCount(1), true);
  assert.equal(isValidQuestionCount(10), true);
  assert.equal(isValidQuestionCount(11), false);
});

test('diagram instructions are normalized safely', () => {
  assert.deepEqual(normalizeDiagramRequest({
    description: '  Draw a leaf  ',
    labels: [' blade ', '', '<unsafe>'],
  }), {
    description: 'Draw a leaf',
    labels: ['blade', '<unsafe>'],
      svg: '',
  });
  assert.equal(normalizeDiagramRequest({
    description: 'Draw a circle',
    labels: [],
    svg: '<svg viewBox="0 0 20 20"><script>alert(1)</script><circle cx="10" cy="10" r="8" /></svg>',
  }).svg.includes('<script>'), false);
  assert.equal(normalizeDiagramRequest({ description: ' ' }), null);
});
