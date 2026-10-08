import { api, getUser, saveUser, storageUrl } from '../utils/api.js';
import { toast } from '../utils/toast.js';
import { confirmModal } from '../utils/modal.js';
import { MAX_QUESTIONS_PER_REQUEST, normalizeDiagramSpec } from './aiQuestionContract.js';
import { requireAuthentication } from '../utils/authGate.js';
import { saveOfflineQuestion } from '../utils/db.js';
import { loadMetaData } from '../utils/meta.js';

const esc = (value) => String(value ?? '')
  .replace(/&/g, '&amp;')
  .replace(/</g, '&lt;')
  .replace(/>/g, '&gt;')
  .replace(/"/g, '&quot;');

const aiContentTypes = (subjectName) => {
  if (/literature/i.test(subjectName || '')) return [['standard', 'Standard'], ['prose', 'Prose'], ['poetry', 'Poetry'], ['drama', 'Drama'], ['comprehension', 'Comprehension']];
  if (/english|language|french|hausa|yoruba|igbo/i.test(subjectName || '')) return [['standard', 'Standard'], ['comprehension', 'Comprehension'], ['passage', 'Passage']];
  if (/biology|basic science|physics|chemistry|agricultural/i.test(subjectName || '')) return [['standard', 'Standard'], ['practical', 'Practical']];
  if (/economics|commerce|government|civic|social studies|history|geography|business/i.test(subjectName || '')) return [['standard', 'Standard'], ['case_study', 'Case study'], ['data_interpretation', 'Data interpretation']];
  return [['standard', 'Standard']];
};

export function openAiQuestionFlow(meta, onSaved, existingQuestions = []) {
  if (!localStorage.getItem('gs_token')) {
    requireAuthentication('Ask AI', async () => {
      const freshMeta = await loadMetaData();
      return openAiQuestionFlow(freshMeta, onSaved, existingQuestions);
    });
    return;
  }
  if (!navigator.onLine) {
    toast('AI question generation requires an internet connection. Enter questions manually while offline.', 'warn');
    return;
  }
  const overlay = document.createElement('div');
  let requestController = null;
  overlay.className = 'fixed inset-0 z-50 bg-black/40 flex items-end sm:items-center justify-center p-0 sm:p-4';
  overlay.innerHTML = `
    <div class="bg-white dark:bg-gray-900 w-full sm:max-w-xl max-h-[94vh] overflow-y-auto rounded-t-2xl sm:rounded-2xl shadow-xl">
      <div class="sticky top-0 z-10 bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 px-4 py-3 flex items-center justify-between">
        <div><h3 class="font-semibold">Ask AI</h3><p class="text-xs text-gray-500">Create curriculum-focused questions for review</p></div>
        <button data-ai-close class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800" aria-label="Close">✕</button>
      </div>
      <form data-ai-form class="p-4 space-y-4">
        <div class="rounded-xl bg-primary-50 text-primary-900 dark:bg-primary-100 dark:text-gray-900 border border-primary-200 dark:border-primary-300 p-3 text-xs font-medium">
          Maximum ${MAX_QUESTIONS_PER_REQUEST} questions per request. Daily allowance: 10 requests. Generated questions are never added until you review and accept them.
        </div>
        <div class="grid grid-cols-2 gap-3">
          <label class="text-xs font-medium">Subject<select name="subject_id" required class="mt-1 w-full px-2 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm"><option value="">Select subject</option>${meta.subjects.map((x) => `<option value="${x.id}">${esc(x.name)}</option>`).join('')}</select></label>
          <label class="text-xs font-medium">Class<select name="class_id" required class="mt-1 w-full px-2 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm"><option value="">Select class</option>${meta.classes.map((x) => `<option value="${x.id}">${esc(x.name)}</option>`).join('')}</select></label>
          <label class="text-xs font-medium">Term<select name="term_id" required class="mt-1 w-full px-2 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm"><option value="">Select term</option>${meta.terms.map((x) => `<option value="${x.id}">${esc(x.name)}</option>`).join('')}</select></label>
          <label class="text-xs font-medium">Number (1–${MAX_QUESTIONS_PER_REQUEST})<input name="count" type="number" min="1" max="${MAX_QUESTIONS_PER_REQUEST}" value="5" required class="mt-1 w-full px-2 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm" /></label>
          <fieldset class="text-xs font-medium"><legend>Question types</legend><div class="mt-1 flex flex-wrap gap-3"><label class="flex items-center gap-1.5 font-normal"><input type="checkbox" name="types" value="mcq" checked /> MCQ</label><label class="flex items-center gap-1.5 font-normal"><input type="checkbox" name="types" value="fill" checked /> Fill in the gap</label><label class="flex items-center gap-1.5 font-normal"><input type="checkbox" name="types" value="theory" checked /> Theory</label></div></fieldset>
          <label class="text-xs font-medium">Content type<select name="content_type" class="mt-1 w-full px-2 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm"><option value="standard">Standard</option></select></label>
          <label class="text-xs font-medium">Difficulty<select name="difficulty" class="mt-1 w-full px-2 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm"><option value="easy">Easy</option><option value="medium" selected>Medium</option><option value="hard">Hard</option><option value="mixed">Mixed</option></select></label>
        </div>
        <label class="block text-xs font-medium">Topics and educational focus <span class="font-normal text-gray-400">(optional, max 500 characters)</span><textarea name="focus" maxlength="500" rows="3" placeholder="Enter topics separated by commas, e.g. Fractions, indices, number line, etc" class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm"></textarea></label>
        <div data-ai-error class="hidden text-sm text-red-600 bg-red-50 dark:bg-red-900/20 rounded-lg px-3 py-2"></div>
        <div class="flex gap-2"><button data-ai-clear type="button" class="flex-1 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-sm">Clear</button><button data-ai-generate type="submit" class="flex-1 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold text-sm">Generate questions</button></div>
      </form>
      <div data-ai-review class="hidden p-4 space-y-3"></div>
    </div>`;
  document.body.appendChild(overlay);

  const close = () => { requestController?.abort(); overlay.remove(); };
  overlay.querySelector('[data-ai-close]').onclick = close;
  overlay.querySelector('[data-ai-clear]').onclick = () => overlay.querySelector('[data-ai-form]').reset();
  const subjectSelect = overlay.querySelector('[name="subject_id"]');
  const contentTypeSelect = overlay.querySelector('[name="content_type"]');
  const updateContentTypes = () => {
    contentTypeSelect.innerHTML = aiContentTypes(subjectSelect.selectedOptions[0]?.textContent || '')
      .map(([value, label]) => `<option value="${value}">${label}</option>`).join('');
  };
  subjectSelect.addEventListener('change', updateContentTypes);
  updateContentTypes();

  overlay.querySelector('[data-ai-form]').addEventListener('submit', async (event) => {
    event.preventDefault();
    const form = event.currentTarget;
    const button = overlay.querySelector('[data-ai-generate]');
    const error = overlay.querySelector('[data-ai-error]');
    const values = Object.fromEntries(new FormData(form).entries());
    values.topic = values.focus || '';
    const selectedTypes = [...form.querySelectorAll('[name="types"]:checked')].map((input) => input.value);
    values.type = selectedTypes.length ? selectedTypes : 'mixed';
    values.count = Number(values.count);
    values.subject_name = subjectSelect.selectedOptions[0]?.textContent?.trim() || '';
    values.class_name = form.elements.class_id?.selectedOptions[0]?.textContent?.trim() || '';
    values.term_name = form.elements.term_id?.selectedOptions[0]?.textContent?.trim() || '';
    button.disabled = true;
    button.textContent = 'Generating…';
    error.classList.add('hidden');
    requestController = new AbortController();
    try {
      const result = await api('/ai/questions/generate', { method: 'POST', body: JSON.stringify(values), signal: requestController.signal, timeoutMs: 45000 });
      if (result.data?.credits_left !== undefined) {
        const user = getUser();
        if (user) {
          saveUser({ ...user, credits: result.data.credits_left });
        }
        const creditsValue = document.getElementById('dash-credits') || document.getElementById('credits-value');
        if (creditsValue) {
          creditsValue.textContent = result.data.credits_left === -1 ? 'Unlimited' : result.data.credits_left;
        }
        toast(`Generated. ${result.data.cost || 35} credits used.`, 'success');
      }
      renderReview(overlay, result.data?.questions || [], result.data?.passage || null, values, onSaved, close, existingQuestions);
    } catch (err) {
      if (err.name === 'AbortError') return;
      const message = err.status === 0 && err.cause?.name === 'AbortError'
        ? 'The server took too long to respond. Please wait a moment, then try again.'
        : (err.message || 'Could not generate questions');
      error.innerHTML = `${esc(message)} <button type="button" data-ai-retry class="font-semibold underline ml-1">Retry</button>`;
      error.classList.remove('hidden');
      error.querySelector('[data-ai-retry]')?.addEventListener('click', () => form.requestSubmit());
    } finally {
      button.disabled = false;
      button.textContent = 'Generate questions';
    }
  });
}

function renderReview(overlay, questions, passage, values, onSaved, close, existingQuestions) {
  const form = overlay.querySelector('[data-ai-form]');
  const review = overlay.querySelector('[data-ai-review]');
  form.classList.add('hidden');
  review.classList.remove('hidden');
  review.innerHTML = `
    <div class="flex items-center justify-between"><div><h4 class="font-semibold">Review generated questions</h4><p class="text-xs text-gray-500">Review against the Nigerian curriculum before accepting.</p></div><span data-ai-count class="text-xs font-medium text-primary-600">0 / ${questions.length} accepted</span></div>
    <p class="text-xs text-amber-700 bg-amber-50 dark:bg-amber-900/20 rounded-lg px-3 py-2">AI can make mistakes. Check each question, answer, and difficulty before adding it.</p>
    ${passage ? `<div class="rounded-xl border border-primary-100 dark:border-primary-800 bg-primary-50/60 dark:bg-primary-900/20 p-3 space-y-2"><label class="block text-xs font-semibold">Generated ${esc(values.content_type || 'content')} passage</label><input data-ai-passage-title value="${esc(passage.title)}" class="w-full px-2 py-2 rounded-lg border border-primary-100 dark:border-primary-800 bg-white dark:bg-gray-800 text-sm" /><textarea data-ai-passage-body rows="7" class="w-full px-2 py-2 rounded-lg border border-primary-100 dark:border-primary-800 bg-white dark:bg-gray-800 text-sm">${esc(passage.body)}</textarea><p class="text-[10px] text-gray-500">Review this passage before accepting its questions.</p></div>` : ''}
    <div data-ai-items class="space-y-3"></div>
    <div class="flex gap-2 pt-2"><button data-ai-copy-all type="button" class="flex-1 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-sm">Copy all</button><button data-ai-back type="button" class="flex-1 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-sm">Back</button><button data-ai-accept-all type="button" class="flex-1 py-2 rounded-xl bg-primary-600 text-white text-sm font-semibold">Accept selected</button></div>`;

  const items = review.querySelector('[data-ai-items]');
  questions.forEach((question, index) => {
    const diagram = normalizeDiagramSpec(question.diagram_spec || question.diagram_request);
    const card = document.createElement('div');
    card.className = 'rounded-xl border border-gray-200 dark:border-gray-700 p-3 space-y-2';
    card.dataset.index = index;
    card.innerHTML = `<div class="flex items-center justify-between"><label class="flex items-center gap-2 text-xs font-semibold"><input type="checkbox" data-select checked />Question ${index + 1}</label><div class="flex items-center gap-2"><button type="button" data-up class="text-xs text-gray-500" title="Move up">↑</button><button type="button" data-down class="text-xs text-gray-500" title="Move down">↓</button><button type="button" data-copy class="text-xs text-primary-600">Copy</button><button type="button" data-remove class="text-xs text-red-500">Remove</button></div></div>
      <textarea data-body rows="3" class="w-full px-2 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm">${esc(question.body)}</textarea>
      ${diagram ? `<div data-diagram-preview class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-2 min-h-40 flex items-center justify-center">${diagram.svg ? diagram.svg : `<span class="text-xs text-gray-600">No diagram preview available.</span>`}</div>` : ''}
      ${(question.options || []).map((option) => `<label class="flex gap-2 items-center text-xs"><span class="font-semibold w-4">${esc(option.key)}</span><input data-option="${esc(option.key)}" value="${esc(option.text)}" class="flex-1 px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800" /></label>`).join('')}
      <div class="flex gap-2"><input data-answer value="${esc(question.answer)}" placeholder="Answer" class="flex-1 px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-xs" /><button type="button" data-accept class="px-3 py-1.5 rounded-lg bg-primary-50 text-primary-700 text-xs font-semibold">Accept</button></div>`;
    items.appendChild(card);
  });

  const accepted = new Set();
  let passageId = null;
  const updateCount = () => { review.querySelector('[data-ai-count]').textContent = `${accepted.size} / ${questions.length} accepted`; };
  const normalize = (text) => String(text || '').toLowerCase().replace(/[^a-z0-9]+/g, ' ').trim();
  const saveQuestion = async (card) => {
    const index = Number(card.dataset.index);
    const question = questions[index];
    question.body = card.querySelector('[data-body]').value.trim();
    question.answer = card.querySelector('[data-answer]').value.trim();
    question.options = [...card.querySelectorAll('[data-option]')].map((input) => ({ key: input.dataset.option, text: input.value.trim() })).filter((x) => x.text);
    if (!question.body || (question.type === 'mcq' && question.options.length < 2)) throw new Error(`Question ${index + 1} needs a body and at least two options`);
    const duplicate = existingQuestions.some((item) => normalize(item.body) === normalize(question.body));
    if (duplicate) throw new Error(`Question ${index + 1} already exists in your question bank`);

    const payload = {
      ...question,
      diagram_spec: question.diagram_spec || question.diagram_request || null,
      source: 'ai',
      ai_generated: true,
      content_type: values.content_type || 'standard',
      passage_id: null,
      subject_id: values.subject_id,
      class_id: values.class_id,
      term_id: values.term_id,
    };

    await saveOfflineQuestion(payload);

    accepted.add(index);
    card.classList.add('opacity-50');
    card.querySelector('[data-accept]').disabled = true;
    updateCount();
  };

  items.querySelectorAll('[data-accept]').forEach((button) => button.addEventListener('click', async () => {
    if (!await confirmModal({ title: 'Add question?', message: 'Add this reviewed question to your question bank?', confirmLabel: 'Add question' })) return;
    try { await saveQuestion(button.closest('[data-index]')); toast('Question added to your bank', 'success'); } catch (err) { toast(err.message, 'error'); }
  }));
  items.querySelectorAll('[data-remove]').forEach((button) => button.addEventListener('click', () => button.closest('[data-index]').remove()));
  items.querySelectorAll('[data-up]').forEach((button) => button.addEventListener('click', () => {
    const card = button.closest('[data-index]');
    if (card.previousElementSibling) items.insertBefore(card, card.previousElementSibling);
  }));
  items.querySelectorAll('[data-down]').forEach((button) => button.addEventListener('click', () => {
    const card = button.closest('[data-index]');
    if (card.nextElementSibling) items.insertBefore(card.nextElementSibling, card);
  }));
  items.querySelectorAll('[data-copy]').forEach((button) => button.addEventListener('click', async () => {
    const card = button.closest('[data-index]');
    const options = [...card.querySelectorAll('[data-option]')].map((input) => `${input.dataset.option}. ${input.value}`).join('\n');
    await navigator.clipboard.writeText(`${card.querySelector('[data-body]').value}\n${options}\nAnswer: ${card.querySelector('[data-answer]').value}`);
    toast('Question copied', 'success');
  }));
  review.querySelector('[data-ai-back]').onclick = () => { review.classList.add('hidden'); form.classList.remove('hidden'); };
  review.querySelector('[data-ai-copy-all]').onclick = async () => {
    const text = [...items.querySelectorAll('[data-index]')].map((card, index) => {
      const options = [...card.querySelectorAll('[data-option]')].map((input) => `${input.dataset.option}. ${input.value}`).join('\n');
      return `${index + 1}. ${card.querySelector('[data-body]').value}\n${options}\nAnswer: ${card.querySelector('[data-answer]').value}`;
    }).join('\n\n');
    await navigator.clipboard.writeText(text);
    toast('Questions copied', 'success');
  };
  review.querySelector('[data-ai-accept-all]').onclick = async () => {
    const selectedCards = [...items.querySelectorAll('[data-index]')].filter((card) => card.querySelector('[data-select]')?.checked && !accepted.has(Number(card.dataset.index)));
    if (!selectedCards.length) { toast('Select at least one question', 'error'); return; }
    if (!await confirmModal({ title: 'Add selected questions?', message: `Add ${selectedCards.length} reviewed question${selectedCards.length === 1 ? '' : 's'} to your question bank?`, confirmLabel: 'Add questions' })) return;
    const failed = [];
    for (const card of selectedCards) {
      try { await saveQuestion(card); } catch (err) { failed.push(err.message); }
    }
    if (accepted.size) {
      await onSaved();
      if (failed.length) toast(`${accepted.size} added, ${failed.length} failed: ${failed[0]}`, 'error');
      else if (accepted.size === questions.length) { toast('All selected questions added to your bank', 'success'); close(); }
    }
  };
}
