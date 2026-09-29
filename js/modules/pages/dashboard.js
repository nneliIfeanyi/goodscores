import { getUser } from '../../utils/api.js';
import { getOfflineQuestions, getOfflinePapers } from '../../utils/db.js';

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
          <span class="bg-white/15 px-2.5 py-1 rounded-full">PDF: 95 credits</span>
        </div>
        <p class="mt-3 text-xs text-primary-100">✓ Manual question entry works offline</p>
      </div>

      <!-- Quick actions -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
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
    const [questions, papers] = await Promise.all([getOfflineQuestions(), getOfflinePapers()]);
    const questionCount = document.getElementById('dash-question-count');
    const paperCount = document.getElementById('dash-paper-count');
    if (questionCount) questionCount.textContent = questions.length;
    if (paperCount) paperCount.textContent = papers.length;
  } catch (_) {
    const questionCount = document.getElementById('dash-question-count');
    const paperCount = document.getElementById('dash-paper-count');
    if (questionCount) questionCount.textContent = '0';
    if (paperCount) paperCount.textContent = '0';
  }

  document.getElementById('dash-new-q')?.addEventListener('click', () => {
    document.querySelector('[data-page="questions"]')?.click();
  });
  document.getElementById('dash-build-paper')?.addEventListener('click', () => {
    document.querySelector('[data-page="papers"]')?.click();
  });
}
