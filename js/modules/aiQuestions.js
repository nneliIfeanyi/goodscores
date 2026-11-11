import { apiStream, getUser, saveUser, storageUrl } from '../utils/api.js';
import { toast } from '../utils/toast.js';
import { confirmModal } from '../utils/modal.js';
import { MAX_QUESTIONS_PER_REQUEST, normalizeDiagramSpec } from './aiQuestionContract.js';
import { requireAuthentication } from '../utils/authGate.js';
import { saveQuestion as saveQuestionToBank } from '../utils/backup.js?v=28';
import { loadMetaData } from '../utils/meta.js';
import { mathHtml } from '../utils/math.js';

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
  let progressTimer = null;
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
        <div data-ai-progress class="ai-generation-progress hidden" role="status" aria-live="polite">
          <div class="ai-generation-progress__heading"><span data-ai-progress-label>Connecting to OpenAI…</span><span data-ai-progress-percent>0%</span></div>
          <div data-ai-progress-track class="ai-generation-progress__track" data-indeterminate="false" role="progressbar" aria-label="Question generation progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" aria-valuetext="0 questions received">
            <div data-ai-progress-bar class="ai-generation-progress__bar"></div>
          </div>
          <div class="ai-generation-progress__details"><span data-ai-progress-count>0 questions received</span><span data-ai-progress-elapsed>Elapsed 0:00</span></div>
          <p class="ai-generation-progress__note">Progress counts completed questions; it cannot predict exact time remaining.</p>
        </div>
        <div class="flex gap-2"><button data-ai-clear type="button" class="flex-1 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-sm">Clear</button><button data-ai-generate type="submit" class="flex-1 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold text-sm">Generate questions</button></div>
      </form>
      <div data-ai-review class="hidden p-4 space-y-3"></div>
    </div>`;
  document.body.appendChild(overlay);

  const close = () => {
    if (progressTimer !== null) {
      clearInterval(progressTimer);
      progressTimer = null;
    }
    requestController?.abort();
    overlay.remove();
  };
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
    const progressPanel = overlay.querySelector('[data-ai-progress]');
    const progressTrack = overlay.querySelector('[data-ai-progress-track]');
    const progressBar = overlay.querySelector('[data-ai-progress-bar]');
    const progressLabel = overlay.querySelector('[data-ai-progress-label]');
    const progressPercent = overlay.querySelector('[data-ai-progress-percent]');
    const progressCount = overlay.querySelector('[data-ai-progress-count]');
    const progressElapsed = overlay.querySelector('[data-ai-progress-elapsed]');
    const values = Object.fromEntries(new FormData(form).entries());
    values.topic = values.focus || '';
    const selectedTypes = [...form.querySelectorAll('[name="types"]:checked')].map((input) => input.value);
    values.type = selectedTypes.length ? selectedTypes : 'mixed';
    values.count = Number(values.count);
    values.subject_name = subjectSelect.selectedOptions[0]?.textContent?.trim() || '';
    values.class_name = form.elements.class_id?.selectedOptions[0]?.textContent?.trim() || '';
    values.term_name = form.elements.term_id?.selectedOptions[0]?.textContent?.trim() || '';
    const startedAt = Date.now();
    const updateProgress = (update) => {
      const total = Math.max(1, Number(update.questions_total) || values.count);
      const completed = Math.min(total, Math.max(0, Number(update.questions_completed) || 0));
      const percent = Math.round((completed / total) * 100);
      const phase = update.phase || '';
      const labels = {
        connecting: 'Contacting OpenAI…',
        generating: completed > 0 ? 'Generating questions…' : 'Waiting for the first question…',
        validating: 'Checking the completed response…',
        finishing: 'Finishing up…',
        complete: 'Questions ready',
      };
      const indeterminate = completed === 0 && ['connecting', 'generating'].includes(phase);

      progressLabel.textContent = labels[phase] || 'Generating questions…';
      progressPercent.textContent = `${percent}%`;
      progressCount.textContent = `${completed} of ${total} questions received`;
      progressBar.style.width = `${indeterminate ? 35 : percent}%`;
      progressTrack.dataset.indeterminate = String(indeterminate);
      progressTrack.setAttribute('aria-valuenow', String(percent));
      progressTrack.setAttribute('aria-valuetext', `${completed} of ${total} questions received`);
    };
    const updateElapsed = () => {
      const seconds = Math.floor((Date.now() - startedAt) / 1000);
      progressElapsed.textContent = `Elapsed ${Math.floor(seconds / 60)}:${String(seconds % 60).padStart(2, '0')}`;
    };

    button.disabled = true;
    button.textContent = 'Generating…';
    error.classList.add('hidden');
    progressPanel.classList.remove('hidden');
    requestController = new AbortController();
    updateProgress({ phase: 'connecting', questions_completed: 0, questions_total: values.count });
    updateElapsed();
    progressTimer = window.setInterval(updateElapsed, 1000);
    try {
      const result = await apiStream('/ai/questions/generate?stream=1', {
        method: 'POST',
        body: JSON.stringify(values),
        signal: requestController.signal,
        timeoutMs: 75000,
      }, updateProgress);
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
      progressPanel.classList.add('hidden');
      error.innerHTML = `${esc(message)} <button type="button" data-ai-retry class="font-semibold underline ml-1">Retry</button>`;
      error.classList.remove('hidden');
      error.querySelector('[data-ai-retry]')?.addEventListener('click', () => form.requestSubmit());
    } finally {
      if (progressTimer !== null) {
        clearInterval(progressTimer);
        progressTimer = null;
      }
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
    ${passage ? `<div class="rounded-xl border border-primary-100 dark:border-primary-800 bg-primary-50/60 dark:bg-primary-900/20 p-3 space-y-2"><label class="block text-xs font-semibold">Generated ${esc(values.content_type || 'content')} passage</label><input data-ai-passage-title value="${esc(passage.title)}" class="w-full px-2 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm" /><textarea data-ai-passage-body rows="7" class="w-full px-2 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm">${esc(passage.body)}</textarea><p data-ai-passage-note class="text-[10px] text-gray-500">Review this passage before accepting its questions.</p></div>` : ''}
    <div data-ai-items class="space-y-3"></div>
    <div class="flex gap-2 pt-2"><button data-ai-copy-all type="button" class="flex-1 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-sm">Copy all</button><button data-ai-back type="button" class="flex-1 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-sm">Back</button><button data-ai-accept-all type="button" class="flex-1 py-2 rounded-xl bg-primary-600 text-white text-sm font-semibold">Accept selected</button></div>`;

  const items = review.querySelector('[data-ai-items]');
  const passageTitleInput = review.querySelector('[data-ai-passage-title]');
  const passageBodyInput = review.querySelector('[data-ai-passage-body]');
  const passageNote = review.querySelector('[data-ai-passage-note]');
  questions.forEach((question, index) => {
    const diagram = normalizeDiagramSpec(question.diagram_spec || question.diagram_request);
    const card = document.createElement('div');
    card.className = 'rounded-xl border border-gray-200 dark:border-gray-700 p-3 space-y-2';
    card.dataset.index = index;
    card.innerHTML = `<div class="flex items-center justify-between"><label class="flex items-center gap-2 text-xs font-semibold"><input type="checkbox" data-select checked />Question ${index + 1}</label><div class="flex items-center gap-2"><button type="button" data-up class="text-xs text-gray-500" title="Move up">↑</button><button type="button" data-down class="text-xs text-gray-500" title="Move down">↓</button><button type="button" data-copy class="text-xs text-primary-600">Copy</button><button type="button" data-remove class="text-xs text-red-500">Remove</button></div></div>
      <div data-math-preview class="rounded-lg bg-gray-50 dark:bg-gray-800/60 p-2 text-sm">${mathHtml(question.body)}</div>
      <textarea data-body rows="3" class="w-full px-2 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm">${esc(question.body)}</textarea>
      ${diagram ? `<div data-diagram-preview class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-2 min-h-40 flex items-center justify-center">${diagram.svg ? diagram.svg : `<span class="text-xs text-gray-600">No diagram preview available.</span>`}</div>` : ''}
      ${(question.options || []).map((option) => `<label class="flex gap-2 items-center text-xs"><span class="font-semibold w-4">${esc(option.key)}</span><input data-option="${esc(option.key)}" value="${esc(option.text)}" class="flex-1 px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800" /></label>`).join('')}
      <div class="flex gap-2"><input data-answer value="${esc(question.answer)}" placeholder="Answer" class="flex-1 px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-xs" /><button type="button" data-accept class="px-3 py-1.5 rounded-lg bg-primary-50 text-primary-700 text-xs font-semibold">Accept</button></div>`;
    items.appendChild(card);
  });

  items.querySelectorAll('[data-body]').forEach((textarea) => {
    textarea.addEventListener('input', () => {
      const preview = textarea.closest('[data-index]')?.querySelector('[data-math-preview]');
      if (preview) preview.innerHTML = mathHtml(textarea.value);
    });
  });

  const accepted = new Set();
  const savingQuestions = new Set();
  let passageId = null;
  const updateCount = () => { review.querySelector('[data-ai-count]').textContent = `${accepted.size} / ${questions.length} accepted`; };
  const normalize = (text) => String(text || '').toLowerCase().replace(/[^a-z0-9]+/g, ' ').trim();
  const saveReviewedQuestion = async (card) => {
    const index = Number(card.dataset.index);
    if (accepted.has(index)) return null;
    if (savingQuestions.has(index)) throw new Error(`Question ${index + 1} is already being saved`);
    const question = questions[index];
    question.body = card.querySelector('[data-body]').value.trim();
    question.answer = card.querySelector('[data-answer]').value.trim();
    question.options = [...card.querySelectorAll('[data-option]')].map((input) => ({ key: input.dataset.option, text: input.value.trim() })).filter((x) => x.text);
    if (!question.body || (question.type === 'mcq' && question.options.length < 2)) throw new Error(`Question ${index + 1} needs a body and at least two options`);
    const passageTitle = passage ? passageTitleInput.value.trim() : '';
    const passageBody = passage ? passageBodyInput.value.trim() : '';
    if (passage && (!passageTitle || !passageBody)) throw new Error('Passage title and text are required');
    if (passage && !passageId) passageId = `local-passage-${crypto.randomUUID()}`;
    const duplicate = existingQuestions.some((item) => normalize(item.body) === normalize(question.body));
    if (duplicate) throw new Error(`Question ${index + 1} already exists in your question bank`);

    const payload = {
      ...question,
      diagram_spec: question.diagram_spec || question.diagram_request || null,
      source: 'ai',
      ai_generated: true,
      content_type: values.content_type || 'standard',
      passage_id: passage ? passageId : null,
      passage_title: passage ? passageTitle : null,
      passage_body: passage ? passageBody : null,
      subject_id: values.subject_id,
      class_id: values.class_id,
      term_id: values.term_id,
    };

    const acceptButton = card.querySelector('[data-accept]');
    savingQuestions.add(index);
    if (acceptButton) {
      acceptButton.disabled = true;
      acceptButton.textContent = 'Saving…';
    }
    try {
      const saved = await saveQuestionToBank(payload);
      if (passage && saved.passage_id) {
        passageId = saved.passage_id;
        if (!String(passageId).startsWith('local-passage-')) {
          passageTitleInput.disabled = true;
          passageBodyInput.disabled = true;
          passageNote.textContent = 'Passage saved and linked to accepted questions.';
        }
      }
      accepted.add(index);
      card.classList.add('opacity-50');
      if (acceptButton) acceptButton.textContent = 'Accepted';
      updateCount();
      return saved;
    } catch (error) {
      if (acceptButton) {
        acceptButton.disabled = false;
        acceptButton.textContent = 'Accept';
      }
      throw error;
    } finally {
      savingQuestions.delete(index);
    }
  };

  items.querySelectorAll('[data-accept]').forEach((button) => button.addEventListener('click', async () => {
    if (!await confirmModal({ title: 'Add question?', message: 'Add this reviewed question to your question bank?', confirmLabel: 'Add question' })) return;
    try {
      const saved = await saveReviewedQuestion(button.closest('[data-index]'));
      if (!saved) return;
      toast(saved.synced ? 'Question added to your bank' : 'Question saved on this device and will sync when online.', saved.synced ? 'success' : 'info');
    } catch (err) { toast(err.message, 'error'); }
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
  const acceptAllButton = review.querySelector('[data-ai-accept-all]');
  acceptAllButton.onclick = async () => {
    if (acceptAllButton.disabled) return;
    const selectedCards = [...items.querySelectorAll('[data-index]')].filter((card) => card.querySelector('[data-select]')?.checked && !accepted.has(Number(card.dataset.index)));
    if (!selectedCards.length) { toast('Select at least one question', 'error'); return; }
    acceptAllButton.disabled = true;
    if (!await confirmModal({ title: 'Add selected questions?', message: `Add ${selectedCards.length} reviewed question${selectedCards.length === 1 ? '' : 's'} to your question bank?`, confirmLabel: 'Add questions' })) {
      acceptAllButton.disabled = false;
      return;
    }
    const originalLabel = acceptAllButton.textContent;
    acceptAllButton.textContent = 'Saving…';
    const failed = [];
    let added = 0;
    let synced = 0;
    try {
      for (const card of selectedCards) {
        try {
          const saved = await saveReviewedQuestion(card);
          if (saved) {
            added += 1;
            if (saved.synced) synced += 1;
          }
        } catch (err) {
          failed.push(err.message);
        }
      }
      if (added) await onSaved();
      if (failed.length) {
        const savedSummary = `${synced} synced, ${added - synced} saved locally`;
        toast(`${savedSummary}; ${failed.length} failed: ${failed[0]}`, added ? 'warn' : 'error');
      } else if (synced === added && added > 0) {
        toast(`Added ${added} selected question${added === 1 ? '' : 's'} to your bank`, 'success');
        if (accepted.size === questions.length) close();
      } else if (added > 0) {
        toast(`${added - synced} question${added - synced === 1 ? '' : 's'} saved locally; they will sync when online.`, 'info');
      } else {
        toast('The selected questions are already saved.', 'info');
      }
    } finally {
      acceptAllButton.disabled = false;
      acceptAllButton.textContent = originalLabel;
    }
  };
}
