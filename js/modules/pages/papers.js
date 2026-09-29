import { api } from '../../utils/api.js';
import { getUser } from '../../utils/api.js';
import { loadMetaData } from '../../utils/meta.js';
import { toast } from '../../utils/toast.js';
import { confirmModal } from '../../utils/modal.js';
import { API_BASE, storageUrl } from '../../utils/api.js';
import { requireAuthentication } from '../../utils/authGate.js';
import { getOfflineQuestions, getOfflinePapers, getOfflinePaper, saveOfflinePaper, deleteOfflinePaper } from '../../utils/db.js';
import { printPaper, downloadPaperHtml } from '../../utils/paperRenderer.js';

let meta = { subjects: [], classes: [], terms: [] };
let allQuestions = [];
let papers = [];

const questionKey = (question) => String(question?.id ?? question?.offline_id ?? '');

function isTheoryQuestion(question) {
  return question?.type === 'theory';
}

function isFillQuestion(question) {
  return question?.type === 'fill';
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

async function loadPapers() {
  const localPapers = await getOfflinePapers();
  papers = localPapers;
  renderList();
}

async function exportPaper(id, button = null) {
  const ownerOverlay = button?.closest('.fixed.inset-0');
  if (!localStorage.getItem('gs_token')) {
    requireAuthentication('Export', () => exportPaper(id, button));
    return;
  }
  if (!navigator.onLine) {
    ownerOverlay?.remove();
    toast('PDF export requires an internet connection. You can still prepare papers when online.', 'warn');
    return;
  }
  const originalLabel = button?.textContent || 'Export PDF';
  if (button) {
    button.disabled = true;
    button.textContent = 'Generating…';
  }
  try {
    const localPaper = await getOfflinePaper(id);
    if (localPaper) {
      const clientReferenceId = `local_export_${localPaper.offline_id}_${Date.now()}`;
      const exportFormat = window.prompt('Type PDF to print or DOC to download an editable Word-compatible document', 'PDF')?.toLowerCase();
      if (!exportFormat) return;
      const authorization = await api('/credits/authorize-export', {
        method: 'POST',
        body: JSON.stringify({ client_reference_id: clientReferenceId, format: exportFormat }),
      });
      if (exportFormat === 'doc' || exportFormat === 'docx') await downloadPaperHtml(localPaper, 'docx');
      else await printPaper(localPaper);
      toast(`Export authorized. ${authorization.data?.credits_left ?? ''} export credits remaining.`, 'success');
      return;
    }
    const exp = await api(`/papers/${id}/export`, { method: 'POST', body: '{}' });
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

function renderList() {
  const el = document.getElementById('paper-list');
  if (!el) return;

  if (!papers.length) {
    el.innerHTML = `
      <div class="text-center py-12 text-gray-500 dark:text-gray-400">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="font-medium">No exam papers yet</p>
        <p class="text-sm mt-1">Build a paper from your questions</p>
      </div>`;
    return;
  }

  el.innerHTML = papers.map((p) => `
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 cursor-pointer hover:border-primary-300 transition" data-paper-card data-id="${p.id || p.offline_id}">
      <div class="flex items-start justify-between gap-2">
        <div>
          <h3 class="font-medium text-sm">${p.title}</h3>
          <p class="text-xs text-gray-500 mt-0.5">
            ${p.subject_name || ''} ${p.class_name ? '· ' + p.class_name : ''} ${p.term_name ? '· ' + p.term_name : ''}
          </p>
          <p class="text-xs text-gray-400 mt-1">${p.questions ? p.questions.length : (p.question_count ?? 0)} questions · ${p.total_marks || 0} marks · ${p.status}</p>
        </div>
        <div class="flex gap-1">
          <button class="btn-edit-paper p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-sm" data-id="${p.id || p.offline_id}">Edit</button>
          <button class="btn-export-paper p-2 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/30 text-primary-700 dark:text-primary-300 text-sm" data-id="${p.id || p.offline_id}" title="Export PDF">PDF</button>
          <button class="btn-del-paper p-2 rounded-lg hover:bg-red-50 text-red-500" data-id="${p.id || p.offline_id}" data-offline-id="${p.offline_id || ''}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
          </button>
        </div>
      </div>
    </div>
  `).join('');

  el.querySelectorAll('[data-paper-card]').forEach((card) => {
    card.addEventListener('click', () => viewPaper(card.dataset.id));
  });
  el.querySelectorAll('.btn-edit-paper').forEach((b) => {
    b.addEventListener('click', async (event) => {
      event.stopPropagation();
      try {
        const local = await getOfflinePaper(b.dataset.id);
        if (local) openBuilder(local);
      } catch (err) {
        toast(err.message || 'Could not open paper', 'error');
      }
    });
  });
  el.querySelectorAll('.btn-export-paper').forEach((b) => {
    b.addEventListener('click', (event) => {
      event.stopPropagation();
      exportPaper(b.dataset.id, b);
    });
  });
  el.querySelectorAll('.btn-del-paper').forEach((b) => {
    b.addEventListener('click', async (event) => {
      event.stopPropagation();
      if (!await confirmModal({ title: 'Delete paper?', message: 'This paper will be deleted permanently.', confirmLabel: 'Delete', danger: true })) return;
      if (b.dataset.offlineId) {
        await deleteOfflinePaper(b.dataset.offlineId);
      }
      await loadPapers();
    });
  });
}

async function openBuilder(existingPaper = null) {
  // Load questions for selection
  try {
    allQuestions = await getOfflineQuestions();
  } catch {
    allQuestions = [];
  }

  const selectedQuestionIds = new Set((existingPaper?.questions || []).map(questionKey));
  const passages = [];

  const savedSections = existingPaper?.paper_settings?.sections || [];
  const sections = savedSections.length
    ? savedSections.map((section, index) => ({
        key: section.key || String.fromCharCode(65 + index),
        type: section.type || (section.key === 'B' ? 'theory' : section.key === 'C' ? 'fill' : 'objective'),
        title: section.title || `Section ${String.fromCharCode(65 + index)}`,
        instructions: section.instructions || '',
        passage_id: section.passage_id || null,
        question_ids: (section.question_ids || []).map(String),
      }))
    : [
        {
          key: 'A',
          type: 'objective',
          title: 'Section A: Objective Questions',
          instructions: 'Answer all questions. Choose the correct option or complete the gap.',
          passage_id: null,
          question_ids: [],
        },
        {
          key: 'B',
          type: 'theory',
          title: 'Section B: Theory Questions',
          instructions: 'Answer any three questions.',
          passage_id: null,
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
    <div class="bg-white dark:bg-gray-900 w-full sm:max-w-lg max-h-[94vh] overflow-y-auto rounded-t-2xl sm:rounded-2xl shadow-xl">
      <div class="sticky top-0 z-10 bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 px-4 py-3 flex items-center justify-between">
        <h3 class="font-semibold">${existingPaper ? 'Edit Exam Paper' : 'Build Exam Paper'}</h3>
        <button id="pb-close" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
      <form id="pb-form" class="p-4 space-y-4">
        <div>
          <label class="block text-xs font-medium mb-1">Paper title</label>
          <input id="pb-title" name="title" required value="${existingPaper?.title || ''}" placeholder="e.g. Biology – SS2 1st Term Exam"
            class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm" />
        </div>
        <div class="grid grid-cols-3 gap-2">
          <div>
            <label class="block text-xs font-medium mb-1">Subject</label>
            <select name="subject_id" class="w-full px-2 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm">
              <option value="">${meta.subjects.length ? '—' : 'Add a subject in Account settings'}</option>
              ${meta.subjects.map((s) => `<option value="${s.id}" ${existingPaper?.subject_id == s.id ? 'selected' : ''}>${s.name}</option>`).join('')}
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium mb-1">Class</label>
            <select name="class_id" class="w-full px-2 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm">
              <option value="">${meta.classes.length ? '—' : 'Add a class in Account settings'}</option>
              ${meta.classes.map((c) => `<option value="${c.id}" ${existingPaper?.class_id == c.id ? 'selected' : ''}>${c.name}</option>`).join('')}
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium mb-1">Term</label>
            <select name="term_id" class="w-full px-2 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm">
              <option value="">—</option>
              ${meta.terms.map((t) => `<option value="${t.id}" ${existingPaper?.term_id == t.id ? 'selected' : ''}>${t.name}</option>`).join('')}
            </select>
          </div>
        </div>
        ${(!meta.subjects.length || !meta.classes.length) ? `<div class="rounded-xl border border-amber-200 bg-amber-50 dark:border-amber-800 dark:bg-amber-900/20 p-3 text-xs text-amber-800 dark:text-amber-200">
          Add your own ${!meta.subjects.length ? 'subjects' : ''}${!meta.subjects.length && !meta.classes.length ? ' and ' : ''}${!meta.classes.length ? 'classes' : ''} in Account settings before setting paper details.
          <button type="button" id="pb-open-account" class="ml-1 font-semibold underline">Open Account settings</button>
        </div>` : ''}

        <div>
          <label class="block text-xs font-medium mb-1">Time allowed</label>
          <input name="time_allowed" value="${existingPaper?.paper_settings?.time_allowed || ''}" placeholder="e.g. 1 hour 30 minutes"
            class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm" />
        </div>

        <div class="border-t border-gray-100 dark:border-gray-800 pt-4">
          <div class="flex items-center justify-between mb-2">
            <div>
              <p class="text-sm font-semibold">Paper sections</p>
              <p class="text-[11px] text-gray-400 mt-1">Create, rename, reorder, remove, and assign passages or questions to any section.</p>
            </div>
            <button id="pb-add-section" type="button" class="px-2.5 py-1.5 rounded-lg border border-primary-200 text-primary-700 dark:text-primary-300 text-xs font-semibold">Add section</button>
          </div>
          <div id="pb-sections" class="space-y-3"></div>
        </div>

        <div>
          <div class="flex items-center justify-between mb-2">
            <label class="text-xs font-medium">Select questions</label>
            <label class="inline-flex items-center gap-1.5 text-xs text-primary-600 dark:text-primary-400 cursor-pointer">
              <input id="pb-check-all" type="checkbox" class="rounded border-gray-300 text-primary-600" />
              Check all
            </label>
          </div>
          <div id="pb-q-status" class="hidden text-xs text-gray-400 mb-2"></div>
          <div id="pb-q-list" class="space-y-2 max-h-60 overflow-y-auto border border-gray-100 dark:border-gray-700 rounded-xl p-2"></div>
        </div>

        <div>
          <div class="flex items-center justify-between mb-1">
            <label class="text-xs font-medium">Theory question numbering</label>
            <span class="text-[11px] text-gray-400">Use 1(a) or 1(b)(i)</span>
          </div>
          <div id="pb-structure-list" class="space-y-2 border border-gray-100 dark:border-gray-700 rounded-xl p-2"></div>
        </div>

        <div id="pb-error" class="hidden text-sm text-red-600"></div>
        <button type="submit" class="w-full py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold text-sm">
          ${existingPaper ? 'Save paper changes' : 'Save paper draft'}
        </button>
      </form>
    </div>
  `;

  document.body.appendChild(overlay);
  overlay.querySelector('#pb-open-account')?.addEventListener('click', () => {
    overlay.remove();
    document.querySelector('[data-page="account"]')?.click();
  });
  document.getElementById('pb-close').onclick = () => overlay.remove();

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
  };
  ['subject_id', 'class_id', 'term_id'].forEach((name) => {
    form.elements[name]?.addEventListener('change', updatePaperTitle);
  });
  const questionList = document.getElementById('pb-q-list');
  const structureList = document.getElementById('pb-structure-list');
  const sectionsList = document.getElementById('pb-sections');
  const savedLayout = existingPaper?.paper_settings?.question_layout || [];
  const structureState = new Map(savedLayout.map((item) => [String(item.question_id), {
    level: Number.isInteger(item.level) ? item.level : 0,
  }]));
  const questionStatus = document.getElementById('pb-q-status');
  const checkAll = document.getElementById('pb-check-all');

  const sectionForQuestion = (questionId) => sections.find((section) => section.question_ids.includes(String(questionId)));
  const defaultSectionForQuestion = (question) => isTheoryQuestion(question)
    ? (sections.find((section) => section.type === 'theory') || sections[0])
    : sections[0];

  const renderSections = () => {
    sections.forEach((section, index) => {
      section.key = String.fromCharCode(65 + index);
    });
    sectionsList.innerHTML = sections.map((section, index) => `
      <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-3 space-y-2" data-section-index="${index}">
        <div class="flex items-center gap-2">
          <span class="text-xs font-bold text-primary-600">${section.key}</span>
          <input data-section-title value="${section.title.replace(/"/g, '&quot;')}" class="min-w-0 flex-1 px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm" aria-label="Section title" />
          <button type="button" data-section-up class="p-1 text-xs ${index === 0 ? 'invisible' : ''}" title="Move section up">↑</button>
          <button type="button" data-section-down class="p-1 text-xs ${index === sections.length - 1 ? 'invisible' : ''}" title="Move section down">↓</button>
          <button type="button" data-section-remove class="p-1 text-xs text-red-500 ${sections.length <= 1 ? 'invisible' : ''}" title="Remove section">×</button>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <select data-section-type class="px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-xs">
            ${sectionTypes.map(([value, label]) => `<option value="${value}" ${section.type === value ? 'selected' : ''}>${label}</option>`).join('')}
          </select>
          <select data-section-passage class="px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-xs">
            <option value="">No passage attached</option>
            ${passages.map((passage) => `<option value="${passage.id}" ${String(section.passage_id || '') === String(passage.id) ? 'selected' : ''}>${passage.title}</option>`).join('')}
          </select>
        </div>
        <textarea data-section-instructions rows="2" placeholder="Instructions, e.g. Answer any three questions" class="w-full px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-xs">${section.instructions}</textarea>
        <p class="text-[11px] text-gray-400">${section.question_ids.length} question${section.question_ids.length === 1 ? '' : 's'} assigned</p>
      </div>`).join('');

    sectionsList.querySelectorAll('[data-section-index]').forEach((card) => {
      const index = Number(card.dataset.sectionIndex);
      card.querySelector('[data-section-title]').addEventListener('input', (event) => { sections[index].title = event.target.value; });
      card.querySelector('[data-section-type]').addEventListener('change', (event) => { sections[index].type = event.target.value; });
      card.querySelector('[data-section-passage]').addEventListener('change', (event) => { sections[index].passage_id = event.target.value || null; });
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
  };

  document.getElementById('pb-add-section').addEventListener('click', () => {
    sections.push({ key: '', type: 'custom', title: `Section ${String.fromCharCode(65 + sections.length)}`, instructions: '', passage_id: null, question_ids: [] });
    renderSections();
    renderQuestions();
  });

  const renderQuestions = () => {
    if (!allQuestions.length) {
      questionList.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">No questions match these selections.</p>';
      checkAll.checked = false;
      checkAll.indeterminate = false;
      return;
    }

    questionList.innerHTML = allQuestions.map((q) => `
      <label data-question-id="${questionKey(q)}" draggable="${selectedQuestionIds.has(questionKey(q))}" class="flex items-start gap-2 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer ${selectedQuestionIds.has(questionKey(q)) ? 'cursor-grab' : ''}">
        <input type="checkbox" name="qid" value="${questionKey(q)}" class="mt-1 rounded border-gray-300 text-primary-600" ${selectedQuestionIds.has(questionKey(q)) ? 'checked' : ''} />
          <div class="min-w-0 flex-1">
          <span class="text-[10px] font-semibold uppercase text-primary-600">${q.type}</span>
          <p class="text-xs text-gray-700 dark:text-gray-300 line-clamp-2">${(q.body || '').replace(/<[^>]+>/g, '').slice(0, 80)}</p>
          <p class="text-[10px] text-gray-400">${q.marks || 1} mk · ${q.subject_name || ''}</p>
        </div>
        <select data-question-section class="w-28 px-1 py-1 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-[10px]" aria-label="Question section">
          ${sections.map((section) => `<option value="${section.key}" ${sectionForQuestion(questionKey(q))?.key === section.key ? 'selected' : ''}>Section ${section.key}</option>`).join('')}
        </select>
      </label>
    `).join('');

    questionList.querySelectorAll('[name="qid"]').forEach((checkbox) => {
      checkbox.addEventListener('change', () => {
        const id = String(checkbox.value);
        if (checkbox.checked) {
          selectedQuestionIds.add(id);
          if (!sectionForQuestion(id)) defaultSectionForQuestion(allQuestions.find((question) => questionKey(question) === id))?.question_ids.push(id);
        } else {
          selectedQuestionIds.delete(id);
          sections.forEach((section) => { section.question_ids = section.question_ids.filter((questionId) => questionId !== id); });
        }
        updateCheckAll();
        renderSections();
        renderStructure();
      });
    });
    questionList.querySelectorAll('[data-question-section]').forEach((select) => {
      select.addEventListener('change', () => {
        const id = String(select.closest('[data-question-id]')?.dataset.questionId || select.parentElement.querySelector('[name="qid"]').value);
        sections.forEach((section) => { section.question_ids = section.question_ids.filter((questionId) => questionId !== id); });
        sections.find((section) => section.key === select.value)?.question_ids.push(id);
        renderSections();
      });
    });
    let draggedQuestionId = null;
    questionList.querySelectorAll('[data-question-id][draggable="true"]').forEach((row) => {
      row.addEventListener('dragstart', (event) => {
        draggedQuestionId = String(row.dataset.questionId);
        event.dataTransfer.effectAllowed = 'move';
        row.classList.add('opacity-50');
      });
      row.addEventListener('dragend', () => {
        draggedQuestionId = null;
        row.classList.remove('opacity-50');
      });
      row.addEventListener('dragover', (event) => {
        event.preventDefault();
        event.dataTransfer.dropEffect = 'move';
      });
      row.addEventListener('drop', (event) => {
        event.preventDefault();
        const targetQuestionId = String(row.dataset.questionId);
        if (!draggedQuestionId || draggedQuestionId === targetQuestionId) return;
        const sourceSection = sectionForQuestion(draggedQuestionId);
        const targetSection = sectionForQuestion(targetQuestionId);
        if (!sourceSection || !targetSection) return;
        sourceSection.question_ids = sourceSection.question_ids.filter((id) => String(id) !== String(draggedQuestionId));
        const targetIndex = targetSection.question_ids.findIndex((id) => String(id) === String(targetQuestionId));
        targetSection.question_ids.splice(targetIndex < 0 ? targetSection.question_ids.length : targetIndex, 0, draggedQuestionId);
        renderQuestions();
        renderSections();
        renderStructure();
      });
    });
    updateCheckAll();
  };

  const updateCheckAll = () => {
    const visibleIds = allQuestions.map(questionKey);
    const selectedVisible = visibleIds.filter((id) => selectedQuestionIds.has(id)).length;
    checkAll.checked = visibleIds.length > 0 && selectedVisible === visibleIds.length;
    checkAll.indeterminate = selectedVisible > 0 && selectedVisible < visibleIds.length;
  };

  const renderStructure = () => {
    const orderedIds = sections.flatMap((section) => section.question_ids.map(String));
    const theoryQuestions = orderedIds
      .filter((id, index, ids) => selectedQuestionIds.has(id) && ids.indexOf(id) === index)
      .map((id) => allQuestions.find((q) => questionKey(q) === String(id)))
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
      return `<div class="grid grid-cols-[1fr_5rem] gap-2 items-center">
        <div class="min-w-0">
          <p class="text-xs truncate">${(q.body || '').replace(/<[^>]+>/g, '').slice(0, 70)}</p>
          <span data-structure-label="${q.id}" class="mt-1 block text-xs font-semibold text-primary-600">${label}</span>
        </div>
        <select data-structure-level="${q.id}" aria-label="Question nesting level"
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
    allQuestions = (await getOfflineQuestions()).filter((question) =>
      (!form.subject_id.value || String(question.subject_id) === String(form.subject_id.value))
      && (!form.class_id.value || String(question.class_id) === String(form.class_id.value))
      && (!form.term_id.value || String(question.term_id) === String(form.term_id.value)));
    questionStatus.textContent = `${allQuestions.length} local question${allQuestions.length === 1 ? '' : 's'} found`;
    renderQuestions();
    renderStructure();
  };

  ['subject_id', 'class_id', 'term_id'].forEach((field) => {
    form[field].addEventListener('change', loadFilteredQuestions);
  });
  checkAll.addEventListener('change', () => {
    allQuestions.forEach((q) => {
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
    const orderedIds = sections.flatMap((section) => section.question_ids.map(String));
    const qids = [...orderedIds, ...selectedQuestionIds].filter((id, index, ids) => selectedQuestionIds.has(id) && ids.indexOf(id) === index);
    if (!qids.length) {
      document.getElementById('pb-error').textContent = 'Select at least one question';
      document.getElementById('pb-error').classList.remove('hidden');
      return;
    }

    const theoryIds = qids.filter((id) => isTheoryQuestion(allQuestions.find((q) => questionKey(q) === String(id))));
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

    const btn = form.querySelector('[type="submit"]');
    btn.disabled = true;
    try {
      const questionMap = new Map(allQuestions.map((question) => [questionKey(question), question]));
      const localPaper = await saveOfflinePaper({
        ...payload,
        offline_id: existingPaper?.offline_id,
        id: existingPaper?.id,
        subject_name: form.subject_id.selectedOptions[0]?.textContent.trim() || '',
        class_name: form.class_id.selectedOptions[0]?.textContent.trim() || '',
        term_name: form.term_id.selectedOptions[0]?.textContent.trim() || '',
        questions: qids.map((id) => questionMap.get(String(id))).filter(Boolean),
        total_marks: qids.reduce((total, id) => total + Number(questionMap.get(String(id))?.marks || 0), 0),
      });
      overlay.remove();
      await loadPapers();
      viewPaper(localPaper.offline_id);
    } catch (err) {
      document.getElementById('pb-error').textContent = err.message || 'Failed';
      document.getElementById('pb-error').classList.remove('hidden');
    } finally {
      btn.disabled = false;
    }
  });
}

async function viewPaper(id) {
  try {
    let p = await getOfflinePaper(id);
    if (!p) return;
    const overlay = document.createElement('div');
    overlay.className = 'fixed inset-0 z-50 bg-black/40 flex items-end sm:items-center justify-center p-0 sm:p-4';
    overlay.innerHTML = `
      <div class="bg-white dark:bg-gray-900 w-full sm:max-w-lg max-h-[90vh] overflow-y-auto rounded-t-2xl sm:rounded-2xl shadow-xl p-4">
        <div class="flex justify-between items-start mb-4">
          <div>
            <h3 class="font-semibold">${p.title}</h3>
            <p class="text-xs text-gray-500">${p.subject_name || ''} · ${p.class_name || ''} · ${p.total_marks || 0} marks</p>
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
        <button id="btn-export-pdf" class="mt-4 w-full py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold text-sm">
          Export PDF
        </button>
        <p class="mt-2 text-xs text-gray-400 text-center">Requires online connection. File downloads when ready.</p>
      </div>
    `;
    document.body.appendChild(overlay);
    document.getElementById('vp-close').onclick = () => overlay.remove();
    document.getElementById('vp-edit').onclick = () => {
      overlay.remove();
      openBuilder(p);
    };

    document.getElementById('btn-export-pdf')?.addEventListener('click', (event) => {
      event.stopPropagation();
      exportPaper(id, document.getElementById('btn-export-pdf'));
    });
  } catch (err) {
    toast(err.message || 'Could not open paper', 'error');
  }
}

export async function renderPapers() {
  const main = document.getElementById('main-content');
  main.innerHTML = `
    <div class="page-enter max-w-3xl mx-auto p-4 pb-24">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold">Exam Papers</h2>
        <button id="btn-new-paper" class="floating-action-button" title="Build a paper" aria-label="Build a paper">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        </button>
      </div>
      <div id="paper-list" class="space-y-3">
        <div class="text-center py-8 text-gray-400 text-sm">Loading…</div>
      </div>
    </div>
  `;

  await loadMeta();
  document.getElementById('btn-new-paper')?.addEventListener('click', () => {
    openBuilder();
  });
  await loadPapers();
}
