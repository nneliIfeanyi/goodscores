import { api, storageUrl } from '../../utils/api.js';
import { saveOfflineQuestion, getOfflineQuestions, getOfflineQuestion, deleteOfflineQuestion } from '../../utils/db.js';
import { backupQuestionBank } from '../../utils/backup.js';
import { loadMetaData, optionsHtml } from '../../utils/meta.js';
import { toast } from '../../utils/toast.js';
import { confirmModal } from '../../utils/modal.js';
import { openAiQuestionFlow } from '../aiQuestions.js';
import { requireAuthentication } from '../../utils/authGate.js';
import { normalizeDiagramSpec } from '../aiQuestionContract.js';

let meta = { subjects: [], classes: [], terms: [] };
let questions = [];
let editingId = null;

const escapeHtml = (value) => String(value ?? '')
  .replace(/&/g, '&amp;')
  .replace(/</g, '&lt;')
  .replace(/>/g, '&gt;')
  .replace(/"/g, '&quot;');

function isLanguageSubject(subjectId) {
  const subject = meta.subjects.find((item) => String(item.id) === String(subjectId));
  return !!subject && /language|english|french|hausa|yoruba|igbo/i.test(subject.name || '');
}

function contentTypeOptions(subjectId) {
  const subject = meta.subjects.find((item) => String(item.id) === String(subjectId));
  const name = subject?.name || '';
  if (/literature/i.test(name)) {
    return [['standard', 'Standard'], ['prose', 'Prose'], ['poetry', 'Poetry'], ['drama', 'Drama'], ['comprehension', 'Comprehension']];
  }
  if (/english|language|french|hausa|yoruba|igbo/i.test(name)) {
    return [['standard', 'Standard'], ['comprehension', 'Comprehension'], ['passage', 'Passage']];
  }
  if (/biology|basic science|physics|chemistry|agricultural/i.test(name)) {
    return [['standard', 'Standard'], ['practical', 'Practical']];
  }
  if (/economics|commerce|government|civic|social studies|history|geography|business/i.test(name)) {
    return [['standard', 'Standard'], ['case_study', 'Case study'], ['data_interpretation', 'Data interpretation']];
  }
  return [['standard', 'Standard']];
}

async function loadMeta() {
  meta = await loadMetaData();
}

async function renderOfflineDraftStatus() {
  const panel = document.getElementById('offline-draft-status');
  if (!panel) return;
  const drafts = (await getOfflineQuestions({ includeDeleted: true })).filter((q) => q.backup_state !== 'backed_up');
  if (!drafts.length) {
    panel.classList.add('hidden');
    return;
  }
  panel.classList.remove('hidden');
  panel.innerHTML = `
    <div class="flex items-start justify-between gap-3">
      <div>
        <p class="text-sm font-semibold">Offline drafts</p>
        <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">${drafts.length} local change${drafts.length === 1 ? '' : 's'} since the last backup.</p>
      </div>
      <div class="flex shrink-0 gap-2">
        <button id="offline-backup" type="button" class="text-xs font-semibold text-primary-700 dark:text-primary-300 hover:underline">Backup now</button>
      </div>
    </div>`;
  panel.querySelector('#offline-backup')?.addEventListener('click', async () => {
    const { backupQuestionBank } = await import('../../utils/backup.js');
    await backupQuestionBank();
    await renderOfflineDraftStatus();
  });
}

async function loadQuestions(filters = {}) {
  const listEl = document.getElementById('q-list');
  if (listEl) {
    listEl.innerHTML = '<div class="loading-skeleton"></div><div class="loading-skeleton"></div><div class="loading-skeleton"></div>';
  }
  const offline = await getOfflineQuestions();
  questions = offline.filter((q) => (!filters.type || q.type === filters.type)
    && (!filters.subject_id || String(q.subject_id) === String(filters.subject_id))
    && (!filters.class_id || String(q.class_id) === String(filters.class_id))
    && (!filters.search || String(q.body || '').toLowerCase().includes(filters.search.toLowerCase())))
    .map((q) => ({ ...q, subject_name: meta.subjects.find((s) => s.id == q.subject_id)?.name, class_name: meta.classes.find((c) => c.id == q.class_id)?.name }));
  renderList();
}

function renderList() {
  const listEl = document.getElementById('q-list');
  if (!listEl) return;

  if (!questions.length) {
    listEl.innerHTML = `
      <div class="text-center py-12 text-gray-500 dark:text-gray-400">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="font-medium">No questions yet</p>
        <p class="text-sm mt-1">Tap + to create your first question</p>
      </div>`;
    return;
  }

  listEl.innerHTML = questions.map((q) => {
    const typeBadge = {
      mcq: 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
      fill: 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
      theory: 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300',
    }[q.type] || 'bg-gray-100 text-gray-600';

    const preview = (q.body || '').replace(/<[^>]+>/g, '').slice(0, 100);
    const answer = String(q.answer || '').trim();
    const options = Array.isArray(q.options) ? q.options.filter((option) => option?.text) : [];
    const correctAnswer = answer.toLowerCase();
    const diagramSpec = normalizeDiagramSpec(q.diagram_spec || q.diagram_request);
    const diagramImages = (Array.isArray(q.images) ? q.images : [])
      .filter((image) => image?.data_url || image?.file_path);
    if (q._pending_image && !diagramImages.some((image) => image.data_url === q._pending_image)) {
      diagramImages.unshift({ data_url: q._pending_image, original_name: 'Question image' });
    }
    const diagram = diagramSpec || diagramImages.length
      ? `<div class="mb-4 space-y-2"><p class="text-xs font-semibold text-gray-500 uppercase">Diagram</p>
          ${diagramImages.map((image) => `<img src="${escapeHtml(image.data_url || storageUrl(image.file_path))}" alt="${escapeHtml(image.original_name || diagramSpec?.description || 'Question diagram')}" class="max-h-48 max-w-full object-contain mx-auto rounded-lg border border-gray-200 dark:border-gray-700" />`).join('')}
          ${diagramSpec?.svg ? `<div class="max-h-56 overflow-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white p-2 flex justify-center">${diagramSpec.svg}</div>` : ''}
          ${diagramSpec?.description ? `<p class="text-xs text-gray-500 dark:text-gray-400">${escapeHtml(diagramSpec.description)}</p>` : ''}
        </div>`
      : '';
    const details = q.type === 'mcq'
      ? `<div class="space-y-1.5"><p class="text-xs font-semibold text-gray-500 uppercase">Options</p>${options.length ? options.map((option) => {
        const optionKey = String(option.key || '').trim();
        const optionText = String(option.text || '').trim();
        const isCorrect = correctAnswer === optionKey.toLowerCase() || correctAnswer === optionText.toLowerCase();
        return `<div class="flex items-start gap-2 text-sm ${isCorrect ? 'text-green-700 dark:text-green-300 font-medium' : 'text-gray-600 dark:text-gray-300'}"><span class="shrink-0 font-semibold">${escapeHtml(optionKey)}.</span><span>${escapeHtml(optionText)}</span>${isCorrect ? '<span class="text-[10px] uppercase tracking-wide">Correct</span>' : ''}</div>`;
      }).join('') : '<p class="text-sm text-gray-400">No options saved</p>'}</div>`
      : `<div><p class="text-xs font-semibold text-gray-500 uppercase">${q.type === 'fill' ? 'Correct answer' : 'Answer / marking guide'}</p><p class="mt-1 text-sm text-gray-700 dark:text-gray-200 whitespace-pre-wrap">${answer ? escapeHtml(answer) : 'No answer saved'}</p></div>`;

    return `
      <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 hover:border-primary-300 transition" data-id="${q.id || q.offline_id}">
        <div class="flex items-start justify-between gap-2">
          <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2 mb-1.5">
              <span class="text-[10px] font-semibold uppercase px-2 py-0.5 rounded-full ${typeBadge}">${q.type}</span>
              ${q.subject_name ? `<span class="text-xs text-gray-500">${q.subject_name}</span>` : ''}
              ${q.class_name ? `<span class="text-xs text-gray-400">· ${q.class_name}</span>` : ''}
              ${q.passage_id ? `<span class="text-xs text-primary-600">Passage: ${q.passage_title || 'linked'}</span>` : ''}
              ${q.image_count > 0 ? `<span class="text-xs text-primary-600">📷 ${q.image_count}</span>` : ''}
            </div>
            <p class="text-sm text-gray-800 dark:text-gray-200 line-clamp-2">${preview}${preview.length >= 100 ? '…' : ''}</p>
            <p class="text-xs text-gray-400 mt-1">${q.marks || 1} mark(s)</p>
          </div>
          <div class="flex gap-1 shrink-0">
            <button class="btn-toggle-details p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700" aria-expanded="false" title="Show options and answer">
              <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <button class="btn-edit p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700" data-id="${q.id || ''}" data-offline="${q.offline_id || ''}" title="Edit">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            </button>
            <button class="btn-del p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-red-500" data-id="${q.id || ''}" data-offline="${q.offline_id || ''}" title="Delete">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
          </div>
        </div>
        <div class="question-details hidden mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">${diagram}${details}</div>
      </div>`;
  }).join('');

  listEl.querySelectorAll('.btn-toggle-details').forEach((btn) => {
    btn.addEventListener('click', () => {
      const card = btn.closest('.bg-white[data-id]');
      const details = card?.querySelector('.question-details');
      const expanded = btn.getAttribute('aria-expanded') === 'true';
      details?.classList.toggle('hidden', expanded);
      btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
      btn.querySelector('svg')?.classList.toggle('rotate-180', !expanded);
    });
  });
  listEl.querySelectorAll('.btn-edit').forEach((btn) => {
    btn.addEventListener('click', () => openForm(btn.dataset.id || null, btn.dataset.offline || null));
  });
  listEl.querySelectorAll('.btn-del').forEach((btn) => {
    btn.addEventListener('click', () => deleteQuestion(btn.dataset.id || btn.dataset.offline));
  });
}

async function openForm(id = null, offlineId = null) {
  editingId = id;
  let q = id ? questions.find((x) => String(x.id) === String(id) || String(x.offline_id) === String(offlineId || id)) : null;
  if (!q && offlineId) q = await getOfflineQuestion(offlineId);
  const diagramSpec = q?.diagram_spec || q?.diagram_request || null;
  const illustration = q?.images?.find((image) => image.type === 'ai_illustration');

  const overlay = document.createElement('div');
  overlay.id = 'q-form-overlay';
  overlay.className = 'fixed inset-0 z-50 bg-black/40 flex items-end sm:items-center justify-center p-0 sm:p-4';
  overlay.innerHTML = `
    <div class="bg-white dark:bg-gray-900 w-full sm:max-w-lg max-h-[92vh] overflow-y-auto rounded-t-2xl sm:rounded-2xl shadow-xl">
      <div class="sticky top-0 bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 px-4 py-3 flex items-center justify-between">
        <h3 class="font-semibold">${id ? 'Edit Question' : 'New Question'}</h3>
        <button id="q-form-close" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
      <form id="q-form" class="p-4 space-y-4">
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-medium mb-1">Type</label>
            <select name="type" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm">
              <option value="mcq" ${q?.type === 'mcq' ? 'selected' : ''}>MCQ</option>
              <option value="fill" ${q?.type === 'fill' ? 'selected' : ''}>Fill in the gap</option>
              <option value="theory" ${q?.type === 'theory' ? 'selected' : ''}>Theory</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium mb-1">Marks</label>
            <input type="number" name="marks" min="0.5" step="0.5" value="${q?.marks || 1}"
              class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm" />
          </div>
        </div>

        <div class="grid grid-cols-3 gap-2">
          <div>
            <label class="block text-xs font-medium mb-1">Subject</label>
            <select name="subject_id" class="w-full px-2 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm">
              <option value="">${meta.subjects.length ? '—' : 'Add a subject in Account settings'}</option>
              ${meta.subjects.map((s) => `<option value="${s.id}" ${q?.subject_id == s.id ? 'selected' : ''}>${s.name}</option>`).join('')}
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium mb-1">Class</label>
            <select name="class_id" class="w-full px-2 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm">
              <option value="">${meta.classes.length ? '—' : 'Add a class in Account settings'}</option>
              ${meta.classes.map((c) => `<option value="${c.id}" ${q?.class_id == c.id ? 'selected' : ''}>${c.name}</option>`).join('')}
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium mb-1">Term</label>
            <select name="term_id" class="w-full px-2 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm">
              <option value="">—</option>
              ${meta.terms.map((t) => `<option value="${t.id}" ${q?.term_id == t.id ? 'selected' : ''}>${t.name}</option>`).join('')}
            </select>
          </div>
        </div>
        ${(!meta.subjects.length || !meta.classes.length) ? `<div class="rounded-xl border border-amber-200 bg-amber-50 dark:border-amber-800 dark:bg-amber-900/20 p-3 text-xs text-amber-800 dark:text-amber-200">
          Add your own ${!meta.subjects.length ? 'subjects' : ''}${!meta.subjects.length && !meta.classes.length ? ' and ' : ''}${!meta.classes.length ? 'classes' : ''} in Account settings before assigning them to questions.
          <button type="button" id="q-open-account" class="ml-1 font-semibold underline">Open Account settings</button>
        </div>` : ''}

        <div>
          <label class="block text-xs font-medium mb-1">Content type</label>
          <select name="content_type" class="w-full px-2 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm"></select>
          <p class="text-[10px] text-gray-500 mt-1">Options are tailored to the selected subject. Leave Standard for ordinary questions.</p>
        </div>

        <div id="passage-section" class="hidden rounded-xl border border-primary-100 dark:border-primary-800 bg-primary-50/60 dark:bg-primary-900/20 p-3 space-y-2">
          <div>
            <label class="block text-xs font-medium mb-1">Passage or extract</label>
            <select name="passage_id" class="w-full px-2 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm">
              <option value="">Standalone question</option>
              <option value="new">Create a new passage</option>
            </select>
          </div>
          <div id="new-passage-fields" class="hidden space-y-2">
            <input name="passage_title" maxlength="255" placeholder="Passage title, e.g. The School Garden"
              class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm" />
            <textarea name="passage_body" rows="5" placeholder="Write the comprehension passage here..."
              class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm"></textarea>
          </div>
          <p class="text-[10px] text-gray-500">Save one passage and attach several questions to it. It will appear once when the paper is exported.</p>
        </div>

        <div>
          <div class="flex items-center justify-between gap-2 mb-1">
            <label class="block text-xs font-medium">Question</label>
            <button type="button" id="editor-mode-toggle" class="text-[11px] font-medium text-primary-600 dark:text-primary-400 hover:underline" aria-pressed="true">
              Use basic inputs
            </button>
          </div>
          <textarea name="body" rows="4"
            class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm"
            placeholder="Type the question here…">${q?.body || ''}</textarea>
        </div>

        <div id="mcq-options" class="${(q?.type || 'mcq') !== 'mcq' ? 'hidden' : ''}">
          <label class="block text-xs font-medium mb-1">Options</label>
          <div id="options-list" class="space-y-2">
            ${(q?.options || [{ key: 'A', text: '' }, { key: 'B', text: '' }, { key: 'C', text: '' }, { key: 'D', text: '' }])
              .map((o, i) => `
              <div class="flex gap-2 items-center">
                <span class="w-6 text-center font-semibold text-sm text-primary-600">${o.key || String.fromCharCode(65 + i)}</span>
                <textarea id="q-opt-${o.key || String.fromCharCode(65 + i)}" name="opt_text" data-key="${o.key || String.fromCharCode(65 + i)}" rows="1"
                  class="flex-1 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm"
                  placeholder="Option ${o.key || String.fromCharCode(65 + i)}">${(o.text || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')}</textarea>
              </div>`).join('')}
          </div>
          <div class="mt-2">
            <label class="block text-xs font-medium mb-1">Correct answer</label>
            <textarea id="q-mcq-answer" name="answer" rows="1"
              class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm"
              placeholder="e.g. A or the full text">${q?.answer || ''}</textarea>
          </div>
        </div>

        <div id="answer-field" class="${(q?.type || 'mcq') === 'mcq' ? 'hidden' : ''}">
          <label class="block text-xs font-medium mb-1">Answer / Marking guide</label>
          <textarea name="answer_other" rows="2"
            class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm"
            placeholder="Expected answer or marking points">${q?.type !== 'mcq' ? (q?.answer || '') : ''}</textarea>
        </div>

        <div>
          <div class="flex items-center justify-between gap-2 mb-1">
            <label class="block text-xs font-medium">Diagram / Image (optional)</label>
            <button type="button" id="q-ocr" class="text-[11px] font-medium text-primary-600 dark:text-primary-400 hover:underline">Extract text with OCR</button>
          </div>
          <input type="file" id="q-image" accept="image/*" class="w-full text-sm" />
          <p class="text-[10px] text-gray-400 mt-1">OCR reads text only; the scanned image is ignored and will not be saved as the question diagram. Costs 35 credits.</p>
          <div id="q-image-preview" class="mt-2 hidden">
            <img class="max-h-32 rounded-lg border" />
          </div>
        </div>

        <div class="rounded-xl border border-primary-100 dark:border-primary-800 bg-primary-50/50 dark:bg-primary-900/10 p-3 space-y-2">
          <div class="flex items-center justify-between"><label class="text-xs font-semibold">Educational diagram specification</label><span class="text-[10px] text-gray-500">Optional</span></div>
          <select name="diagram_type" class="w-full px-2 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-xs">
            <option value="">No generated diagram</option>
            <option value="precise_diagram" ${(diagramSpec?.type || '') === 'precise_diagram' ? 'selected' : ''}>Precise SVG diagram</option>
            <option value="illustration" ${(diagramSpec?.type || '') === 'illustration' ? 'selected' : ''}>Educational illustration</option>
          </select>
          <textarea name="diagram_description" rows="2" placeholder="Describe the diagram for learners" class="w-full px-2 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-xs">${escapeHtml(diagramSpec?.description || '')}</textarea>
          <div class="flex gap-2">
            <button type="button" id="q-generate-svg" class="flex-1 py-2 rounded-lg border border-primary-200 text-primary-700 dark:text-primary-300 text-xs font-semibold">Generate SVG</button>
            <button type="button" id="q-generate-illustration" class="flex-1 py-2 rounded-lg border border-primary-200 text-primary-700 dark:text-primary-300 text-xs font-semibold">Generate illustration</button>
          </div>
          <textarea name="diagram_svg" rows="4" placeholder="Precise SVG diagram markup" class="${diagramSpec?.type === 'precise_diagram' ? '' : 'hidden'} w-full px-2 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-[10px] font-mono">${escapeHtml(diagramSpec?.svg || '')}</textarea>
          ${diagramSpec?.type === 'precise_diagram' && diagramSpec.svg ? `<div class="rounded-lg bg-white border p-2 flex justify-center">${diagramSpec.svg}</div>` : ''}
          ${illustration ? `<div><p class="text-[10px] text-gray-500 mb-1">Generated illustration</p><img src="${escapeHtml(storageUrl(illustration.file_path))}" alt="${escapeHtml(illustration.caption || diagramSpec?.description || 'Educational illustration')}" class="max-h-40 max-w-full object-contain mx-auto rounded-lg border" /></div>` : ''}
        </div>

        <div id="q-form-error" class="hidden text-sm text-red-600 bg-red-50 dark:bg-red-900/20 rounded-lg px-3 py-2"></div>

        <button type="submit" class="w-full py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold text-sm">
          ${id ? 'Save changes' : 'Create question'}
        </button>
      </form>
    </div>
  `;

  document.body.appendChild(overlay);
  overlay.querySelector('#q-open-account')?.addEventListener('click', () => {
    overlay.remove();
    document.querySelector('[data-page="account"]')?.click();
  });
  const editors = {};
  const richTextareas = [
    ...['body', 'answer_other', 'passage_body'].map((field) => overlay.querySelector(`[name="${field}"]`)),
    ...overlay.querySelectorAll('[name="opt_text"], [name="answer"]'),
  ].filter(Boolean);
  const editorModeToggle = overlay.querySelector('#editor-mode-toggle');
  let editorMode = 'basic';

  const isDarkTheme = () => document.documentElement.classList.contains('dark');
  const setEditorModeLabel = () => {
    const advanced = editorMode === 'advanced';
    editorModeToggle.textContent = advanced ? 'Use basic inputs' : 'Use advanced editor';
    editorModeToggle.setAttribute('aria-pressed', String(advanced));
  };

  const initRichEditors = () => {
    if (editorMode !== 'advanced' || !window.tinymce) return;
    richTextareas.forEach((textarea) => {
      if (editors[textarea.id || textarea.name]) return;
      const field = textarea.id || textarea.name;
      window.tinymce.init({
        target: textarea,
        menubar: false,
        height: field === 'body' ? 220 : 180,
        plugins: 'lists link charmap',
        toolbar: 'bold italic underline superscript subscript | bullist numlist | charmap | removeformat',
        skin: isDarkTheme() ? 'oxide-dark' : 'oxide',
        content_css: isDarkTheme() ? 'dark' : 'default',
        branding: false,
        setup: (editor) => { editors[field] = editor; },
      });
    });
  };

  const removeRichEditors = () => {
    richTextareas.forEach((textarea) => {
      const field = textarea.id || textarea.name;
      const editor = editors[field];
      if (!editor) return;
      textarea.value = editor.getContent();
      editor.remove();
      delete editors[field];
      textarea.style.display = '';
    });
  };

  const syncEditorsWithTheme = () => {
    if (editorMode !== 'advanced' || !window.tinymce) return;
    const values = {};
    richTextareas.forEach((textarea) => {
      const field = textarea.id || textarea.name;
      values[field] = editors[field]?.getContent() ?? textarea.value;
    });
    removeRichEditors();
    richTextareas.forEach((textarea) => {
      const field = textarea.id || textarea.name;
      textarea.value = values[field] || '';
    });
    initRichEditors();
  };

  editorModeToggle?.addEventListener('click', () => {
    editorMode = editorMode === 'advanced' ? 'basic' : 'advanced';
    if (editorMode === 'basic') removeRichEditors();
    else initRichEditors();
    setEditorModeLabel();
  });
  window.addEventListener('gs-theme-change', syncEditorsWithTheme);
  setEditorModeLabel();
  const closeForm = () => {
    removeRichEditors();
    window.removeEventListener('gs-theme-change', syncEditorsWithTheme);
    overlay.remove();
  };
  document.getElementById('q-form-close').onclick = closeForm;

  // Type toggle
  const typeSelect = overlay.querySelector('[name="type"]');
  typeSelect.addEventListener('change', () => {
    const isMcq = typeSelect.value === 'mcq';
    document.getElementById('mcq-options').classList.toggle('hidden', !isMcq);
    document.getElementById('answer-field').classList.toggle('hidden', isMcq);
  });
  const diagramTypeSelect = overlay.querySelector('[name="diagram_type"]');
  const diagramSvgField = overlay.querySelector('[name="diagram_svg"]');
  const diagramDescriptionField = overlay.querySelector('[name="diagram_description"]');
  const generateSvgButton = overlay.querySelector('#q-generate-svg');
  const generateIllustrationButton = overlay.querySelector('#q-generate-illustration');
  diagramTypeSelect?.addEventListener('change', () => {
    diagramSvgField?.classList.toggle('hidden', diagramTypeSelect.value !== 'precise_diagram');
  });
  generateSvgButton?.addEventListener('click', async () => {
    if (!localStorage.getItem('gs_token')) return requireAuthentication('Generate SVG diagram', () => generateSvgButton.click());
    if (!navigator.onLine) return toast('SVG generation requires an internet connection.', 'warn');
    const description = diagramDescriptionField?.value.trim() || '';
    if (!description) return toast('Describe the diagram first.', 'error');
    generateSvgButton.disabled = true;
    generateSvgButton.textContent = 'Generating…';
    try {
      const result = await api('/ai/diagram/generate', { method: 'POST', body: JSON.stringify({ type: 'precise_diagram', description }) });
      diagramTypeSelect.value = 'precise_diagram';
      diagramSvgField.value = result.data?.svg || '';
      diagramSvgField.classList.remove('hidden');
      toast('SVG diagram generated. Review it before saving.', 'success');
    } catch (error) {
      toast(error.message || 'Could not generate SVG diagram', 'error');
    } finally {
      generateSvgButton.disabled = false;
      generateSvgButton.textContent = 'Generate SVG';
    }
  });
  generateIllustrationButton?.addEventListener('click', async () => {
    if (!localStorage.getItem('gs_token')) return requireAuthentication('Generate illustration', () => generateIllustrationButton.click());
    if (!navigator.onLine) return toast('Illustration generation requires an internet connection.', 'warn');
    if (!q?.offline_id && !id) return toast('Save the question first, then generate its illustration.', 'info');
    const description = diagramDescriptionField?.value.trim() || '';
    if (!description) return toast('Describe the illustration first.', 'error');
    generateIllustrationButton.disabled = true;
    generateIllustrationButton.textContent = 'Generating…';
    try {
      let serverQuestionId = id || q?.id;
      if (!serverQuestionId && q?.offline_id) {
        await backupQuestionBank({ offlineId: q.offline_id });
        const backedUpQuestion = await getOfflineQuestion(q.offline_id);
        serverQuestionId = backedUpQuestion?.id;
        if (!serverQuestionId) throw new Error('The question could not be backed up. Please try Backup now, then generate again.');
        q = backedUpQuestion;
      }
      const result = await api(`/questions/${serverQuestionId}/generate-illustration`, { method: 'POST' });
      const generatedImage = { ...result.data, type: 'ai_illustration', caption: description };
      q.images = [...(q.images || []).filter((image) => image.type !== 'ai_illustration'), generatedImage];
      await saveOfflineQuestion(q);
      renderList();
      toast('Educational illustration generated.', 'success');
    } catch (error) {
      toast(error.message || 'Could not generate illustration', 'error');
    } finally {
      generateIllustrationButton.disabled = false;
      generateIllustrationButton.textContent = 'Generate illustration';
    }
  });

  const subjectSelect = overlay.querySelector('[name="subject_id"]');
  const contentTypeSelect = overlay.querySelector('[name="content_type"]');
  const passageSection = overlay.querySelector('#passage-section');
  const passageSelect = overlay.querySelector('[name="passage_id"]');
  const newPassageFields = overlay.querySelector('#new-passage-fields');
  const passageBody = overlay.querySelector('[name="passage_body"]');
  let passageEditor = null;

  const loadPassages = async () => {
    passageSelect.innerHTML = '<option value="">Standalone question</option><option value="new">Create a new passage</option>';
  };

  const updatePassageVisibility = () => {
    const visible = ['comprehension', 'passage', 'prose', 'poetry', 'drama'].includes(contentTypeSelect.value);
    passageSection.classList.toggle('hidden', !visible);
    if (!visible) {
      passageSelect.value = '';
      newPassageFields.classList.add('hidden');
    } else {
      loadPassages();
    }
  };

  const updateContentTypes = () => {
    const current = contentTypeSelect.value || q?.content_type || 'standard';
    contentTypeSelect.innerHTML = contentTypeOptions(subjectSelect.value)
      .map(([value, label]) => `<option value="${value}">${label}</option>`).join('');
    contentTypeSelect.value = contentTypeOptions(subjectSelect.value).some(([value]) => value === current) ? current : 'standard';
    updatePassageVisibility();
  };

  subjectSelect.addEventListener('change', updateContentTypes);
  contentTypeSelect.addEventListener('change', updatePassageVisibility);
  ['class_id', 'term_id'].forEach((field) => {
    overlay.querySelector(`[name="${field}"]`).addEventListener('change', () => {
      if (!passageSection.classList.contains('hidden')) loadPassages();
    });
  });
  passageSelect.addEventListener('change', () => {
    newPassageFields.classList.toggle('hidden', passageSelect.value !== 'new');
  });
  updateContentTypes();

  initRichEditors();

  let ocrController = null;
  overlay.querySelector('#q-ocr')?.addEventListener('click', async (event) => {
    if (!localStorage.getItem('gs_token')) {
      requireAuthentication('OCR', () => openForm(editingId, q?.offline_id));
      return;
    }
    if (!navigator.onLine) {
      toast('OCR is unavailable while offline.', 'warn');
      return;
    }
    if (ocrController) {
      ocrController.abort();
      return;
    }
    const file = fileInput?.files?.[0];
    if (!file) {
      toast('Choose an image for OCR first', 'warn');
      return;
    }
    const button = event.currentTarget;
    ocrController = new AbortController();
    button.classList.add('text-red-600');
    button.title = 'Click to cancel OCR';
    button.disabled = false;
    button.textContent = 'Reading image…';
    try {
      const image = await prepareOcrImage(file, (stage) => { button.textContent = stage; });
      button.textContent = 'Recognizing text…';
      const result = await api('/ocr', {
        method: 'POST',
        body: JSON.stringify({ image }),
        signal: ocrController.signal,
      });
      button.textContent = 'Preparing questions…';
      const scannedQuestions = result.data?.questions || (result.data?.parsed ? [result.data.parsed] : []);
      if (scannedQuestions.length > 1) {
        const scanValues = {
          subject_id: subjectSelect.value,
          class_id: overlay.querySelector('[name="class_id"]')?.value,
          term_id: overlay.querySelector('[name="term_id"]')?.value,
          content_type: overlay.querySelector('[name="content_type"]')?.value || 'standard',
        };
        overlay.remove();
        renderOcrReview(scannedQuestions, scanValues);
        return;
      }
      const parsed = result.data?.parsed || {};
      const setContent = (field, value) => {
        const editor = editors[field];
        if (editor) editor.setContent(value || '');
        else {
          const input = overlay.querySelector(`[name="${field}"]`);
          if (input) input.value = value || '';
        }
      };
      setContent('body', parsed.body || result.data?.text || '');
      if (parsed.type) {
        typeSelect.value = parsed.type;
        typeSelect.dispatchEvent(new Event('change'));
      }
      (parsed.options || []).forEach((option) => {
        const input = overlay.querySelector(`[name="opt_text"][data-key="${option.key}"]`);
        if (input) {
          if (editors[input.id]) editors[input.id].setContent(option.text || '');
          else input.value = option.text || '';
        }
      });
      if (parsed.answer) setContent(typeSelect.value === 'mcq' ? 'answer' : 'answer_other', parsed.answer);
      toast(`OCR complete. ${result.data?.credits_left ?? ''} credits remaining. Review before saving.`, 'success');
    } catch (err) {
      if (err.name === 'AbortError') toast('OCR cancelled', 'info');
      else toast(err.message || 'OCR failed', 'error');
    } finally {
      ocrController = null;
      button.classList.remove('text-red-600');
      button.title = '';
      button.textContent = 'Extract text with OCR';
    }
  });

  // Image preview
  const fileInput = document.getElementById('q-image');
  fileInput?.addEventListener('change', () => {
    const file = fileInput.files?.[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (e) => {
      const prev = document.getElementById('q-image-preview');
      prev.classList.remove('hidden');
      prev.querySelector('img').src = e.target.result;
    };
    reader.readAsDataURL(file);
  });

  document.getElementById('q-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const errEl = document.getElementById('q-form-error');
    errEl.classList.add('hidden');

    const type = form.type.value;
    const payload = {
      type,
      body: (editors.body ? editors.body.getContent() : form.body.value).trim(),
      marks: parseFloat(form.marks.value) || 1,
      subject_id: form.subject_id.value || null,
      class_id: form.class_id.value || null,
      term_id: form.term_id.value || null,
      content_type: form.content_type.value || 'standard',
      passage_id: form.passage_id.value && form.passage_id.value !== 'new' ? Number(form.passage_id.value) : null,
      difficulty: 'medium',
    };

    const diagramType = form.diagram_type?.value || '';
    const diagramDescription = form.diagram_description?.value.trim() || '';
    const diagramSvg = form.diagram_svg?.value.trim() || '';
    payload.diagram_spec = diagramType && diagramDescription
      ? {
        type: diagramType,
        description: diagramDescription,
        labels: [],
        svg: diagramType === 'precise_diagram' ? diagramSvg : '',
        image_prompt: diagramType === 'illustration' ? diagramDescription : '',
      }
      : null;

    if (type === 'mcq') {
      const opts = [];
      form.querySelectorAll('[name="opt_text"]').forEach((inp) => {
        const editor = editors[inp.id];
        const text = (editor ? editor.getContent() : inp.value).trim();
        if (text) opts.push({ key: inp.dataset.key, text });
      });
      payload.options = opts;
      payload.answer = (editors['q-mcq-answer'] ? editors['q-mcq-answer'].getContent() : form.answer.value).trim();
    } else {
      payload.answer = (editors.answer_other ? editors.answer_other.getContent() : form.answer_other.value).trim();
    }

    const btn = form.querySelector('[type="submit"]');
    btn.disabled = true;
    btn.textContent = 'Saving…';

    try {
      if (!payload.body) {
        throw new Error('Question text is required');
      }
      if (form.passage_id.value === 'new') {
        const passageText = (editors.passage_body ? editors.passage_body.getContent() : form.passage_body.value).trim();
        if (!form.passage_title.value.trim() || !passageText.trim()) {
          throw new Error('Passage title and text are required');
        }
        payload.passage_id = q?.passage_id || `local-passage-${crypto.randomUUID()}`;
        payload.passage_title = form.passage_title.value.trim();
        payload.passage_body = passageText;
      }

      payload.offline_id = q?.offline_id || offlineId || crypto.randomUUID();
      payload.source = q?.source || 'manual';
      const file = fileInput?.files?.[0];
      if (file) {
        const imageData = await prepareStoredImage(file);
        payload._pending_image = imageData;
        payload.images = [{
          data_url: imageData,
          original_name: file.name,
          mime_type: 'image/jpeg',
          type: 'diagram',
          position: 'after_body',
          sort_order: 0,
        }];
      }
      await saveOfflineQuestion(payload);
      closeForm();
      toast(editingId ? 'Question updated locally' : 'saved successfully', 'success');
      await loadQuestions();
    } catch (err) {
      errEl.textContent = err.message || 'Failed to save';
      errEl.classList.remove('hidden');
    } finally {
      btn.disabled = false;
      btn.textContent = editingId ? 'Save changes' : 'Create question';
    }
  });
}

function chooseQuestionCreation() {
  const overlay = document.createElement('div');
  overlay.className = 'fixed inset-0 z-50 bg-black/40 flex items-end sm:items-center justify-center p-4';
  overlay.innerHTML = `
    <div class="bg-white dark:bg-gray-900 w-full max-w-sm rounded-2xl shadow-xl p-5">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold">New question</h3>
        <button data-close class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800" aria-label="Close">✕</button>
      </div>
      <div class="grid gap-3">
        <button data-use-ai class="text-left rounded-xl border border-primary-200 dark:border-primary-800 bg-primary-50 dark:bg-primary-900/20 p-4 hover:border-primary-500">
          <span class="block font-semibold text-primary-700 dark:text-primary-300">Use AI</span>
          <span class="block text-xs text-gray-500 mt-1">Generate curriculum-focused questions, review them, then accept.</span>
        </button>
        <button data-scan-paper class="text-left rounded-xl border border-cyan-200 dark:border-cyan-800 bg-cyan-50 dark:bg-cyan-900/20 p-4 hover:border-cyan-500">
          <span class="block font-semibold text-cyan-700 dark:text-cyan-300">Scan a paper</span>
          <span class="block text-xs text-gray-500 mt-1">Extract one or more questions from an image, review them, then accept.</span>
        </button>
        <button data-manual class="text-left rounded-xl border border-gray-200 dark:border-gray-700 p-4 hover:border-primary-400">
          <span class="block font-semibold">Manual entry</span>
          <span class="block text-xs text-gray-500 mt-1">Write and save one question yourself.</span>
        </button>
      </div>
    </div>`;
  document.body.appendChild(overlay);
  const close = () => overlay.remove();
  overlay.querySelector('[data-close]').onclick = close;
  overlay.querySelector('[data-manual]').onclick = () => { close(); openForm(); };
  overlay.querySelector('[data-scan-paper]').onclick = () => { close(); openOcrScan(); };
  overlay.querySelector('[data-use-ai]').onclick = () => {
    close();
    openAiQuestionFlow(meta, loadQuestions, questions);
  };
}

function openOcrScan() {
  const overlay = document.createElement('div');
  overlay.className = 'fixed inset-0 z-50 bg-black/40 flex items-end sm:items-center justify-center p-4';
  overlay.innerHTML = `
    <div class="bg-white dark:bg-gray-900 w-full max-w-lg rounded-2xl shadow-xl p-5">
      <div class="flex items-center justify-between mb-4">
        <div><h3 class="font-semibold">Scan a paper</h3><p class="text-xs text-gray-500 mt-1">Upload a clear image containing one or more questions.</p></div>
        <button data-close class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800" aria-label="Close">✕</button>
      </div>
      <form data-ocr-form class="space-y-3">
        <div class="grid grid-cols-3 gap-2">
          <label class="text-xs font-medium">Subject<select name="subject_id" class="mt-1 w-full px-2 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm"><option value="">${meta.subjects.length ? '—' : 'Add in Account settings'}</option>${meta.subjects.map((x) => `<option value="${x.id}">${escapeHtml(x.name)}</option>`).join('')}</select></label>
          <label class="text-xs font-medium">Class<select name="class_id" class="mt-1 w-full px-2 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm"><option value="">${meta.classes.length ? '—' : 'Add in Account settings'}</option>${meta.classes.map((x) => `<option value="${x.id}">${escapeHtml(x.name)}</option>`).join('')}</select></label>
          <label class="text-xs font-medium">Term<select name="term_id" class="mt-1 w-full px-2 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm"><option value="">—</option>${meta.terms.map((x) => `<option value="${x.id}">${escapeHtml(x.name)}</option>`).join('')}</select></label>
        </div>
        <label class="block text-xs font-medium">Paper image<input name="image" type="file" accept="image/*" required class="mt-1 w-full text-sm" /></label>
        <p class="text-xs text-gray-500">OCR reads text only. The scanned image is ignored and will not be saved with the questions. OCR costs 35 credits. Nothing is saved until you review and accept the questions.</p>
        <div data-ocr-error class="hidden text-sm text-red-600 bg-red-50 dark:bg-red-900/20 rounded-lg px-3 py-2"></div>
        <div class="flex gap-2"><button type="submit" class="flex-1 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold">Extract questions</button><button type="button" data-ocr-cancel class="hidden px-3 py-2.5 rounded-xl border border-red-200 text-red-600 text-sm">Cancel</button></div>
      </form>
    </div>`;
  document.body.appendChild(overlay);
  let scanController = null;
  const close = () => { scanController?.abort(); overlay.remove(); };
  overlay.querySelector('[data-close]').onclick = close;
  const cancelButton = overlay.querySelector('[data-ocr-cancel]');
  cancelButton.onclick = () => scanController?.abort();
  overlay.querySelector('[data-ocr-form]').addEventListener('submit', async (event) => {
    event.preventDefault();
    const form = event.currentTarget;
    const button = form.querySelector('button[type="submit"]');
    const error = form.querySelector('[data-ocr-error]');
    const file = form.image.files?.[0];
    if (!file) return;
    scanController = new AbortController();
    button.disabled = true;
    cancelButton.classList.remove('hidden');
    button.textContent = 'Reading image…';
    error.classList.add('hidden');
    try {
      if (!localStorage.getItem('gs_token')) {
        close();
        requireAuthentication('OCR', () => openOcrScan());
        return;
      }
      const image = await prepareOcrImage(file, (stage) => { button.textContent = stage; });
      button.textContent = 'Recognizing text…';
      const result = await api('/ocr', { method: 'POST', body: JSON.stringify({ image }), signal: scanController.signal });
      button.textContent = 'Preparing questions…';
      const scannedQuestions = result.data?.questions || (result.data?.parsed ? [result.data.parsed] : []);
      if (!scannedQuestions.length) throw new Error('No questions were found in the image');
      close();
      renderOcrReview(scannedQuestions, {
        subject_id: form.subject_id.value,
        class_id: form.class_id.value,
        term_id: form.term_id.value,
        credits_left: result.data?.credits_left,
      });
    } catch (err) {
      error.textContent = err.name === 'AbortError' ? 'OCR cancelled.' : (err.message || 'OCR failed');
      error.classList.remove('hidden');
    } finally {
      scanController = null;
      button.disabled = false;
      cancelButton.classList.add('hidden');
      button.textContent = 'Extract questions';
    }
  });
}

function renderOcrReview(scannedQuestions, values) {
  const overlay = document.createElement('div');
  overlay.className = 'fixed inset-0 z-[60] bg-black/40 flex items-end sm:items-center justify-center p-0 sm:p-4';
  overlay.innerHTML = `
    <div class="bg-white dark:bg-gray-900 w-full sm:max-w-xl max-h-[94vh] overflow-y-auto rounded-t-2xl sm:rounded-2xl shadow-xl">
      <div class="sticky top-0 z-10 bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 px-4 py-3 flex items-center justify-between"><div><h3 class="font-semibold">Review scanned questions</h3><p class="text-xs text-gray-500">Edit each question before adding it to your bank.</p></div><button data-close class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800" aria-label="Close">✕</button></div>
      <div class="p-4 space-y-3"><div class="text-xs text-amber-700 bg-amber-50 dark:bg-amber-900/20 rounded-lg px-3 py-2">OCR can make mistakes. Check the text, options, and answers before accepting.</div><div data-ocr-items class="space-y-3"></div><div class="flex gap-2 pt-2"><button data-ocr-accept type="button" class="flex-1 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold">Accept selected</button></div></div>
    </div>`;
  document.body.appendChild(overlay);
  const close = () => overlay.remove();
  overlay.querySelector('[data-close]').onclick = close;
  const items = overlay.querySelector('[data-ocr-items]');
  scannedQuestions.forEach((question, index) => {
    const card = document.createElement('div');
    card.className = 'rounded-xl border border-gray-200 dark:border-gray-700 p-3 space-y-2';
    card.dataset.index = index;
    card.innerHTML = `<label class="flex items-center gap-2 text-xs font-semibold"><input type="checkbox" data-select checked />Question ${index + 1}</label><textarea data-body rows="3" class="w-full px-2 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm">${escapeHtml(question.body)}</textarea>${(question.options || []).map((option) => `<label class="flex gap-2 items-center text-xs"><span class="font-semibold w-4">${escapeHtml(option.key)}</span><input data-option="${escapeHtml(option.key)}" value="${escapeHtml(option.text)}" class="flex-1 px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800" /></label>`).join('')}<input data-answer value="${escapeHtml(question.answer)}" placeholder="Answer (optional)" class="w-full px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-xs" /><button type="button" data-remove class="text-xs text-red-500">Remove</button>`;
    items.appendChild(card);
  });
  items.querySelectorAll('[data-remove]').forEach((button) => button.addEventListener('click', () => button.closest('[data-index]').remove()));
  overlay.querySelector('[data-ocr-accept]').onclick = async () => {
    const selected = [...items.querySelectorAll('[data-index]')].filter((card) => card.querySelector('[data-select]')?.checked);
    if (!selected.length) { toast('Select at least one question', 'error'); return; }
    if (!await confirmModal({ title: 'Add scanned questions?', message: `Add ${selected.length} scanned question${selected.length === 1 ? '' : 's'} to your question bank?`, confirmLabel: 'Add questions' })) return;
    const button = overlay.querySelector('[data-ocr-accept]');
    button.disabled = true;
    let saved = 0;
    let failed = '';
    for (const card of selected) {
      const source = scannedQuestions[Number(card.dataset.index)];
      const type = source.type || (card.querySelectorAll('[data-option]').length >= 2 ? 'mcq' : 'theory');
      const payload = { ...source, type, content_type: values.content_type || 'standard', body: card.querySelector('[data-body]').value.trim(), answer: card.querySelector('[data-answer]').value.trim(), options: [...card.querySelectorAll('[data-option]')].map((input) => ({ key: input.dataset.option, text: input.value.trim() })).filter((option) => option.text), subject_id: values.subject_id || null, class_id: values.class_id || null, term_id: values.term_id || null, marks: 1 };
      try {
        if (!payload.body || (payload.type === 'mcq' && payload.options.length < 2)) throw new Error('A question needs text and at least two options for MCQ');
        await saveOfflineQuestion({ ...payload, source: 'ocr' });
        saved += 1;
      } catch (err) { failed = err.message || 'Could not save a question'; }
    }
    if (saved) await loadQuestions();
    if (failed) toast(`${saved} added, but one failed: ${failed}`, 'error');
    else { toast(`${saved} scanned question${saved === 1 ? '' : 's'} added`, 'success'); close(); }
    button.disabled = false;
  };
}

function fileToBase64(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = () => resolve(reader.result);
    reader.onerror = reject;
    reader.readAsDataURL(file);
  });
}

function prepareStoredImage(file) {
  const maxBytes = 8 * 1024 * 1024;
  const maxDimension = 5000;
  const targetDimension = 1800;
  if (!file.type.startsWith('image/')) throw new Error('Choose an image file');
  if (file.size > maxBytes) throw new Error('Image is too large. Choose an image under 8MB.');
  return new Promise((resolve, reject) => {
    const image = new Image();
    const reader = new FileReader();
    reader.onerror = () => reject(new Error('Could not read image'));
    reader.onload = () => {
      image.onerror = () => reject(new Error('Could not decode image'));
      image.onload = () => {
        if (image.width > maxDimension || image.height > maxDimension) {
          reject(new Error(`Image dimensions must be ${maxDimension}px or smaller`));
          return;
        }
        const scale = Math.min(1, targetDimension / Math.max(image.width, image.height));
        const canvas = document.createElement('canvas');
        canvas.width = Math.max(1, Math.round(image.width * scale));
        canvas.height = Math.max(1, Math.round(image.height * scale));
        canvas.getContext('2d').drawImage(image, 0, 0, canvas.width, canvas.height);
        resolve(canvas.toDataURL('image/jpeg', 0.84));
      };
      image.src = reader.result;
    };
    reader.readAsDataURL(file);
  });
}

function prepareOcrImage(file, onProgress = () => {}) {
  const maxBytes = 12 * 1024 * 1024;
  const maxDimension = 6000;
  const targetDimension = 2400;
  if (!file.type.startsWith('image/')) throw new Error('Choose an image file for OCR');
  if (file.size > maxBytes) throw new Error('Image is too large. Choose an image under 12MB.');
  onProgress('Reading image…');
  return new Promise((resolve, reject) => {
    const image = new Image();
    const reader = new FileReader();
    reader.onerror = () => reject(new Error('Could not read the image'));
    reader.onload = () => {
      image.onerror = () => reject(new Error('Could not decode the image'));
      image.onload = () => {
        if (!image.width || !image.height || image.width > maxDimension || image.height > maxDimension) {
          if (image.width > maxDimension || image.height > maxDimension) {
            reject(new Error(`Image dimensions must be ${maxDimension}px or smaller`));
            return;
          }
        }
        onProgress('Preparing image…');
        const scale = Math.min(1, targetDimension / Math.max(image.width, image.height));
        const canvas = document.createElement('canvas');
        canvas.width = Math.max(1, Math.round(image.width * scale));
        canvas.height = Math.max(1, Math.round(image.height * scale));
        canvas.getContext('2d').drawImage(image, 0, 0, canvas.width, canvas.height);
        resolve(canvas.toDataURL('image/jpeg', 0.82));
      };
      image.src = reader.result;
    };
    reader.readAsDataURL(file);
  });
}

async function deleteQuestion(id) {
  if (!id || !await confirmModal({ title: 'Delete question?', message: 'This question will be removed from your question bank.', confirmLabel: 'Delete', danger: true })) return;
  try {
    const question = questions.find((item) => String(item.id || item.offline_id) === String(id));
    await deleteOfflineQuestion(question?.offline_id || id);
    toast('Question deleted locally. It will be removed from the cloud on the next backup.', 'success');
    await loadQuestions();
  } catch (err) {
    toast(err.message || 'Delete failed', 'error');
  }
}

export async function renderQuestions() {
  const main = document.getElementById('main-content');
  main.innerHTML = `
    <div class="page-enter max-w-3xl mx-auto p-4 pb-24">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold">Questions</h2>
        <button id="btn-new-q" class="floating-action-button" title="Create a question" aria-label="Create a question">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        </button>
      </div>

      <!-- Filters -->
      <div class="flex gap-2 overflow-x-auto pb-2 mb-3 -mx-1 px-1">
        <select id="f-type" class="text-xs px-2.5 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 shrink-0">
          <option value="">All types</option>
          <option value="mcq">MCQ</option>
          <option value="fill">Fill gap</option>
          <option value="theory">Theory</option>
        </select>
        <select id="f-subject" class="text-xs px-2.5 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 shrink-0">
          <option value="">All subjects</option>
        </select>
        <select id="f-class" class="text-xs px-2.5 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 shrink-0">
          <option value="">All classes</option>
        </select>
        <input id="f-search" type="search" placeholder="Search…"
          class="text-xs px-2.5 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 w-28 shrink-0" />
      </div>

      <div id="offline-draft-status" class="hidden mb-4 rounded-xl border border-amber-200 bg-amber-50 p-3 dark:border-amber-800 dark:bg-amber-900/20"></div>

      <div id="q-list" class="space-y-3">
        <div class="loading-skeleton"></div>
        <div class="loading-skeleton"></div>
        <div class="loading-skeleton"></div>
      </div>
    </div>
  `;

  await loadMeta();

  // Populate filter dropdowns
  const subjSel = document.getElementById('f-subject');
  meta.subjects.forEach((s) => {
    subjSel.innerHTML += `<option value="${s.id}">${s.name}</option>`;
  });
  const classSel = document.getElementById('f-class');
  meta.classes.forEach((c) => {
    classSel.innerHTML += `<option value="${c.id}">${c.name}</option>`;
  });

  const applyFilters = () => {
    loadQuestions({
      type: document.getElementById('f-type').value,
      subject_id: document.getElementById('f-subject').value,
      class_id: document.getElementById('f-class').value,
      search: document.getElementById('f-search').value.trim(),
    });
  };

  ['f-type', 'f-subject', 'f-class'].forEach((id) => {
    document.getElementById(id)?.addEventListener('change', applyFilters);
  });
  let searchTimer;
  document.getElementById('f-search')?.addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(applyFilters, 300);
  });

  document.getElementById('btn-new-q')?.addEventListener('click', chooseQuestionCreation);
  window.addEventListener('gs-backup-status-refresh', renderOfflineDraftStatus);
  window.addEventListener('gs-questions-refresh', () => loadQuestions());
  await renderOfflineDraftStatus();

  await loadQuestions();
}
