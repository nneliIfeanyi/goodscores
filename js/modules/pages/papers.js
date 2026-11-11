import { api } from '../../utils/api.js';
import { loadMetaData } from '../../utils/meta.js';
import { toast } from '../../utils/toast.js';
import { confirmModal } from '../../utils/modal.js';
import { API_BASE, storageUrl, getUser } from '../../utils/api.js';
import { requireAuthentication } from '../../utils/authGate.js';
import { getQuestionBank } from '../../utils/questionBank.js';

let meta = { subjects: [], classes: [], terms: [] };
let allQuestions = [];
let papers = [];
let schoolWidePapers = false;

window.addEventListener('gs-papers-refresh', () => {
  if (document.getElementById('paper-list')) loadPapers();
});
window.addEventListener('online', () => {
  if (document.getElementById('paper-list')) loadPapers();
});
window.addEventListener('offline', () => {
  if (document.getElementById('paper-list')) {
    papers = [];
    renderList();
  }
});

const questionKey = (question) => String(question?.id ?? question?.offline_id ?? '');
const escapeHtml = (value) => String(value ?? '')
  .replace(/&/g, '&amp;')
  .replace(/</g, '&lt;')
  .replace(/>/g, '&gt;')
  .replace(/"/g, '&quot;')
  .replace(/'/g, '&#39;');

function hasOnlinePaperAccess(feature) {
  if (!localStorage.getItem('gs_token')) {
    requireAuthentication(feature, () => renderPapers());
    return false;
  }
  if (!navigator.onLine) {
    toast(`${feature} requires an internet connection.`, 'warn');
    return false;
  }
  return true;
}

function isTheoryQuestion(question) {
  return question?.type === 'theory';
}

const sectionTypes = [
  ['objective', 'Objective'],
  ['fill', 'Fill-in-the-gap'],
  ['theory', 'Theory'],
  ['prose', 'Prose'],
  ['poetry', 'Poetry'],
  ['drama', 'Drama'],
  ['comprehension', 'Comprehension'],
  ['custom', 'Custom'],
];

async function loadMeta() {
  meta = await loadMetaData();
}

async function loadPassages() {
  const response = await api('/passages');
  if (!Array.isArray(response.data)) throw new Error('The server returned an invalid passage list');
  return response.data;
}

async function loadPapers(schoolWide = schoolWidePapers) {
  schoolWidePapers = schoolWide;
  if (!navigator.onLine || !localStorage.getItem('gs_token')) {
    papers = [];
    renderList();
    return;
  }
  try {
    const response = await api(schoolWide ? '/school/papers' : '/papers');
    if (!Array.isArray(response.data)) throw new Error('The server returned an invalid paper list');
    papers = response.data;
  } catch (error) {
    papers = [];
    toast(error.message || 'Could not load papers from the server', 'error');
  }
  renderList();
}

async function exportPaper(id, button = null, schoolWide = false) {
  const ownerOverlay = button?.closest('.fixed.inset-0');
  if (!hasOnlinePaperAccess('PDF export')) {
    ownerOverlay?.remove();
    return;
  }
  if (!/^[1-9]\d*$/.test(String(id || ''))) {
    toast('Save this paper online before exporting it.', 'warn');
    return;
  }
  const originalLabel = button?.textContent || 'Export PDF';
  if (button) {
    button.disabled = true;
    button.textContent = 'Generating…';
  }
  try {
    const endpoint = schoolWide ? `/school/papers/${id}/export` : `/papers/${id}/export`;
    const exp = await api(endpoint, { method: 'POST', body: '{}' });
    const url = exp.data?.download_url;
    if (url) {
      const full = url.startsWith('http')
        ? url
        : (url.startsWith('/storage/') ? `${API_BASE}${url}` : storageUrl(url));
      window.open(full, '_blank');
      toast(exp.data?.fallback ? 'Paper ready. Use the browser print dialog to save it as PDF.' : 'PDF ready', 'success');
      if (exp.data?.credits_left !== undefined) toast(`Credits left: ${exp.data.credits_left}`, 'info');
    } else {
      toast(exp.data?.message || 'Export complete', 'success');
    }
  } catch (err) {
    ownerOverlay?.remove();
    toast(err.message || 'Export failed', 'error');
  } finally {
    if (button) {
      button.disabled = false;
      button.textContent = originalLabel;
    }
  }
}

async function exportPaperWord(id, button = null, schoolWide = false) {
  const ownerOverlay = button?.closest('.fixed.inset-0');
  if (!hasOnlinePaperAccess('Word export')) return;
  if (!/^[1-9]\d*$/.test(String(id || ''))) {
    toast('Save this paper online before exporting it to Word.', 'warn');
    return;
  }

  const originalLabel = button?.textContent || 'Word · 45';
  if (button) {
    button.disabled = true;
    button.textContent = 'Generating…';
  }
  try {
    const endpoint = schoolWide ? `/school/papers/${id}/export/word` : `/papers/${id}/export/word`;
    const result = await api(endpoint, {
      method: 'POST',
      body: '{}',
      timeoutMs: 60000,
    });
    const url = result.data?.download_url;
    if (!url) throw new Error('Word document was not generated');
    const full = url.startsWith('http')
      ? url
      : (url.startsWith('/storage/') ? `${API_BASE}${url}` : storageUrl(url));
    const downloadWindow = window.open(full, '_blank');
    if (downloadWindow) downloadWindow.opener = null;
    else window.location.assign(full);
    toast('Editable Word document ready. 45 credits used.', 'success');
    if (result.data?.credits_left !== undefined) toast(`Credits left: ${result.data.credits_left}`, 'info');
  } catch (err) {
    toast(err.message || 'Word export failed', 'error');
  } finally {
    if (button) {
      button.disabled = false;
      button.textContent = originalLabel;
    }
    if (ownerOverlay?.isConnected) ownerOverlay.remove();
  }
}

function renderList() {
  const el = document.getElementById('paper-list');
  if (!el) return;
  const schoolWide = schoolWidePapers;

  if (!papers.length) {
    el.innerHTML = `
      <div class="text-center py-12 text-gray-500 dark:text-gray-400">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="font-medium">${navigator.onLine ? (schoolWide ? 'No school papers yet' : 'No exam papers yet') : 'Connect to view and manage papers'}</p>
        <p class="text-sm mt-1">${navigator.onLine ? (schoolWide ? 'Build a paper or ask a teacher to create one' : 'Build a paper from your questions') : 'Paper building and exports are online-only.'}</p>
      </div>`;
    return;
  }

  el.innerHTML = papers.map((p) => `
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 cursor-pointer hover:border-primary-300 transition" data-paper-card data-id="${escapeHtml(p.id)}">
      <div class="flex items-start justify-between gap-2">
        <div>
          <h3 class="font-medium text-sm">${escapeHtml(p.title)}</h3>
          <p class="text-xs text-gray-500 mt-0.5">
            ${escapeHtml(p.subject_name || '')} ${p.class_name ? '· ' + escapeHtml(p.class_name) : ''} ${p.term_name ? '· ' + escapeHtml(p.term_name) : ''}
          </p>
          <p class="text-xs text-gray-400 mt-1">${p.questions ? p.questions.length : (p.question_count ?? 0)} questions · ${escapeHtml(p.total_marks || 0)} marks · ${escapeHtml(p.status)}</p>
          ${schoolWide ? `<p class="text-xs text-gray-500 mt-1">Created by ${escapeHtml(p.author_name || 'Teacher')}${p.editor_name ? ` · Last edited by ${escapeHtml(p.editor_name)}` : ''}</p>` : ''}
        </div>
        <div class="flex gap-1">
          <button class="btn-edit-paper p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-sm" data-id="${p.id}">Edit</button>
          <button class="btn-export-paper p-2 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/30 text-primary-700 dark:text-primary-300 text-sm" data-id="${p.id}" title="Export PDF">PDF</button>
          <button class="btn-export-word p-2 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/30 text-primary-700 dark:text-primary-300 text-sm disabled:opacity-40" data-id="${p.id}" title="Export editable Word document (45 credits)">Word · 45</button>
          <button class="btn-del-paper p-2 rounded-lg hover:bg-red-50 text-red-500" data-id="${escapeHtml(p.id)}" ${schoolWide ? 'hidden' : ''}>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
          </button>
        </div>
      </div>
    </div>
  `).join('');

  el.querySelectorAll('[data-paper-card]').forEach((card) => {
    card.addEventListener('click', () => viewPaper(card.dataset.id, schoolWide));
  });
  el.querySelectorAll('.btn-edit-paper').forEach((b) => {
    b.addEventListener('click', async (event) => {
      event.stopPropagation();
      try {
        openBuilder(b.dataset.id, schoolWide);
      } catch (err) {
        toast(err.message || 'Could not open paper', 'error');
      }
    });
  });
  el.querySelectorAll('.btn-export-paper').forEach((b) => {
    b.addEventListener('click', (event) => {
      event.stopPropagation();
      exportPaper(b.dataset.id, b, schoolWide);
    });
  });
  el.querySelectorAll('.btn-export-word').forEach((b) => {
    b.addEventListener('click', (event) => {
      event.stopPropagation();
      exportPaperWord(b.dataset.id, b, schoolWide);
    });
  });
  el.querySelectorAll('.btn-del-paper').forEach((b) => {
    b.addEventListener('click', async (event) => {
      event.stopPropagation();
      if (!await confirmModal({ title: 'Delete paper?', message: 'This paper will be deleted permanently.', confirmLabel: 'Delete', danger: true })) return;
      if (!hasOnlinePaperAccess('Paper deletion')) return;
      try {
        await api(`/papers/${b.dataset.id}`, { method: 'DELETE' });
        toast('Paper deleted', 'success');
        await loadPapers();
      } catch (error) {
        toast(error.message || 'Could not delete paper', 'error');
      }
    });
  });
}

async function openBuilder(paperOrId = null, schoolWide = false) {
  if (!hasOnlinePaperAccess('Paper building')) return;
  document.getElementById('paper-builder')?.remove();

  const paperId = typeof paperOrId === 'object' ? paperOrId?.id : paperOrId;
  let existingPaper = null;
  try {
    if (paperId) {
      const endpoint = schoolWide ? `/school/papers/${paperId}` : `/papers/${paperId}`;
      const response = await api(endpoint);
      existingPaper = response.data;
      if (!existingPaper) throw new Error('Paper not found');
    }
    allQuestions = await getQuestionBank({}, { onlineOnly: true, serverOnly: true });
    allQuestions = mergeExistingPaperQuestions(allQuestions, {});
  } catch (error) {
    toast(error.message || 'Could not open the online paper builder', 'error');
    return;
  }

  const paperMeta = schoolWide && existingPaper?.school_meta
    ? existingPaper.school_meta
    : meta;

  let passages;
  try {
    passages = await loadPassages();
  } catch (error) {
    passages = [];
    toast(`${error.message || 'Could not load passages'}. You can still build a paper without passages.`, 'warn');
  }

  const selectedQuestionIds = new Set((existingPaper?.questions || []).map(questionKey));
  function mergeExistingPaperQuestions(questions, filters) {
    if (!schoolWide || !existingPaper?.questions?.length) return questions;
    const included = new Set(questions.map(questionKey));
    const currentQuestions = existingPaper.questions.filter((question) => {
      const matchesFilters = ['subject_id', 'class_id', 'term_id']
        .every((field) => !filters[field] || String(question[field] || '') === String(filters[field]));
      return matchesFilters && !included.has(questionKey(question));
    });
    return [...questions, ...currentQuestions];
  }

  const savedSections = existingPaper?.paper_settings?.sections || [];
  const sections = savedSections.length
    ? savedSections.map((section, index) => ({
        key: section.key || String.fromCharCode(65 + index),
        type: section.type || (section.key === 'B' ? 'theory' : section.key === 'C' ? 'fill' : 'objective'),
        title: section.title || `Section ${String.fromCharCode(65 + index)}`,
        instructions: section.instructions || '',
        passage_id: section.passage_id || null,
        passage: section.passage || null,
        question_ids: (section.question_ids || []).map(String),
      }))
    : [
        {
          key: 'A',
          type: 'objective',
          title: 'Section A: Objective & Fill-in-the-gap Questions',
          instructions: 'Answer all questions. Choose the correct option or complete the gap.',
          passage_id: null,
          passage: null,
          question_ids: [],
        },
        {
          key: 'B',
          type: 'theory',
          title: 'Section B: Theory Questions',
          instructions: 'Answer any three questions.',
          passage_id: null,
          passage: null,
          question_ids: [],
        },
      ];

  if (!savedSections.length) {
    (existingPaper?.questions || []).forEach((question) => {
      const target = isTheoryQuestion(question) ? sections[1] : sections[0];
      target.question_ids.push(questionKey(question));
    });
  }

  const overlay = document.createElement('div');
  overlay.id = 'paper-builder';
  overlay.className = 'fixed inset-0 z-50 bg-black/40 flex items-end sm:items-center justify-center';
  overlay.innerHTML = `
    <div class="pb-builder-dialog bg-white dark:bg-gray-900 w-full max-h-[94vh] overflow-y-auto rounded-t-2xl sm:rounded-2xl shadow-xl">
      <div class="sticky top-0 z-10 bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 px-4 py-3 flex items-center justify-between">
        <h3 class="font-semibold">${existingPaper ? 'Edit Exam Paper' : 'Build Exam Paper'}</h3>
        <button id="pb-close" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
      <form id="pb-form" class="p-3 sm:p-4 pb-24 lg:pb-4 space-y-4" novalidate>
        <nav class="pb-mobile-stepper" aria-label="Paper builder steps">
          ${['Details', 'Questions', 'Review'].map((label, index) => `<button type="button" data-pb-go-step="${index}" aria-current="${index === 0 ? 'step' : 'false'}"><span>${index + 1}</span>${label}</button>`).join('')}
        </nav>
        <div class="pb-step-layout">
          <div class="space-y-4">
            <section data-pb-step-panel="0" class="pb-step-panel is-active rounded-2xl border border-gray-100 dark:border-gray-800 p-4 space-y-4">
              <div>
                <h4 class="text-sm font-semibold">Paper details</h4>
                <p class="text-xs text-gray-500 mt-1">Set the basics first. Your title can be edited at any time.</p>
              </div>
              <div>
                <label class="block text-xs font-medium mb-1" for="pb-title">Paper title</label>
                <input id="pb-title" name="title" value="${existingPaper?.title || ''}" placeholder="e.g. Biology – SS2 1st Term Exam"
                  class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm" />
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <div>
                  <label class="block text-xs font-medium mb-1">Subject</label>
                  <select name="subject_id" class="w-full px-2 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm">
                    <option value="">${paperMeta.subjects.length ? '—' : 'Add a subject in Account settings'}</option>
                    ${existingPaper?.subject_id && !paperMeta.subjects.some((subject) => String(subject.id) === String(existingPaper.subject_id)) ? `<option value="${escapeHtml(existingPaper.subject_id)}" selected>${escapeHtml(existingPaper.subject_name || 'Current subject')}</option>` : ''}
                    ${paperMeta.subjects.map((s) => `<option value="${s.id}" ${existingPaper?.subject_id == s.id ? 'selected' : ''}>${escapeHtml(s.name)}</option>`).join('')}
                  </select>
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1">Class</label>
                  <select name="class_id" class="w-full px-2 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm">
                    <option value="">${paperMeta.classes.length ? '—' : 'Add a class in Account settings'}</option>
                    ${existingPaper?.class_id && !paperMeta.classes.some((classItem) => String(classItem.id) === String(existingPaper.class_id)) ? `<option value="${escapeHtml(existingPaper.class_id)}" selected>${escapeHtml(existingPaper.class_name || 'Current class')}</option>` : ''}
                    ${paperMeta.classes.map((c) => `<option value="${c.id}" ${existingPaper?.class_id == c.id ? 'selected' : ''}>${escapeHtml(c.name)}</option>`).join('')}
                  </select>
                </div>
                <div>
                  <label class="block text-xs font-medium mb-1">Term</label>
                  <select name="term_id" class="w-full px-2 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm">
                    <option value="">—</option>
                    ${existingPaper?.term_id && !paperMeta.terms.some((term) => String(term.id) === String(existingPaper.term_id)) ? `<option value="${escapeHtml(existingPaper.term_id)}" selected>${escapeHtml(existingPaper.term_name || 'Current term')}</option>` : ''}
                    ${paperMeta.terms.map((t) => `<option value="${t.id}" ${existingPaper?.term_id == t.id ? 'selected' : ''}>${escapeHtml(t.name)}</option>`).join('')}
                  </select>
                </div>
              </div>
              ${(!paperMeta.subjects.length || !paperMeta.classes.length) ? `<div class="rounded-xl border border-amber-200 bg-amber-50 dark:border-amber-800 dark:bg-amber-900/20 p-3 text-xs text-amber-800 dark:text-amber-200">
                Add your own ${!paperMeta.subjects.length ? 'subjects' : ''}${!paperMeta.subjects.length && !paperMeta.classes.length ? ' and ' : ''}${!paperMeta.classes.length ? 'classes' : ''} in Account settings before setting paper details.
                <button type="button" id="pb-open-account" class="ml-1 font-semibold underline">Open Account settings</button>
              </div>` : ''}
              <details class="rounded-xl border border-gray-100 dark:border-gray-800 p-3">
                <summary class="cursor-pointer text-xs font-semibold">More options</summary>
                <label class="block text-xs font-medium mt-3 mb-1" for="pb-time-allowed">Time allowed</label>
                <input id="pb-time-allowed" name="time_allowed" value="${existingPaper?.paper_settings?.time_allowed || ''}" placeholder="e.g. 1 hour 30 minutes"
                  class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm" />
              </details>
            </section>

            <section data-pb-step-panel="1" class="pb-step-panel rounded-2xl border border-gray-100 dark:border-gray-800 p-4 space-y-3">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <h4 class="text-sm font-semibold">Choose questions</h4>
                  <p class="text-xs text-gray-500 mt-1">MCQs and fill-in-the-gap questions go to Section A; theory goes to Section B. Add sections in Review when needed.</p>
                </div>
                <label class="inline-flex items-center gap-1.5 text-xs text-primary-600 dark:text-primary-400 cursor-pointer shrink-0">
                  <input id="pb-check-all" type="checkbox" class="rounded border-gray-300 text-primary-600" />
                  Select visible
                </label>
              </div>
              <div id="pb-selection-summary" class="rounded-lg bg-primary-50 dark:bg-primary-900/20 px-3 py-2 text-xs font-medium text-primary-800 dark:text-primary-200" aria-live="polite"></div>
              <input id="pb-search" type="search" placeholder="Search questions…" aria-label="Search questions"
                class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm" />
              <div id="pb-q-status" class="text-xs text-gray-400"></div>
              <div id="pb-q-list" class="space-y-2 max-h-[45vh] overflow-y-auto border border-gray-100 dark:border-gray-700 rounded-xl p-2"></div>
            </section>
          </div>

          <section data-pb-step-panel="2" class="pb-step-panel rounded-2xl border border-gray-100 dark:border-gray-800 p-4 space-y-4">
            <div>
              <h4 class="text-sm font-semibold">Review and arrange</h4>
              <p class="text-xs text-gray-500 mt-1">Check totals, organize sections, and review the paper outline before saving.</p>
            </div>
            <div class="rounded-xl bg-gray-50 dark:bg-gray-800 p-3">
              <p id="pb-preview-title" class="font-semibold text-sm"></p>
              <p id="pb-preview-details" class="text-xs text-gray-500 mt-1"></p>
              <p id="pb-preview-count" class="text-xs font-medium mt-2"></p>
              <div id="pb-preview-sections" class="mt-2 space-y-1 text-xs"></div>
            </div>
            <div>
              <div class="flex items-center justify-between mb-2">
                <div>
                  <p class="text-sm font-semibold">Sections</p>
                  <p class="text-[11px] text-gray-400 mt-1">Reorder, rename, remove, or add sections and passages.</p>
                </div>
                <button id="pb-add-section" type="button" class="px-2.5 py-1.5 rounded-lg border border-primary-200 text-primary-700 dark:text-primary-300 text-xs font-semibold">Add section</button>
              </div>
              <div id="pb-sections" class="space-y-3"></div>
            </div>
            <div>
              <p class="text-xs font-semibold mb-2">Assign selected questions</p>
              <p class="text-[11px] text-gray-400 mb-2">Drag within a section or use the arrows to change numbering. Use the menu to move a question to another section.</p>
              <div id="pb-assignments" class="space-y-2"></div>
            </div>
            <div>
              <div class="flex items-center justify-between mb-1">
                <label class="text-xs font-medium">Theory question numbering</label>
                <span class="text-[11px] text-gray-400">Use 1(a) or 1(b)(i)</span>
              </div>
              <div id="pb-structure-list" class="space-y-2 border border-gray-100 dark:border-gray-700 rounded-xl p-2"></div>
            </div>
            <button type="submit" data-pb-save class="pb-desktop-save w-full py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold text-sm">
              ${existingPaper ? 'Save paper changes' : 'Save paper draft'}
            </button>
          </section>
        </div>

        <div id="pb-error" class="hidden text-sm text-red-600" role="alert"></div>
        <div class="pb-mobile-nav">
          <button type="button" data-pb-back class="px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-sm font-medium">Back</button>
          <p id="pb-mobile-step-label" class="text-xs text-gray-500 text-center"></p>
          <button type="button" data-pb-next class="px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-semibold">Continue</button>
          <button type="submit" data-pb-save class="pb-mobile-save hidden px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-semibold">
            ${existingPaper ? 'Save changes' : 'Save draft'}
          </button>
        </div>
      </form>
    </div>
  `;

  document.body.appendChild(overlay);
  overlay.querySelector('#pb-open-account')?.addEventListener('click', () => {
    overlay.remove();
    document.querySelector('[data-page="account"]')?.click();
  });
  overlay.querySelector('#pb-close').onclick = () => overlay.remove();

  const form = document.getElementById('pb-form');
  const titleInput = document.getElementById('pb-title');
  const titleWasProvided = Boolean(existingPaper?.title);
  let titleWasEdited = titleWasProvided;
  titleInput?.addEventListener('input', () => { titleWasEdited = true; });
  const updatePaperTitle = () => {
    if (titleWasEdited) return;
    const selectedTitleParts = ['subject_id', 'class_id', 'term_id']
      .map((name) => form.elements[name]?.selectedOptions[0]?.textContent.trim())
      .filter((label) => label && label !== '—');
    titleInput.value = selectedTitleParts.length ? `${selectedTitleParts.join(' - ')} Exam` : '';
    updateBuilderSummary();
  };
  ['subject_id', 'class_id', 'term_id'].forEach((name) => {
    form.elements[name]?.addEventListener('change', () => {
      updatePaperTitle();
      updateBuilderSummary();
    });
  });
  const questionList = document.getElementById('pb-q-list');
  const structureList = document.getElementById('pb-structure-list');
  const sectionsList = document.getElementById('pb-sections');
  const assignmentsList = document.getElementById('pb-assignments');
  const savedLayout = existingPaper?.paper_settings?.question_layout || [];
  const structureState = new Map(savedLayout.map((item) => [String(item.question_id), {
    level: Number.isInteger(item.level) ? item.level : 0,
  }]));
  const questionStatus = document.getElementById('pb-q-status');
  const checkAll = document.getElementById('pb-check-all');
  const searchInput = document.getElementById('pb-search');
  const selectionSummary = document.getElementById('pb-selection-summary');
  const previewTitle = document.getElementById('pb-preview-title');
  const previewDetails = document.getElementById('pb-preview-details');
  const previewCount = document.getElementById('pb-preview-count');
  const previewSections = document.getElementById('pb-preview-sections');
  const knownQuestions = new Map();
  const rememberQuestions = (questions) => {
    (questions || []).forEach((question) => knownQuestions.set(questionKey(question), question));
  };
  rememberQuestions(allQuestions);
  rememberQuestions(existingPaper?.questions);
  const stepPanels = [...overlay.querySelectorAll('[data-pb-step-panel]')];
  const stepButtons = [...overlay.querySelectorAll('[data-pb-go-step]')];
  const backButton = overlay.querySelector('[data-pb-back]');
  const nextButton = overlay.querySelector('[data-pb-next]');
  const mobileSaveButton = overlay.querySelector('.pb-mobile-save');
  const mobileStepLabel = document.getElementById('pb-mobile-step-label');
  const stepNames = ['Details', 'Questions', 'Review'];
  let activeStep = 0;

  const clearBuilderError = () => {
    const error = document.getElementById('pb-error');
    error.textContent = '';
    error.classList.add('hidden');
  };
  const showBuilderError = (message) => {
    const error = document.getElementById('pb-error');
    error.textContent = message;
    error.classList.remove('hidden');
  };
  const setBuilderStep = (step) => {
    activeStep = Math.max(0, Math.min(stepNames.length - 1, step));
    stepPanels.forEach((panel, index) => panel.classList.toggle('is-active', index === activeStep));
    stepButtons.forEach((button, index) => button.setAttribute('aria-current', index === activeStep ? 'step' : 'false'));
    backButton.hidden = activeStep === 0;
    nextButton.hidden = activeStep === stepNames.length - 1;
    mobileSaveButton.classList.toggle('hidden', activeStep !== stepNames.length - 1);
    mobileStepLabel.textContent = `Step ${activeStep + 1} of ${stepNames.length}: ${stepNames[activeStep]}`;
    clearBuilderError();
  };

  stepButtons.forEach((button) => button.addEventListener('click', () => setBuilderStep(Number(button.dataset.pbGoStep))));
  backButton.addEventListener('click', () => setBuilderStep(activeStep - 1));
  nextButton.addEventListener('click', () => {
    if (activeStep === 0 && !titleInput.value.trim()) {
      showBuilderError('Enter a paper title or choose a subject, class, or term to create one.');
      titleInput.focus();
      return;
    }
    setBuilderStep(activeStep + 1);
  });

  const sectionForQuestion = (questionId) => sections.find((section) => section.question_ids.includes(String(questionId)));
  const visibleQuestions = () => {
    const query = searchInput.value.trim().toLowerCase();
    return query
      ? allQuestions.filter((question) => `${question.body || ''} ${question.subject_name || ''} ${question.type || ''}`.toLowerCase().includes(query))
      : allQuestions;
  };
  const defaultSectionForQuestion = (question) => {
    const isTheory = isTheoryQuestion(question);
    return sections.find((section) => section.type === (isTheory ? 'theory' : 'objective'))
      || sections[isTheory ? 1 : 0]
      || sections[0];
  };
  const updateBuilderSummary = () => {
    const selected = [...selectedQuestionIds].map((id) => knownQuestions.get(id)).filter(Boolean);
    const totalMarks = selected.reduce((sum, question) => sum + Number(question.marks || 1), 0);
    const marksLabel = Number.isInteger(totalMarks) ? totalMarks : totalMarks.toFixed(1);
    const summary = `${selectedQuestionIds.size} question${selectedQuestionIds.size === 1 ? '' : 's'} selected · ${marksLabel} mark${Number(totalMarks) === 1 ? '' : 's'}`;
    selectionSummary.textContent = summary;
    previewTitle.textContent = titleInput.value.trim() || 'Untitled paper';
    previewDetails.textContent = ['subject_id', 'class_id', 'term_id']
      .map((name) => form.elements[name]?.selectedOptions[0]?.textContent.trim())
      .filter((value) => value && value !== '—')
      .join(' · ') || 'Paper details not set';
    previewCount.textContent = summary;
    previewSections.replaceChildren(...sections.map((section) => {
      const sectionQuestions = section.question_ids
        .map((id) => knownQuestions.get(String(id)))
        .filter(Boolean);
      const sectionMarks = sectionQuestions.reduce((sum, question) => sum + Number(question.marks || 1), 0);
      const line = document.createElement('p');
      line.className = 'text-gray-600 dark:text-gray-300';
      line.textContent = `${section.key || ''} · ${section.title || 'Untitled section'} · ${section.question_ids.length} questions · ${sectionMarks} marks`;
      return line;
    }));
  };
  titleInput?.addEventListener('input', updateBuilderSummary);
  const renderAssignments = () => {
    const orderedIds = sections.flatMap((section) => section.question_ids.map(String));
    const ids = [...orderedIds, ...selectedQuestionIds].filter((id, index, list) => selectedQuestionIds.has(id) && list.indexOf(id) === index);
    if (!ids.length) {
      assignmentsList.innerHTML = '<p class="text-xs text-gray-400">Choose questions to assign them to sections.</p>';
      updateBuilderSummary();
      return;
    }
    assignmentsList.innerHTML = ids.map((id) => {
      const question = knownQuestions.get(id);
      const currentSection = sectionForQuestion(id);
      const orderIndex = currentSection?.question_ids.findIndex((questionId) => String(questionId) === id) ?? -1;
      const body = (question?.body || 'Selected question').replace(/<[^>]+>/g, '').slice(0, 100);
      return `<div class="grid grid-cols-1 sm:grid-cols-[1fr_12rem] gap-2 items-center rounded-lg border border-gray-100 dark:border-gray-800 p-2 pb-question-order-item"
          data-review-question="${escapeHtml(id)}" data-review-section="${escapeHtml(currentSection?.key || '')}">
        <div class="flex min-w-0 items-center gap-2">
          <div class="flex shrink-0 flex-col items-center">
            <button type="button" data-question-move="-1" aria-label="Move question up" title="Move question up" ${orderIndex <= 0 ? 'disabled' : ''} class="px-1 text-xs text-gray-500 disabled:opacity-30">↑</button>
            <span data-question-drag-handle title="Drag to reorder question" aria-hidden="true" class="cursor-grab px-1 text-gray-400">⋮⋮</span>
            <button type="button" data-question-move="1" aria-label="Move question down" title="Move question down" ${orderIndex < 0 || orderIndex >= currentSection.question_ids.length - 1 ? 'disabled' : ''} class="px-1 text-xs text-gray-500 disabled:opacity-30">↓</button>
          </div>
          <div class="min-w-0">
          <span class="text-[10px] font-semibold text-primary-600">${orderIndex >= 0 ? `${orderIndex + 1}.` : '—'}</span>
          <p class="text-xs truncate">${escapeHtml(body)}</p>
          <p class="text-[10px] text-gray-400">${question ? `${escapeHtml(question.type)} · ${escapeHtml(question.marks || 1)} marks` : 'Question details unavailable'}</p>
          </div>
        </div>
        <select data-review-section aria-label="Assign question to section" class="w-full px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-xs">
          ${sections.map((section) => `<option value="${escapeHtml(section.key)}" ${currentSection?.key === section.key ? 'selected' : ''}>Section ${escapeHtml(section.key)} · ${escapeHtml(section.title)}</option>`).join('')}
        </select>
      </div>`;
    }).join('');
    assignmentsList.querySelectorAll('[data-review-section]').forEach((select) => {
      select.addEventListener('change', () => {
        const id = String(select.closest('[data-review-question]')?.dataset.reviewQuestion || '');
        sections.forEach((section) => { section.question_ids = section.question_ids.filter((questionId) => String(questionId) !== id); });
        sections.find((section) => section.key === select.value)?.question_ids.push(id);
        renderSections();
        renderQuestions();
        renderStructure();
      });
    });
    let pointerDrag = null;
    const clearDropTargets = () => {
      assignmentsList.querySelectorAll('.is-drop-target').forEach((targetRow) => targetRow.classList.remove('is-drop-target'));
    };
    assignmentsList.querySelectorAll('[data-review-question]').forEach((row) => {
      const dragHandle = row.querySelector('[data-question-drag-handle]');
      dragHandle.addEventListener('pointerdown', (event) => {
        if (event.button !== 0 || event.isPrimary === false) return;
        event.preventDefault();
        pointerDrag = { questionId: String(row.dataset.reviewQuestion), pointerId: event.pointerId };
        dragHandle.setPointerCapture(event.pointerId);
        row.classList.add('opacity-50');
      });
      dragHandle.addEventListener('pointermove', (event) => {
        if (!pointerDrag || pointerDrag.pointerId !== event.pointerId) return;
        const target = document.elementFromPoint(event.clientX, event.clientY)?.closest('[data-review-question]');
        clearDropTargets();
        if (!target || target === row || !sectionForQuestion(pointerDrag.questionId)
          || sectionForQuestion(pointerDrag.questionId) !== sectionForQuestion(target.dataset.reviewQuestion)) return;
        event.preventDefault();
        target.classList.add('is-drop-target');
      });
      const finishPointerDrag = (event) => {
        if (!pointerDrag || pointerDrag.pointerId !== event.pointerId) return;
        const questionId = pointerDrag.questionId;
        const target = document.elementFromPoint(event.clientX, event.clientY)?.closest('[data-review-question]');
        pointerDrag = null;
        row.classList.remove('opacity-50');
        clearDropTargets();
        const source = sectionForQuestion(questionId);
        const destination = target && sectionForQuestion(target.dataset.reviewQuestion);
        if (!source || source !== destination || questionId === target.dataset.reviewQuestion) return;
        const targetBox = target.getBoundingClientRect();
        const dropAfterTarget = event.clientY >= targetBox.top + targetBox.height / 2;
        source.question_ids = source.question_ids.filter((id) => String(id) !== questionId);
        const targetIndex = source.question_ids.findIndex((id) => String(id) === target.dataset.reviewQuestion);
        source.question_ids.splice(Math.max(0, targetIndex + (dropAfterTarget ? 1 : 0)), 0, questionId);
        renderSections();
        renderQuestions();
        renderStructure();
      };
      dragHandle.addEventListener('pointerup', finishPointerDrag);
      dragHandle.addEventListener('pointercancel', finishPointerDrag);
    });
    assignmentsList.querySelectorAll('[data-question-move]').forEach((button) => {
      button.addEventListener('click', () => {
        const id = String(button.closest('[data-review-question]')?.dataset.reviewQuestion || '');
        const section = sectionForQuestion(id);
        const index = section?.question_ids.findIndex((questionId) => String(questionId) === id) ?? -1;
        const nextIndex = index + Number(button.dataset.questionMove);
        if (!section || index < 0 || nextIndex < 0 || nextIndex >= section.question_ids.length) return;
        [section.question_ids[index], section.question_ids[nextIndex]] = [section.question_ids[nextIndex], section.question_ids[index]];
        renderSections();
        renderQuestions();
        renderStructure();
      });
    });
    updateBuilderSummary();
  };

  const renderSections = () => {
    sections.forEach((section, index) => {
      section.key = String.fromCharCode(65 + index);
    });
    sectionsList.innerHTML = sections.map((section, index) => `
      <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-3 space-y-2" data-section-index="${index}">
        <div class="flex items-center gap-2">
          <span class="text-xs font-bold text-primary-600">${section.key}</span>
          <input data-section-title value="${escapeHtml(section.title)}" class="min-w-0 flex-1 px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm" aria-label="Section title" />
          <button type="button" data-section-up aria-label="Move section up" class="p-1 text-xs ${index === 0 ? 'invisible' : ''}" title="Move section up">↑</button>
          <button type="button" data-section-down aria-label="Move section down" class="p-1 text-xs ${index === sections.length - 1 ? 'invisible' : ''}" title="Move section down">↓</button>
          <button type="button" data-section-remove aria-label="Remove section" class="p-1 text-xs text-red-500 ${sections.length <= 1 ? 'invisible' : ''}" title="Remove section">×</button>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <select data-section-type class="px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-xs">
            ${sectionTypes.map(([value, label]) => `<option value="${value}" ${section.type === value ? 'selected' : ''}>${label}</option>`).join('')}
          </select>
          <select data-section-passage class="px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-xs">
            <option value="">No passage attached</option>
            ${[...passages, ...(section.passage && !passages.some((passage) => String(passage.id) === String(section.passage.id)) ? [section.passage] : [])]
              .map((passage) => `<option value="${escapeHtml(passage.id)}" ${String(section.passage_id || '') === String(passage.id) ? 'selected' : ''}>${escapeHtml(passage.title)}</option>`).join('')}
          </select>
        </div>
        <textarea data-section-instructions rows="2" placeholder="Instructions, e.g. Answer any three questions" class="w-full px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-xs">${escapeHtml(section.instructions)}</textarea>
        <p class="text-[11px] text-gray-400">${section.question_ids.length} question${section.question_ids.length === 1 ? '' : 's'} assigned</p>
      </div>`).join('');

    sectionsList.querySelectorAll('[data-section-index]').forEach((card) => {
      const index = Number(card.dataset.sectionIndex);
      card.querySelector('[data-section-title]').addEventListener('input', (event) => {
        sections[index].title = event.target.value;
        updateBuilderSummary();
      });
      card.querySelector('[data-section-type]').addEventListener('change', (event) => { sections[index].type = event.target.value; });
      card.querySelector('[data-section-passage]').addEventListener('change', (event) => {
        sections[index].passage_id = event.target.value || null;
        sections[index].passage = passages.find((passage) => String(passage.id) === String(event.target.value)) || null;
      });
      card.querySelector('[data-section-instructions]').addEventListener('input', (event) => { sections[index].instructions = event.target.value; });
      card.querySelector('[data-section-up]').addEventListener('click', () => {
        [sections[index - 1], sections[index]] = [sections[index], sections[index - 1]];
        renderSections();
        renderQuestions();
      });
      card.querySelector('[data-section-down]').addEventListener('click', () => {
        [sections[index], sections[index + 1]] = [sections[index + 1], sections[index]];
        renderSections();
        renderQuestions();
      });
      card.querySelector('[data-section-remove]').addEventListener('click', () => {
        const removed = sections.splice(index, 1)[0];
        const fallback = sections[0];
        removed.question_ids.forEach((id) => fallback?.question_ids.push(id));
        renderSections();
        renderQuestions();
        renderStructure();
      });
    });
    renderAssignments();
  };

  document.getElementById('pb-add-section').addEventListener('click', () => {
    sections.push({ key: '', type: 'custom', title: `Section ${String.fromCharCode(65 + sections.length)}`, instructions: '', passage_id: null, passage: null, question_ids: [] });
    renderSections();
    renderQuestions();
  });

  const renderQuestions = () => {
    if (!allQuestions.length) {
      questionList.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">No questions match these selections.</p>';
      questionStatus.textContent = '0 questions found';
      checkAll.checked = false;
      checkAll.indeterminate = false;
      renderAssignments();
      updateBuilderSummary();
      return;
    }

    const visible = visibleQuestions();
    questionStatus.textContent = `${visible.length} of ${allQuestions.length} questions shown`;
    questionList.innerHTML = visible.length ? visible.map((q) => `
      <label data-question-id="${escapeHtml(questionKey(q))}" class="flex items-start gap-2 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
        <input type="checkbox" name="qid" value="${questionKey(q)}" class="mt-1 rounded border-gray-300 text-primary-600" ${selectedQuestionIds.has(questionKey(q)) ? 'checked' : ''} />
        <div class="min-w-0 flex-1">
          <span class="text-[10px] font-semibold uppercase text-primary-600">${escapeHtml(q.type)}</span>
          <p class="text-xs text-gray-700 dark:text-gray-300 line-clamp-2">${escapeHtml((q.body || '').replace(/<[^>]+>/g, '').slice(0, 80))}</p>
          <p class="text-[10px] text-gray-400">${escapeHtml(q.marks || 1)} mk · ${escapeHtml(q.subject_name || '')}</p>
        </div>
      </label>
    `).join('') : '<p class="text-sm text-gray-400 text-center py-4">No questions match your search.</p>';

    questionList.querySelectorAll('[name="qid"]').forEach((checkbox) => {
      checkbox.addEventListener('change', () => {
        const id = String(checkbox.value);
        if (checkbox.checked) {
          selectedQuestionIds.add(id);
          if (!sectionForQuestion(id)) defaultSectionForQuestion(knownQuestions.get(id))?.question_ids.push(id);
        } else {
          selectedQuestionIds.delete(id);
          sections.forEach((section) => { section.question_ids = section.question_ids.filter((questionId) => questionId !== id); });
        }
        updateCheckAll();
        renderSections();
        renderStructure();
      });
    });
    updateCheckAll();
    renderAssignments();
    updateBuilderSummary();
  };

  const updateCheckAll = () => {
    const visibleIds = visibleQuestions().map(questionKey);
    const selectedVisible = visibleIds.filter((id) => selectedQuestionIds.has(id)).length;
    checkAll.checked = visibleIds.length > 0 && selectedVisible === visibleIds.length;
    checkAll.indeterminate = selectedVisible > 0 && selectedVisible < visibleIds.length;
  };

  const renderStructure = () => {
    const orderedIds = sections.flatMap((section) => section.question_ids.map(String));
    const theoryQuestions = orderedIds
      .filter((id, index, ids) => selectedQuestionIds.has(id) && ids.indexOf(id) === index)
      .map((id) => knownQuestions.get(String(id)))
      .filter(isTheoryQuestion);
    if (!theoryQuestions.length) {
      structureList.innerHTML = '<p class="text-xs text-gray-400">Select theory questions to arrange their numbering.</p>';
      return;
    }

    let mainNumber = 0;
    let partNumber = 0;
    let subPartNumber = 0;
    let currentMain = 0;
    let currentPart = 0;
    const labels = new Map();
    theoryQuestions.forEach((q, index) => {
      const state = structureState.get(questionKey(q)) || { level: 0 };
      const level = Math.max(0, Math.min(2, state.level));
      if (level === 0) {
        mainNumber += 1;
        currentMain = mainNumber;
        const nextState = theoryQuestions[index + 1]
          ? structureState.get(questionKey(theoryQuestions[index + 1])) || { level: 0 }
          : { level: 0 };
        const hasParts = nextState.level > 0;
        partNumber = hasParts ? 1 : 0;
        subPartNumber = 0;
        currentPart = hasParts ? 1 : 0;
        labels.set(questionKey(q), hasParts ? `${currentMain}(a)` : String(currentMain));
      } else if (level === 1) {
        if (!currentMain) {
          mainNumber += 1;
          currentMain = mainNumber;
        }
        partNumber += 1;
        subPartNumber = 0;
        currentPart = partNumber;
        labels.set(questionKey(q), `${currentMain}(${String.fromCharCode(96 + currentPart)})`);
      } else {
        if (!currentMain) {
          mainNumber += 1;
          currentMain = mainNumber;
        }
        if (!currentPart) {
          partNumber += 1;
          currentPart = partNumber;
        }
        subPartNumber += 1;
        const roman = ['i', 'ii', 'iii', 'iv', 'v', 'vi', 'vii', 'viii', 'ix', 'x'][subPartNumber - 1] || String(subPartNumber);
        labels.set(questionKey(q), `${currentMain}(${String.fromCharCode(96 + currentPart)})(${roman})`);
      }
    });

    structureList.innerHTML = theoryQuestions.map((q) => {
      const level = Math.max(0, Math.min(2, structureState.get(questionKey(q))?.level || 0));
      const label = labels.get(questionKey(q));
      const id = questionKey(q);
      return `<div class="grid grid-cols-[1fr_5rem] gap-2 items-center">
        <div class="min-w-0">
          <p class="text-xs truncate">${escapeHtml((q.body || '').replace(/<[^>]+>/g, '').slice(0, 70))}</p>
          <span data-structure-label="${id}" class="mt-1 block text-xs font-semibold text-primary-600">${label}</span>
        </div>
        <select data-structure-level="${id}" aria-label="Question nesting level"
          class="px-1 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-xs">
          <option value="0" ${level === 0 ? 'selected' : ''}>Main</option>
          <option value="1" ${level === 1 ? 'selected' : ''}>Part</option>
          <option value="2" ${level === 2 ? 'selected' : ''}>Sub-part</option>
        </select>
      </div>`;
    }).join('');
    structureList.querySelectorAll('[data-structure-level]').forEach((select) => {
      select.addEventListener('change', () => {
        structureState.set(String(select.dataset.structureLevel), { level: Number(select.value) });
        renderStructure();
      });
    });
  };

  const loadFilteredQuestions = async () => {
    questionStatus.textContent = 'Loading questions…';
    questionStatus.classList.remove('hidden');
    questionList.innerHTML = '';
    try {
      const filters = {
        subject_id: form.subject_id.value,
        class_id: form.class_id.value,
        term_id: form.term_id.value,
      };
      allQuestions = await getQuestionBank(filters, { onlineOnly: true, serverOnly: true });
      allQuestions = mergeExistingPaperQuestions(allQuestions, filters);
      rememberQuestions(allQuestions);
    } catch (error) {
      questionStatus.textContent = error.message || 'Could not load server questions';
      questionStatus.classList.remove('hidden');
      return;
    }
    renderQuestions();
    renderStructure();
  };

  ['subject_id', 'class_id', 'term_id'].forEach((field) => {
    form[field].addEventListener('change', loadFilteredQuestions);
  });
  searchInput.addEventListener('input', () => {
    renderQuestions();
    updateCheckAll();
  });
  checkAll.addEventListener('change', () => {
    visibleQuestions().forEach((q) => {
      const id = questionKey(q);
      if (checkAll.checked) {
        selectedQuestionIds.add(id);
        if (!sectionForQuestion(id)) defaultSectionForQuestion(q)?.question_ids.push(id);
      } else {
        selectedQuestionIds.delete(id);
        sections.forEach((section) => { section.question_ids = section.question_ids.filter((questionId) => questionId !== id); });
      }
    });
    renderQuestions();
    renderSections();
    renderStructure();
  });
  renderQuestions();
  renderSections();
  renderStructure();

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    if (!form.title.value.trim()) {
      setBuilderStep(0);
      showBuilderError('Enter a paper title before saving');
      titleInput.focus();
      return;
    }
    const orderedIds = sections.flatMap((section) => section.question_ids.map(String));
    const qids = [...orderedIds, ...selectedQuestionIds].filter((id, index, ids) => selectedQuestionIds.has(id) && ids.indexOf(id) === index);
    if (!qids.length) {
      setBuilderStep(1);
      showBuilderError('Select at least one question before saving');
      return;
    }
    if (!hasOnlinePaperAccess('Saving a paper')) return;
    if (!qids.every((id) => /^[1-9]\d*$/.test(String(id)))) {
      setBuilderStep(1);
      showBuilderError('Only questions saved on the server can be added to a paper');
      return;
    }

    const theoryIds = qids.filter((id) => isTheoryQuestion(knownQuestions.get(String(id))));
    const questionLayout = theoryIds.map((questionId, index) => ({
      question_id: questionId,
      label: form.querySelector(`[data-structure-label="${questionId}"]`)?.textContent.trim() || String(index + 1),
      level: structureState.get(String(questionId))?.level || 0,
    }));

    const payload = {
      title: form.title.value.trim(),
      subject_id: form.subject_id.value || null,
      class_id: form.class_id.value || null,
      term_id: form.term_id.value || null,
      paper_settings: {
        ...(existingPaper?.paper_settings || {}),
        time_allowed: form.time_allowed.value.trim(),
        sections: sections.map((section, index) => ({
          key: String.fromCharCode(65 + index),
          type: section.type,
          title: section.title.trim() || `Section ${String.fromCharCode(65 + index)}`,
          instructions: section.instructions.trim(),
          passage_id: section.passage_id || null,
          question_ids: section.question_ids.filter((id) => qids.includes(String(id))),
        })).filter((section) => section.question_ids.length || section.title),
        question_layout: questionLayout,
      },
      question_ids: qids,
      status: 'draft',
    };

    const saveButtons = [...form.querySelectorAll('[data-pb-save]')];
    saveButtons.forEach((button) => { button.disabled = true; });
    try {
      const endpoint = existingPaper?.id
        ? (schoolWide ? `/school/papers/${existingPaper.id}` : `/papers/${existingPaper.id}`)
        : '/papers';
      const response = await api(endpoint, {
        method: existingPaper?.id ? 'PUT' : 'POST',
        body: JSON.stringify(payload),
      });
      if (!response.data?.id) throw new Error('The server did not confirm the paper save');
      overlay.remove();
      await loadPapers();
      await viewPaper(response.data.id, schoolWide);
    } catch (err) {
      showBuilderError(err.message || 'Paper save failed');
    } finally {
      saveButtons.forEach((button) => { button.disabled = false; });
    }
  });
}

