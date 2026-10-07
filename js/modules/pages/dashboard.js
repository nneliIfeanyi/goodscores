import { getUser } from '../../utils/api.js';
import { getOfflineQuestions, getOfflinePapers } from '../../utils/db.js';
import { loadMetaData } from '../../utils/meta.js';

const ACCOUNT_META_FOCUS_INTENT_KEY = 'gs_account_focus_meta';

function countPersonalRows(rows) {
  return (rows || []).filter((row) => row?.local_only || row?.teacher_id != null).length;
}

function openAccountMetaManager(target = 'subject') {
  sessionStorage.setItem(ACCOUNT_META_FOCUS_INTENT_KEY, '1');
  document.querySelector('[data-page="account"]')?.click();
}

function updateMetaEmptyHints(subjectCount, classCount) {
  const subjectHint = document.getElementById('dash-subject-hint');
  const classHint = document.getElementById('dash-class-hint');
  if (subjectHint) {
    subjectHint.classList.toggle('hidden', Number(subjectCount) > 0);
  }
  if (classHint) {
    classHint.classList.toggle('hidden', Number(classCount) > 0);
  }
}

export async function renderDashboard() {
  const user = getUser() || {};
  const main = document.getElementById('main-content');

  const creditsDisplay = user.is_unlimited || user.credits === -1
    ? 'Unlimited'
    : (user.credits ?? 0);

  main.innerHTML = `
    <div class="page-enter max-w-3xl mx-auto p-4 space-y-6">
      <div>
        <h2 class="text-xl font-semibold">Hello, ${user.name?.split(' ')[0] || 'there'}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
          ${user.role === 'school_admin' ? 'School Admin' : user.role === 'teacher' ? 'Teacher' : ''} 
          ${user.school ? `· ${user.school.name}` : ''}
        </p>
      </div>

      <!-- Credits card -->
      <div class="rounded-2xl bg-gradient-to-br from-primary-600 to-primary-800 text-white p-5 shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-primary-100 text-sm">Available credits</p>
            <p id="dash-credits" class="text-3xl font-bold mt-1">${creditsDisplay}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
        <div class="mt-4 flex gap-3 text-xs">
          <span class="bg-white/15 px-2.5 py-1 rounded-full">PDF output: Free</span>
        </div>
        <p class="mt-3 text-xs text-primary-100">✓ Manual question entry works offline</p>
      </div>

      <!-- Quick actions -->
      <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
          <p class="text-xs text-gray-500">Subjects</p>
          <p id="dash-subject-count" class="text-2xl font-bold mt-1">—</p>
          <p id="dash-subject-hint" class="hidden mt-1 text-[11px] text-amber-700 dark:text-amber-300">No subjects yet. Tap Add.</p>
          <button type="button" data-dash-add-meta="subject" class="mt-1 inline-flex items-center gap-1 text-[11px] font-semibold text-primary-600 dark:text-primary-300 hover:underline">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add
          </button>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
          <p class="text-xs text-gray-500">Classes</p>
          <p id="dash-class-count" class="text-2xl font-bold mt-1">—</p>
          <p id="dash-class-hint" class="hidden mt-1 text-[11px] text-amber-700 dark:text-amber-300">No classes yet. Tap Add.</p>
          <button type="button" data-dash-add-meta="class" class="mt-1 inline-flex items-center gap-1 text-[11px] font-semibold text-primary-600 dark:text-primary-300 hover:underline">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add
          </button>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
          <p class="text-xs text-gray-500">Question bank</p>
          <p id="dash-question-count" class="text-2xl font-bold mt-1">—</p>
          <p class="text-[11px] text-gray-400 mt-1">Your questions</p>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
          <p class="text-xs text-gray-500">Papers built</p>
          <p id="dash-paper-count" class="text-2xl font-bold mt-1">—</p>
          <p class="text-[11px] text-gray-400 mt-1">Your papers</p>
        </div>
        <button id="dash-new-q" class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 text-left hover:border-primary-300 transition">
          <div class="w-10 h-10 rounded-xl bg-primary-50 dark:bg-primary-900/30 text-primary-600 flex items-center justify-center mb-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          </div>
          <p class="font-medium text-sm">New Question</p>
          <p class="text-xs text-gray-500 mt-0.5">MCQ · Fill · Theory</p>
        </button>
        <button id="dash-build-paper" class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 text-left hover:border-primary-300 transition">
          <div class="w-10 h-10 rounded-xl bg-primary-50 dark:bg-primary-900/30 text-primary-600 flex items-center justify-center mb-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          </div>
          <p class="font-medium text-sm">Build Paper</p>
          <p class="text-xs text-gray-500 mt-0.5">Online paper builder</p>
        </button>
      </div>

      <button id="dash-tutorials" type="button" class="group w-full rounded-2xl border border-primary-100 bg-primary-50/80 p-4 text-left transition hover:-translate-y-0.5 hover:border-primary-300 dark:border-primary-900/50 dark:bg-primary-900/20">
        <div class="flex items-center gap-3">
          <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-600 text-white shadow-sm">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.868v4.264a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </span>
          <span class="min-w-0 flex-1">
            <span class="block font-semibold text-primary-900 dark:text-primary-100">Learn with video guides</span>
            <span class="mt-0.5 block text-xs text-primary-700 dark:text-primary-300">See how to create questions and build papers in GoodScores.</span>
          </span>
          <svg class="h-5 w-5 shrink-0 text-primary-600 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5l7 7-7 7"/></svg>
        </div>
      </button>

      ${user.role === 'school_admin' ? `
      <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-4">
        <h3 class="font-medium mb-2">School Admin</h3>
        <p class="text-sm text-gray-500">School code: <span class="font-mono text-primary-600">${user.school?.code || '—'}</span></p>
        <p class="text-xs text-gray-400 mt-1">Share this code with teachers so they can join your school.</p>
      </div>
      ` : ''}
    </div>
  `;

  try {
    const [questions, papers, meta] = await Promise.all([getOfflineQuestions(), getOfflinePapers(), loadMetaData()]);
    const questionCount = document.getElementById('dash-question-count');
    const paperCount = document.getElementById('dash-paper-count');
    const subjectCount = document.getElementById('dash-subject-count');
    const classCount = document.getElementById('dash-class-count');
    if (questionCount) questionCount.textContent = questions.length;
    if (paperCount) paperCount.textContent = papers.length;
    const subjectTotal = countPersonalRows(meta.subjects);
    const classTotal = countPersonalRows(meta.classes);
    if (subjectCount) subjectCount.textContent = subjectTotal;
    if (classCount) classCount.textContent = classTotal;
    updateMetaEmptyHints(subjectTotal, classTotal);
  } catch (_) {
    const questionCount = document.getElementById('dash-question-count');
    const paperCount = document.getElementById('dash-paper-count');
    const subjectCount = document.getElementById('dash-subject-count');
    const classCount = document.getElementById('dash-class-count');
    if (questionCount) questionCount.textContent = '0';
    if (paperCount) paperCount.textContent = '0';
    if (subjectCount) subjectCount.textContent = '0';
    if (classCount) classCount.textContent = '0';
    updateMetaEmptyHints(0, 0);
  }

  document.getElementById('dash-new-q')?.addEventListener('click', () => {
    document.querySelector('[data-page="questions"]')?.click();
  });
  document.getElementById('dash-build-paper')?.addEventListener('click', () => {
    document.querySelector('[data-page="papers"]')?.click();
  });
  document.querySelectorAll('[data-dash-add-meta]').forEach((button) => {
    button.addEventListener('click', () => openAccountMetaManager(button.dataset.dashAddMeta));
  });
  document.getElementById('dash-tutorials')?.addEventListener('click', () => {
    document.getElementById('btn-tutorials')?.click();
  });
}