async function viewPaper(id, schoolWide = false) {
  if (!hasOnlinePaperAccess('Viewing a paper')) return;
  try {
    const endpoint = schoolWide ? `/school/papers/${id}` : `/papers/${id}`;
    const response = await api(endpoint);
    const p = response.data;
    if (!p) throw new Error('Paper not found');
    let history = [];
    if (schoolWide) {
      const historyResponse = await api(`/school/papers/${id}/history`);
      if (!Array.isArray(historyResponse.data)) throw new Error('The server returned an invalid paper edit history');
      history = historyResponse.data;
    }
    const overlay = document.createElement('div');
    overlay.className = 'fixed inset-0 z-50 bg-black/40 flex items-end sm:items-center justify-center p-0 sm:p-4';
    overlay.innerHTML = `
      <div class="bg-white dark:bg-gray-900 w-full sm:max-w-lg max-h-[90vh] overflow-y-auto rounded-t-2xl sm:rounded-2xl shadow-xl p-4">
        <div class="flex justify-between items-start mb-4">
          <div>
            <h3 class="font-semibold">${escapeHtml(p.title)}</h3>
            <p class="text-xs text-gray-500">${escapeHtml(p.subject_name || '')} · ${escapeHtml(p.class_name || '')} · ${escapeHtml(p.total_marks || 0)} marks</p>
            ${schoolWide ? `<p class="text-xs text-gray-500 mt-1">Created by ${escapeHtml(p.author_name || 'Teacher')}${p.editor_name ? ` · Last edited by ${escapeHtml(p.editor_name)}` : ''}</p>` : ''}
          </div>
          <div class="flex items-center gap-1">
            <button class="px-2 py-1 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800" id="vp-edit">Edit</button>
            <button class="p-2 hover:bg-gray-100 rounded-lg" id="vp-close">✕</button>
          </div>
        </div>
        <div class="space-y-4 text-sm">
          ${((p.paper_settings?.sections?.length ? p.paper_settings.sections : [{ key: 'A', title: 'Questions', instructions: '', question_ids: (p.questions || []).map((q) => q.id) }])).map((section) => `
            <section>
              <h4 class="font-semibold border-b border-gray-200 dark:border-gray-700 pb-1">${section.title}</h4>
              ${section.instructions ? `<p class="text-xs text-gray-500 mt-1">${section.instructions}</p>` : ''}
              ${section.passage ? `<div class="my-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-3"><p class="font-semibold">${section.passage.title}</p><div class="mt-1 text-xs leading-5">${section.passage.body}</div></div>` : ''}
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-3 mt-3">
              ${(section.question_ids || []).map((qid, i) => {
                const q = (p.questions || []).find((item) => questionKey(item) === String(qid));
                if (!q) return '';
                const layout = isTheoryQuestion(q)
                  ? (p.paper_settings?.question_layout || []).find((item) => String(item.question_id) === String(qid))
                  : null;
                const label = layout?.label || String(i + 1);
                const indent = Math.max(0, Math.min(2, Number(layout?.level || 0))) * 18;
                return `
            <div class="border-b border-gray-100 dark:border-gray-800 pb-3" style="margin-left:${indent}px">
              <p class="font-medium">${label}. ${q.body}</p>
              ${q.type === 'mcq' && q.options ? `
                <ul class="mt-1 ml-4 text-gray-600 dark:text-gray-400">
                  ${q.options.map((o) => `<li>${o.key}. ${o.text}</li>`).join('')}
                </ul>
              ` : ''}
              ${(q.images || (q._pending_image ? [{ data_url: q._pending_image, original_name: 'Question image' }] : [])).length ? (q.images || (q._pending_image ? [{ data_url: q._pending_image, original_name: 'Question image' }] : [])).map((image) => `
                <figure class="mt-2">
                  <img src="${image.data_url || storageUrl(image.file_path)}" alt="${image.original_name || 'Question image'}" class="max-w-full max-h-56 rounded-lg border border-gray-200 dark:border-gray-700 object-contain" />
                  ${image.caption ? `<figcaption class="text-xs text-gray-500 mt-1">${image.caption}</figcaption>` : ''}
                </figure>
              `).join('') : ''}
            </div>
                `;
              }).join('')}
              </div>
            </section>
          `).join('')}
        </div>
        ${schoolWide ? `
          <section class="mt-4 rounded-xl border border-gray-100 dark:border-gray-800 p-3">
            <h4 class="text-xs font-semibold">School admin edit history</h4>
            ${history.length ? history.map((revision) => `
              <details class="mt-2 text-xs">
                <summary class="cursor-pointer text-gray-500">Version before admin edit ${escapeHtml(revision.revision_number)} · ${escapeHtml(revision.editor_name || 'School admin')} · ${escapeHtml(revision.created_at)}</summary>
                <p class="mt-1 text-gray-500">${escapeHtml(revision.snapshot?.title || 'Untitled paper')} · ${escapeHtml(revision.snapshot?.total_marks || 0)} marks · ${Array.isArray(revision.snapshot?.question_ids) ? revision.snapshot.question_ids.length : 0} questions</p>
              </details>
            `).join('') : '<p class="mt-2 text-xs text-gray-500">No admin edits recorded yet.</p>'}
          </section>
        ` : ''}
        <button id="btn-export-pdf" class="mt-4 w-full py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold text-sm">
          Export PDF
        </button>
        <button id="btn-export-word" class="mt-2 w-full py-2.5 rounded-xl border border-primary-300 text-primary-700 dark:text-primary-300 font-semibold text-sm ${p.id ? '' : 'opacity-50'}" ${p.id ? '' : 'disabled'}>
          Export Word document · 45 credits
        </button>
        <p class="mt-2 text-xs text-gray-400 text-center">Paper exports require an internet connection. PDF is free; Word costs 45 credits.</p>
      </div>
    `;
    document.body.appendChild(overlay);
    document.getElementById('vp-close').onclick = () => overlay.remove();
    document.getElementById('vp-edit').onclick = () => {
      overlay.remove();
      openBuilder(p, schoolWide);
    };

    document.getElementById('btn-export-pdf')?.addEventListener('click', (event) => {
      event.stopPropagation();
      exportPaper(id, document.getElementById('btn-export-pdf'), schoolWide);
    });
    document.getElementById('btn-export-word')?.addEventListener('click', (event) => {
      event.stopPropagation();
      exportPaperWord(p.id, document.getElementById('btn-export-word'), schoolWide);
    });
  } catch (err) {
    toast(err.message || 'Could not open paper', 'error');
  }
}

export async function renderPapers() {
  if (!localStorage.getItem('gs_token')) {
    requireAuthentication('Exam papers', () => renderPapers());
    return;
  }
  const user = getUser();
  schoolWidePapers = user?.role === 'school_admin' && Boolean(user.school_id);
  const main = document.getElementById('main-content');
  main.innerHTML = `
    <div class="page-enter max-w-3xl mx-auto p-4 pb-24">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h2 class="text-xl font-semibold">${schoolWidePapers ? 'School Papers' : 'Exam Papers'}</h2>
          ${schoolWidePapers ? '<p class="text-xs text-gray-500 mt-1">Papers created by you and teachers linked to this school.</p>' : ''}
        </div>
        <button id="btn-new-paper" class="floating-action-button" title="Build a paper online" aria-label="Build a paper">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        </button>
      </div>
      <div id="paper-list" class="space-y-3">
        <div class="text-center py-8 text-gray-400 text-sm">Loading…</div>
      </div>
    </div>
  `;

  await loadMeta();
  const newPaperButton = document.getElementById('btn-new-paper');
  if (newPaperButton && newPaperButton.dataset.bound !== 'true') {
    newPaperButton.dataset.bound = 'true';
    newPaperButton.addEventListener('click', () => openBuilder(null, schoolWidePapers));
  }
  await loadPapers(schoolWidePapers);
}
