import { getUser, saveUser, setToken, api } from '../../utils/api.js';
import { isLoggedIn, logout } from '../auth.js';
import { updateCreditsBadge } from '../../app.js';
import { toast } from '../../utils/toast.js';
import { confirmModal, inputModal } from '../../utils/modal.js';
import { clearOfflineStorage, getOfflineQuestionSummary, getOfflinePapers } from '../../utils/db.js';
import { backupQuestionBank, restoreQuestionBank } from '../../utils/backup.js?v=25';
import { loadMetaData, updateCachedMeta } from '../../utils/meta.js';

async function refreshUser() {
  const me = await api('/auth/me');
  if (me.data) {
    saveUser(me.data);
    updateCreditsBadge(me.data);
    return me.data;
  }
  return getUser();
}

function bindCollapsibleSections() {
  document.querySelectorAll('[data-collapse-target]').forEach((button) => {
    button.addEventListener('click', () => {
      const content = document.getElementById(button.dataset.collapseTarget);
      if (!content) return;
      const expanded = button.getAttribute('aria-expanded') === 'true';
      content.classList.toggle('hidden', expanded);
      button.setAttribute('aria-expanded', expanded ? 'false' : 'true');
      button.setAttribute('aria-label', `${expanded ? 'Expand' : 'Collapse'} ${button.dataset.collapseLabel}`);
      button.setAttribute('title', `${expanded ? 'Expand' : 'Collapse'} ${button.dataset.collapseLabel}`);
      button.querySelector('svg')?.classList.toggle('rotate-180', !expanded);
    });
  });
}

export async function renderAccount() {
  if (!isLoggedIn()) {
    const main = document.getElementById('main-content');
    main.innerHTML = `
      <div class="page-enter max-w-3xl mx-auto p-4 space-y-6 pb-24">
        <h2 class="text-xl font-semibold">Settings</h2>
        <section class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-5 space-y-4">
          <div>
            <h3 class="font-medium">Subjects and classes</h3>
            <p class="text-xs text-gray-500 mt-1">These are saved on this device. Add them before creating questions or building papers.</p>
          </div>
          <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2"><p class="text-xs font-semibold">Subjects</p><div id="my-subject-list" class="space-y-1 text-sm"><p class="text-xs text-gray-400">Loading…</p></div><form id="my-subject-form" class="flex gap-2 pt-2 border-t border-gray-100 dark:border-gray-700"><input name="name" required placeholder="Subject name" class="min-w-0 flex-1 px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-xs" /><input name="code" placeholder="Code" class="w-16 px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-xs" /><button class="px-2.5 py-1.5 rounded-lg bg-primary-600 text-white text-xs" type="submit">Add</button></form></div>
            <div class="space-y-2"><p class="text-xs font-semibold">Classes</p><div id="my-class-list" class="space-y-1 text-sm"><p class="text-xs text-gray-400">Loading…</p></div><form id="my-class-form" class="flex gap-2 pt-2 border-t border-gray-100 dark:border-gray-700"><input name="name" required placeholder="Class name" class="min-w-0 flex-1 px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-xs" /><button class="px-2.5 py-1.5 rounded-lg bg-primary-600 text-white text-xs" type="submit">Add</button></form></div>
          </div>
        </section>
        <button id="btn-sign-in" type="button" class="w-full py-2.5 rounded-xl bg-primary-600 text-white font-semibold text-sm">Sign in</button>
        <p class="text-xs leading-relaxed text-center text-gray-500 dark:text-gray-400">Sign in to unlock AI-powered question tools, export professional exam papers, back up your work securely, and access your questions and papers across devices.</p>
      </div>`;
    document.getElementById('btn-sign-in')?.addEventListener('click', async () => {
      const { renderLogin } = await import('./login.js');
      renderLogin();
    });
    loadTeacherMetaManager();
    return;
  }
  let user = getUser() || {};
  try {
    user = await refreshUser();
  } catch (_) {}

  const main = document.getElementById('main-content');
  main.innerHTML = `
    <div class="page-enter max-w-3xl mx-auto p-4 space-y-6 pb-24">
      <h2 class="text-xl font-semibold">Account</h2>
      <section class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-5 space-y-4">
        <div>
          <h3 class="font-medium">Subjects and classes</h3>
          <p class="text-xs text-gray-500 mt-1">Add your own subjects and classes before creating questions or building papers.</p>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
          <div class="space-y-2">
            <p class="text-xs font-semibold">Subjects</p>
            <div id="my-subject-list" class="space-y-1 text-sm"><p class="text-xs text-gray-400">Loading…</p></div>
            <form id="my-subject-form" class="flex gap-2 pt-2 border-t border-gray-100 dark:border-gray-700">
              <input name="name" required placeholder="Subject name" class="min-w-0 flex-1 px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-xs" />
              <input name="code" placeholder="Code" class="w-16 px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-xs" />
              <button class="px-2.5 py-1.5 rounded-lg bg-primary-600 text-white text-xs" type="submit">Add</button>
            </form>
          </div>
          <div class="space-y-2">
            <p class="text-xs font-semibold">Classes</p>
            <div id="my-class-list" class="space-y-1 text-sm"><p class="text-xs text-gray-400">Loading…</p></div>
            <form id="my-class-form" class="flex gap-2 pt-2 border-t border-gray-100 dark:border-gray-700">
              <input name="name" required placeholder="Class name" class="min-w-0 flex-1 px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-xs" />
              <button class="px-2.5 py-1.5 rounded-lg bg-primary-600 text-white text-xs" type="submit">Add</button>
            </form>
          </div>
        </div>
      </section>
      <button id="btn-logout" type="button" class="w-full py-2.5 rounded-xl border border-red-200 dark:border-red-900 text-red-600 dark:text-red-400 font-medium text-sm hover:bg-red-50 dark:hover:bg-red-900/20 transition">Sign out</button>
    </div>`;
  document.getElementById('btn-logout')?.addEventListener('click', logout);
  loadTeacherMetaManager();
  const mainContent = document.getElementById('main-content');
  if (mainContent && !mainContent.dataset.metaRefreshBound) {
    mainContent.dataset.metaRefreshBound = '1';
    window.addEventListener('gs-meta-refresh', () => loadTeacherMetaManager(true));
  }

  const isAdmin = user.role === 'school_admin';
  const isSchoolLinked = !!user.school_id;
  const isIndividual = !user.school_id && user.role === 'individual';
  const outputSettings = {
    ...(user.pdf_settings || {}),
    ...(isAdmin ? (user.school?.paper_settings || {}) : {}),
  };
  const planLabel = user.is_pro_plus ? 'Pro Plus' : user.is_pro ? (user.is_unlimited ? 'Unlimited' : 'Pro') : 'Free';

  main.innerHTML = `
    <div class="page-enter max-w-3xl mx-auto p-4 space-y-6 pb-24">
      <h2 class="text-xl font-semibold">Account</h2>

      <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-5 space-y-3">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 flex items-center justify-center font-semibold text-lg">
            ${(user.name || 'U').charAt(0).toUpperCase()}
          </div>
          <div>
            <p class="font-medium">${user.name || '—'}</p>
            <p class="text-sm text-gray-500">${user.email || ''}</p>
          </div>
        </div>
        <div class="pt-3 border-t border-gray-100 dark:border-gray-700 grid grid-cols-2 gap-2 text-sm">
          <div>
            <p class="text-gray-500 text-xs">Role</p>
            <p class="font-medium capitalize">${(user.role || '—').replace('_', ' ')}</p>
          </div>
          <div>
            <p class="text-gray-500 text-xs">Plan</p>
            <p class="font-medium">${planLabel}</p>
          </div>
          <div>
            <p class="text-gray-500 text-xs">Credits</p>
            <p class="font-medium">${user.is_unlimited || user.credits === -1 ? 'Unlimited' : (user.credits ?? 0)}</p>
          </div>
          <div>
            <p class="text-gray-500 text-xs">Offline</p>
            <p class="font-medium">Manual entry available</p>
          </div>
        </div>
        ${user.school ? `<p class="text-xs text-primary-600">School: ${user.school.name} · Code <span class="font-mono">${user.school.code}</span></p>` : ''}
      </div>

      <section class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-5 space-y-4">
        <div class="flex items-center justify-between gap-3">
          <div>
            <h3 class="font-medium">Question bank backup</h3>
            <p id="settings-sync-state" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Checking local status…</p>
          </div>
          <span class="flex items-center gap-3 shrink-0"><button id="settings-sync-restore" type="button" class="text-xs font-semibold text-gray-600 dark:text-gray-300 hover:underline">Restore</button><button id="settings-sync-retry" type="button" class="hidden text-xs font-semibold text-primary-700 dark:text-primary-300 hover:underline">Backup now</button><button type="button" data-collapse-target="settings-sync-content" data-collapse-label="question bank backup" aria-expanded="false" aria-controls="settings-sync-content" aria-label="Expand question bank backup" title="Expand question bank backup" class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300"><svg class="h-5 w-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg></button></span>
        </div>
        <div id="settings-sync-content" class="hidden grid grid-cols-3 gap-2 text-center">
          <div class="rounded-lg bg-amber-50 dark:bg-amber-900/20 p-2"><p id="settings-sync-pending" class="text-lg font-bold text-amber-700 dark:text-amber-300">0</p><p class="text-[11px] text-gray-500">Pending</p></div>
          <div class="rounded-lg bg-red-50 dark:bg-red-900/20 p-2"><p id="settings-sync-failed" class="text-lg font-bold text-red-700 dark:text-red-300">0</p><p class="text-[11px] text-gray-500">Failed</p></div>
          <div class="rounded-lg bg-green-50 dark:bg-green-900/20 p-2"><p id="settings-sync-synced" class="text-lg font-bold text-green-700 dark:text-green-300">0</p><p class="text-[11px] text-gray-500">Synced</p></div>
        </div>
      </section>

      <section class="rounded-2xl border border-red-200 dark:border-red-900/70 bg-red-50/60 dark:bg-red-950/20 p-5 space-y-3">
        <div class="flex items-start justify-between gap-3">
          <div>
          <h3 class="font-semibold text-red-700 dark:text-red-300">Offline storage</h3>
          <p class="text-xs text-red-600/80 dark:text-red-300/80 mt-1">Remove this device's saved questions, papers, subjects, and classes. Server backups and your account are not affected.</p>
          </div>
          <button type="button" data-collapse-target="offline-storage-content" data-collapse-label="offline storage" aria-expanded="false" aria-controls="offline-storage-content" aria-label="Expand offline storage" title="Expand offline storage" class="shrink-0 rounded-lg p-1.5 text-red-600 hover:bg-red-100 dark:hover:bg-red-900/30 dark:text-red-300"><svg class="h-5 w-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg></button>
        </div>
        <div id="offline-storage-content" class="hidden">
          <button id="btn-clear-offline-storage" type="button" class="w-full py-2.5 rounded-xl border border-red-300 dark:border-red-800 text-red-700 dark:text-red-300 text-sm font-semibold hover:bg-red-100 dark:hover:bg-red-900/30">Clear offline storage</button>
        </div>
      </section>

      ${(isIndividual || isAdmin) ? `<form id="pdf-settings-form" class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-5 space-y-3">
        <div class="flex items-center justify-between gap-3">
          <div>
            <h3 class="font-medium">Paper output</h3>
            <p class="text-xs text-gray-500 mt-1">Choose the default paper layout. Export uses this choice automatically.</p>
          </div>
          <button type="button" data-collapse-target="paper-settings-content" data-collapse-label="paper output settings" aria-expanded="false" aria-controls="paper-settings-content" aria-label="Expand paper output settings" title="Expand paper output settings" class="shrink-0 rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300">
            <svg class="h-5 w-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg>
          </button>
        </div>
        <div id="paper-settings-content" class="hidden space-y-3">
        <fieldset>
          <legend class="text-sm font-medium">Default paper format</legend>
          <div class="grid gap-3 sm:grid-cols-2 mt-2">
            <label class="block cursor-pointer">
              <input type="radio" name="paper_format" value="columns" class="peer sr-only" ${((outputSettings.paper_format || 'columns') === 'columns') ? 'checked' : ''} />
              <span class="block rounded-xl border border-gray-200 dark:border-gray-600 p-3 peer-checked:border-primary-600 peer-checked:ring-2 peer-checked:ring-primary-200 dark:peer-checked:ring-primary-900">
                <span class="block text-xs font-semibold">Two-column paper</span>
                <span class="mt-2 grid grid-cols-2 gap-2 rounded-lg bg-gray-50 dark:bg-gray-900 p-2 text-[9px] leading-tight text-gray-600 dark:text-gray-300">
                  <span>1. Question statement<br /><br />2. Question statement</span>
                  <span>A. Option&nbsp;&nbsp;B. Option<br />C. Option&nbsp;&nbsp;D. Option</span>
                </span>
                <span class="mt-2 block text-[11px] text-gray-500">Current format with questions arranged in two columns.</span>
              </span>
            </label>
            <label class="block cursor-pointer">
              <input type="radio" name="paper_format" value="inline_options" class="peer sr-only" ${outputSettings.paper_format === 'inline_options' ? 'checked' : ''} />
              <span class="block rounded-xl border border-gray-200 dark:border-gray-600 p-3 peer-checked:border-primary-600 peer-checked:ring-2 peer-checked:ring-primary-200 dark:peer-checked:ring-primary-900">
                <span class="block text-xs font-semibold">Inline options</span>
                <span class="mt-2 block rounded-lg bg-gray-50 dark:bg-gray-900 p-2 text-[9px] leading-tight text-gray-600 dark:text-gray-300">1. What is the answer?&nbsp;&nbsp;&nbsp;&nbsp;A. One&nbsp;&nbsp; B. Two&nbsp;&nbsp; C. Three&nbsp;&nbsp; D. Four</span>
                <span class="mt-2 block text-[11px] text-gray-500">No columns; options begin after the question with tab-sized spacing.</span>
              </span>
            </label>
          </div>
        </fieldset>
        <div class="grid grid-cols-2 gap-3">
          <label class="text-xs font-medium">Paper size
            <select name="paper_size" class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm">
              ${['A4', 'LETTER', 'LEGAL'].map((size) => `<option value="${size}" ${((outputSettings.paper_size || 'A4') === size) ? 'selected' : ''}>${size}</option>`).join('')}
            </select>
          </label>
          <label class="text-xs font-medium">Orientation
            <select name="orientation" class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm">
              <option value="portrait" ${((outputSettings.orientation || 'portrait') === 'portrait') ? 'selected' : ''}>Portrait</option>
              <option value="landscape" ${outputSettings.orientation === 'landscape' ? 'selected' : ''}>Landscape</option>
            </select>
          </label>
          ${[['margin_top', 'Top'], ['margin_bottom', 'Bottom'], ['margin_left', 'Left'], ['margin_right', 'Right']].map(([name, label]) => `<label class="text-xs font-medium">Margin ${label} (mm)<input name="${name}" type="number" min="0" max="50" step="1" value="${outputSettings[name] ?? (name.includes('left') || name.includes('right') ? 8 : 10)}" class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm" /></label>`).join('')}
          <label class="text-xs font-medium">Header height (px)<input name="header_height" type="number" min="0" max="300" step="1" value="${outputSettings.header_height ?? 80}" class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm" /></label>
          <label class="text-xs font-medium">Footer text<input name="footer_text" value="${outputSettings.footer_text ?? 'End of Paper'}" placeholder="End of Paper" class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm" /></label>
          <label class="text-xs font-medium">
            Font size
            <select name="pdf_font_size" class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm">
              ${[8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18].map((size) => `<option value="${size}" ${Number(outputSettings.pdf_font_size || user.pdf_font_size || 11) === size ? 'selected' : ''}>${size} pt</option>`).join('')}
            </select>
          </label>
          <label class="text-xs font-medium">
            Font style
            <select name="pdf_font_family" class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm">
              ${[
                ['dejavusans', 'DejaVu Sans'],
                ['dejavuserif', 'DejaVu Serif'],
                ['freesans', 'FreeSans'],
                ['freeserif', 'FreeSerif'],
                ['freemono', 'FreeMono'],
              ].map(([value, label]) => `<option value="${value}" ${((outputSettings.pdf_font_family || user.pdf_font_family || 'dejavusans') === value) ? 'selected' : ''}>${label}</option>`).join('')}
            </select>
          </label>
        </div>
        <div class="grid grid-cols-2 gap-3 text-xs">
          <label class="flex items-center gap-2"><input type="checkbox" name="show_logo" ${outputSettings.show_logo !== false ? 'checked' : ''} /> Show logo</label>
          <label class="flex items-center gap-2"><input type="checkbox" name="show_school_name" ${outputSettings.show_school_name !== false ? 'checked' : ''} /> Show school name</label>
        </div>
        <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="pdf_show_marks" ${(outputSettings.show_marks !== undefined ? outputSettings.show_marks : user.pdf_show_marks !== false) ? 'checked' : ''} /> Show marks per question</label>
        <button type="submit" class="w-full py-2 rounded-xl bg-primary-600 text-white text-sm font-medium">Save paper settings</button>
        </div>
      </form>` : ''}

      <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-5 space-y-4">
        <div class="flex items-start justify-between gap-3">
          <div>
            <h3 class="font-medium">My subjects and classes</h3>
            <p class="text-xs text-gray-500 mt-1">Nothing is preloaded. Add your own subjects and classes here before creating questions or building papers.</p>
          </div>
          <button type="button" data-collapse-target="meta-settings-content" data-collapse-label="subjects and classes" aria-expanded="false" aria-controls="meta-settings-content" aria-label="Expand subjects and classes" title="Expand subjects and classes" class="shrink-0 rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300"><svg class="h-5 w-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg></button>
        </div>
        <div id="meta-settings-content" class="hidden grid gap-4 sm:grid-cols-2">
          <div class="space-y-2">
            <p class="text-xs font-semibold">Subjects</p>
            <div id="my-subject-list" class="space-y-1 text-sm"><p class="text-xs text-gray-400">Loading…</p></div>
            <form id="my-subject-form" class="flex gap-2 pt-2 border-t border-gray-100 dark:border-gray-700">
              <input name="name" required placeholder="Subject name" class="min-w-0 flex-1 px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-xs" />
              <input name="code" placeholder="Code" class="w-16 px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-xs" />
              <button class="px-2.5 py-1.5 rounded-lg bg-primary-600 text-white text-xs" type="submit">Add</button>
            </form>
          </div>
          <div class="space-y-2">
            <p class="text-xs font-semibold">Classes</p>
            <div id="my-class-list" class="space-y-1 text-sm"><p class="text-xs text-gray-400">Loading…</p></div>
            <form id="my-class-form" class="flex gap-2 pt-2 border-t border-gray-100 dark:border-gray-700">
              <input name="name" required placeholder="Class name" class="min-w-0 flex-1 px-2 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-xs" />
              <button class="px-2.5 py-1.5 rounded-lg bg-primary-600 text-white text-xs" type="submit">Add</button>
            </form>
          </div>
        </div>
      </div>

      <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-5">
        <div class="flex items-center justify-between gap-3">
          <div>
            <h3 class="font-medium">Credit activity</h3>
            <p class="text-xs text-gray-500 mt-0.5">Recent credits added and used</p>
          </div>
          <span class="flex items-center gap-3 shrink-0">
            <button id="btn-refresh-transactions" type="button" class="text-xs text-primary-600 hover:underline">Refresh</button>
            <button type="button" data-collapse-target="credit-activity-content" data-collapse-label="credit activity" aria-expanded="false" aria-controls="credit-activity-content" aria-label="Expand credit activity" title="Expand credit activity" class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300">
              <svg class="h-5 w-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg>
            </button>
          </span>
        </div>
        <div id="credit-activity-content" class="hidden mt-3">
          <div id="credit-transactions" class="space-y-2 text-sm">
            <p class="text-xs text-gray-400">Loading activity…</p>
          </div>
        </div>
      </div>

      ${isAdmin ? `
      <div id="school-admin-panel" class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-5">
        <div class="flex items-center justify-between gap-3">
          <h3 class="font-medium">School admin</h3>
          <button type="button" data-collapse-target="school-admin-content" data-collapse-label="school admin" aria-expanded="false" aria-controls="school-admin-content" aria-label="Expand school admin" title="Expand school admin" class="shrink-0 rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300"><svg class="h-5 w-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg></button>
        </div>
        <div id="school-admin-content" class="hidden space-y-4">
        <p class="text-xs text-gray-500">Share code <span class="font-mono text-primary-600" id="sch-code">…</span> with teachers.</p>
        <div id="teacher-list" class="space-y-2 text-sm"><p class="text-gray-400">Loading teachers…</p></div>
        <form id="create-teacher-form" class="space-y-2 border-t border-gray-100 dark:border-gray-700 pt-3">
          <p class="text-xs font-medium">Create teacher account</p>
          <input name="name" required placeholder="Full name" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm" />
          <input name="email" type="email" required placeholder="Email" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm" />
          <input name="password" placeholder="Password (auto if empty)" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm" />
          <button type="submit" class="w-full py-2 rounded-xl bg-primary-600 text-white text-sm font-medium">Create teacher</button>
        </form>
        <div class="border-t border-gray-100 dark:border-gray-700 pt-3 space-y-2">
          <p class="text-xs font-medium">School header settings</p>
          <input id="sch-name" placeholder="School name" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm" />
          <input id="sch-address" placeholder="Address / motto" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm" />
          <label class="block text-xs font-medium">School logo<input id="sch-logo" type="file" accept="image/png,image/jpeg,image/gif,image/webp" class="mt-1 w-full text-sm" /></label>
          <p class="text-xs font-medium pt-2">Shared PDF output settings</p>
          <div class="grid grid-cols-2 gap-2">
            <select id="sch-pdf-font-size" class="px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm">
              ${[8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18].map((size) => `<option value="${size}">${size} pt</option>`).join('')}
            </select>
            <select id="sch-pdf-font-family" class="px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm">
              <option value="dejavusans">DejaVu Sans</option><option value="dejavuserif">DejaVu Serif</option><option value="freesans">FreeSans</option><option value="freeserif">FreeSerif</option><option value="freemono">FreeMono</option>
            </select>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <select id="sch-paper-size" class="px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm"><option value="A4">A4</option><option value="LETTER">Letter</option><option value="LEGAL">Legal</option></select>
            <select id="sch-orientation" class="px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm"><option value="portrait">Portrait</option><option value="landscape">Landscape</option></select>
            ${[['sch-margin-top', 'Top'], ['sch-margin-bottom', 'Bottom'], ['sch-margin-left', 'Left'], ['sch-margin-right', 'Right']].map(([id, label]) => `<label class="text-xs">Margin ${label} (mm)<input id="${id}" type="number" min="0" max="50" value="10" class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm" /></label>`).join('')}
            <label class="text-xs">Header height (px)<input id="sch-header-height" type="number" min="0" max="300" value="80" class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm" /></label>
            <label class="text-xs">Footer text<input id="sch-footer-text" value="End of Paper" class="mt-1 w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm" /></label>
          </div>
          <div class="grid grid-cols-2 gap-2 text-xs">
            <label class="flex items-center gap-2"><input id="sch-show-logo" type="checkbox" checked /> Show logo</label>
            <label class="flex items-center gap-2"><input id="sch-show-school-name" type="checkbox" checked /> Show school name</label>
          </div>
          <label class="flex items-center gap-2 text-xs"><input id="sch-show-marks" type="checkbox" /> Show marks per question</label>
          <button id="btn-save-school" type="button" class="w-full py-2 rounded-xl border border-primary-300 text-primary-700 text-sm font-medium">Save school info</button>
        </div>
        </div>
      </div>
      ` : ''}

      ${isIndividual ? `
      <form id="school-link-form" class="rounded-2xl bg-white dark:bg-gray-800 border border-primary-100 dark:border-primary-900/60 p-5">
        <div class="flex items-start justify-between gap-3">
          <div>
            <h3 class="font-medium">Join a school</h3>
            <p class="text-xs text-gray-500 mt-1">Enter the School ID shared by your school administrator to link this account and use the school workspace.</p>
          </div>
          <button type="button" data-collapse-target="school-link-content" data-collapse-label="join a school" aria-expanded="false" aria-controls="school-link-content" aria-label="Expand join a school" title="Expand join a school" class="shrink-0 rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300"><svg class="h-5 w-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg></button>
        </div>
        <div id="school-link-content" class="hidden space-y-3">
        <div class="flex gap-2">
          <input name="school_id" required maxlength="10" placeholder="SCH-XXXXXX" class="min-w-0 flex-1 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm uppercase" />
          <button type="submit" class="px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-medium">Link</button>
        </div>
        <p id="school-link-error" class="hidden text-xs text-red-600 dark:text-red-400"></p>
        </div>
      </form>

      <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-5">
        <div class="flex items-start justify-between gap-3">
          <div>
            <h3 class="font-medium">Exam header</h3>
            <p class="text-xs text-gray-500">Set one header here. It will appear automatically on every exported paper.</p>
          </div>
          <button type="button" data-collapse-target="exam-header-content" data-collapse-label="exam header" aria-expanded="false" aria-controls="exam-header-content" aria-label="Expand exam header" title="Expand exam header" class="shrink-0 rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300"><svg class="h-5 w-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg></button>
        </div>
        <div id="exam-header-content" class="hidden space-y-3">
        <div id="header-list" class="space-y-2 text-sm"></div>
        <form id="header-form" class="space-y-2 border-t pt-3 border-gray-100 dark:border-gray-700">
          <input name="school_name" required placeholder="School / centre name" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm" />
          <input name="extra_line" placeholder="Extra line" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm" />
          <label class="block text-xs font-medium">Header logo<input name="logo" type="file" accept="image/png,image/jpeg,image/gif,image/webp" class="mt-1 w-full text-sm" /></label>
          <button type="submit" class="w-full py-2 rounded-xl bg-primary-600 text-white text-sm font-medium">Save header</button>
        </form>
        </div>
      </div>
      ` : ''}

      ${(user.id && (!isSchoolLinked || isAdmin)) ? `<div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-5">
        <div class="flex items-center justify-between gap-3">
          <h3 class="font-medium">Buy credits / subscribe</h3>
          <button type="button" data-collapse-target="buy-credits-content" data-collapse-label="buy credits" aria-expanded="false" aria-controls="buy-credits-content" aria-label="Expand buy credits" title="Expand buy credits" class="shrink-0 rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300">
            <svg class="h-5 w-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg>
          </button>
        </div>
        <div id="buy-credits-content" class="hidden mt-1">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Secure payment via Paystack to <span class="font-semibold text-gray-700 dark:text-gray-200">Goodscores Stanvic Concepts</span>.</p>
        ${isIndividual ? `
        <div class="flex gap-2 mb-3">
          <label class="flex items-center gap-1.5 text-xs"><input type="radio" name="plan_target" value="pro" checked /> Pro</label>
          <label class="flex items-center gap-1.5 text-xs"><input type="radio" name="plan_target" value="pro_plus" /> Pro Plus (multi-header)</label>
        </div>
        ` : ''}
        <div class="grid grid-cols-2 gap-2">
          ${[
            { pack: '500', label: '₦500', desc: '400 credits' },
            { pack: '1000', label: '₦1,000', desc: '1,000 credits' },
            { pack: '2500', label: '₦2,500', desc: '3,000 credits' },
            { pack: '5000', label: '₦5,000', desc: '6,000 credits' },
            { pack: '25000', label: '₦25,000', desc: 'Unlimited 1 mo' },
            { pack: '50000', label: '₦50,000', desc: 'Unlimited 3 mo' },
            { pack: '120000', label: '₦120,000', desc: 'Unlimited 1 yr' },
          ].map(p => `
            <div class="p-3 rounded-xl border border-gray-200 dark:border-gray-600">
              <p class="font-semibold text-sm">${p.label}</p>
              <p class="text-xs text-gray-500">${p.desc}</p>
              <button data-pack="${p.pack}" class="buy-pack mt-3 w-full py-2 rounded-lg bg-primary-600 text-white text-xs font-semibold hover:bg-primary-700 transition">
                ${Number(p.pack) < 25000 ? 'Recharge' : 'Subscribe'}
              </button>
            </div>
          `).join('')}
        </div>
        </div>
      </div>` : ''}

      <button id="btn-logout" class="w-full py-2.5 rounded-xl border border-red-200 dark:border-red-900 text-red-600 dark:text-red-400 font-medium text-sm hover:bg-red-50 dark:hover:bg-red-900/20 transition">
        Sign out
      </button>
      ${isIndividual ? `<section class="rounded-2xl border border-red-200 dark:border-red-900/70 bg-red-50/60 dark:bg-red-950/20 p-5 space-y-3">
        <div><h3 class="font-semibold text-red-700 dark:text-red-300">Danger zone</h3><p class="text-xs text-red-600/80 dark:text-red-300/80 mt-1">Deleting your individual account permanently removes your questions, papers, passages, headers, and personal metadata.</p></div>
        <button id="btn-delete-account" type="button" class="w-full py-2.5 rounded-xl border border-red-300 dark:border-red-800 text-red-700 dark:text-red-300 text-sm font-semibold hover:bg-red-100 dark:hover:bg-red-900/30">Delete my account</button>
      </section>` : ''}
    </div>
  `;

  bindCollapsibleSections();

  document.getElementById('btn-logout')?.addEventListener('click', logout);
  document.getElementById('btn-clear-offline-storage')?.addEventListener('click', async () => {
    const confirmed = await confirmModal({
      title: 'Clear offline storage?',
      message: 'This removes saved questions, papers, subjects, and classes from this device. Server backups and your account will remain safe.',
      confirmLabel: 'Clear storage',
      danger: true,
    });
    if (!confirmed) return;
    const button = document.getElementById('btn-clear-offline-storage');
    if (!button) return;
    button.disabled = true;
    try {
      await clearOfflineStorage();
      toast('Offline storage cleared', 'success');
      await renderAccount();
    } catch (err) {
      toast(err.message || 'Could not clear offline storage', 'error');
      button.disabled = false;
    }
  });
  const updateBackupPanel = async () => {
    const status = { ...(await getOfflineQuestionSummary()), online: navigator.onLine };
    const pendingPapers = (await getOfflinePapers()).filter((paper) => paper.backup_state !== 'backed_up').length;
    status.pending += pendingPapers;
    const state = document.getElementById('settings-sync-state');
    if (!state) return;
    state.textContent = !status.online
      ? 'Offline: new questions stay on this device.'
      : status.failed
        ? 'Local changes are waiting for your explicit backup.'
        : status.pending
          ? 'Connect to the internet to back up your local changes.'
          : 'Everything is backed up.';
    document.getElementById('settings-sync-pending').textContent = status.pending;
    document.getElementById('settings-sync-failed').textContent = status.failed;
    document.getElementById('settings-sync-synced').textContent = status.synced;
    document.getElementById('settings-sync-retry')?.classList.toggle('hidden', !status.online || !status.pending);
  };
  updateBackupPanel();
  document.getElementById('settings-sync-retry')?.addEventListener('click', async (event) => {
    const button = document.getElementById('settings-sync-retry');
    if (!button) return;
    button.disabled = true;
    await backupQuestionBank();
    button.disabled = false;
    updateBackupPanel();
  });
  document.getElementById('settings-sync-restore')?.addEventListener('click', async (event) => {
    const button = document.getElementById('settings-sync-restore');
    if (!button) return;
    button.disabled = true;
    await restoreQuestionBank();
    button.disabled = false;
    updateBackupPanel();
  });
  document.getElementById('school-link-form')?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const form = event.target;
    const button = form.querySelector('button[type="submit"]');
    const error = document.getElementById('school-link-error');
    error.classList.add('hidden');
    button.disabled = true;
    try {
      await api('/auth/settings', {
        method: 'PUT',
        body: JSON.stringify({ school_id: form.school_id.value.trim() }),
      });
      if (res.data?.token) setToken(res.data.token);
      saveUser({ ...getUser(), ...res.data, school: res.data.school });
      toast('Account linked to school', 'success');
      await renderAccount();
    } catch (err) {
      error.textContent = err.message || 'Could not link your account';
      error.classList.remove('hidden');
    } finally {
      button.disabled = false;
    }
  });
  document.getElementById('btn-delete-account')?.addEventListener('click', async () => {
    const confirmed = await confirmModal({
      title: 'Delete your account?',
      message: 'This permanently deletes your individual account and all related data. This cannot be undone.',
      confirmLabel: 'Delete account',
      danger: true,
    });
    if (!confirmed) return;
    const button = document.getElementById('btn-delete-account');
    button.disabled = true;
    try {
      await api('/auth/account', { method: 'DELETE' });
      toast('Account deleted', 'success');
      setTimeout(logout, 500);
    } catch (err) {
      toast(err.message || 'Could not delete account', 'error');
      button.disabled = false;
    }
  });
  document.getElementById('pdf-settings-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const button = form.querySelector('button[type="submit"]');
    button.disabled = true;
    try {
      const res = await api('/auth/settings', {
        method: 'PUT',
        body: JSON.stringify({
          pdf_font_size: Number(form.pdf_font_size.value),
          pdf_font_family: form.pdf_font_family.value,
          pdf_show_marks: form.pdf_show_marks.checked,
          pdf_settings: {
            paper_format: form.paper_format.value,
            paper_size: form.paper_size.value,
            orientation: form.orientation.value,
            margin_top: Number(form.margin_top.value),
            margin_bottom: Number(form.margin_bottom.value),
            margin_left: Number(form.margin_left.value),
            margin_right: Number(form.margin_right.value),
            header_height: Number(form.header_height.value),
            show_logo: form.show_logo.checked,
            show_school_name: form.show_school_name.checked,
            footer_text: form.footer_text.value,
          },
        }),
      });
      await refreshUser();
      toast('PDF settings saved', 'success');
    } catch (err) {
      toast(err.message || 'Could not save PDF settings', 'error');
    } finally {
      button.disabled = false;
    }
  });
  loadTransactions();
  document.getElementById('btn-refresh-transactions')?.addEventListener('click', loadTransactions);
  loadTeacherMetaManager();

  document.querySelectorAll('.buy-pack').forEach((btn) => {
    btn.addEventListener('click', async () => {
      const pack = btn.dataset.pack;
      const plan = document.querySelector('input[name="plan_target"]:checked')?.value || 'pro';
      btn.disabled = true;
      try {
        const res = await api('/credits/initialize', {
          method: 'POST',
          body: JSON.stringify({ pack, plan }),
        });
        const url = res.data?.authorization_url;
        if (!url) throw new Error('Payment checkout could not be started');
        window.location.assign(url);
      } catch (err) {
        toast(err.message || 'Failed', 'error');
      } finally {
        btn.disabled = false;
      }
    });
  });

  if (isAdmin) {
    loadSchoolAdmin();
  }
  if (isIndividual) {
    loadHeaders();
    document.getElementById('header-form')?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const f = e.target;
      try {
        const headerId = f.dataset.headerId;
        const res = await api(headerId ? `/headers/${headerId}` : '/headers', {
          method: headerId ? 'PUT' : 'POST',
          body: JSON.stringify({
            school_name: f.school_name.value.trim(),
            extra_line: f.extra_line.value.trim() || undefined,
          }),
        });
        const logo = f.logo.files?.[0];
        if (logo && (headerId || res.data?.id)) {
          await api(`/headers/${headerId || res.data.id}/logo`, { method: 'POST', body: JSON.stringify({ image: await fileToDataUrl(logo) }) });
        }
        toast('Header saved', 'success');
        loadHeaders();
      } catch (err) {
        toast(err.message || 'Failed', 'error');
      }
    });
  }
}

async function loadTransactions() {
  const el = document.getElementById('credit-transactions');
  if (!el) return;
  try {
    const res = await api('/credits/transactions');
    const rows = res.data || [];
    if (!rows.length) {
      el.innerHTML = '<p class="text-xs text-gray-400">No credit activity yet</p>';
      return;
    }
    const pageSize = 10;
    let page = 0;
    const renderPage = () => {
      const pageCount = Math.ceil(rows.length / pageSize);
      const pageRows = rows.slice(page * pageSize, (page + 1) * pageSize);
      const start = page * pageSize + 1;
      const end = Math.min((page + 1) * pageSize, rows.length);
      el.innerHTML = `${pageRows.map((row) => {
      const amount = Number(row.amount || 0);
      const positive = amount > 0;
      const sign = positive ? '+' : '';
      const date = row.created_at ? new Date(row.created_at.replace(' ', 'T')).toLocaleString() : '';
      return `<div class="flex items-center justify-between gap-3 py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
        <div class="min-w-0"><p class="truncate font-medium">${row.reason || 'Credit activity'}</p><p class="text-[11px] text-gray-400">${date}</p></div>
        <span class="shrink-0 font-semibold ${positive ? 'text-green-600' : 'text-red-500'}">${sign}${amount}</span>
      </div>`;
      }).join('')}
      ${pageCount > 1 ? `<div class="flex items-center justify-between pt-2 text-xs text-gray-500">
        <span>${start}-${end} of ${rows.length}</span>
        <span class="flex items-center gap-2">
          <button type="button" data-transaction-page="previous" class="text-primary-600 disabled:text-gray-300 dark:disabled:text-gray-600" ${page === 0 ? 'disabled' : ''}>Previous</button>
          <span>Page ${page + 1} of ${pageCount}</span>
          <button type="button" data-transaction-page="next" class="text-primary-600 disabled:text-gray-300 dark:disabled:text-gray-600" ${page === pageCount - 1 ? 'disabled' : ''}>Next</button>
        </span>
      </div>` : ''}`;
      el.querySelector('[data-transaction-page="previous"]')?.addEventListener('click', () => {
        page -= 1;
        renderPage();
      });
      el.querySelector('[data-transaction-page="next"]')?.addEventListener('click', () => {
        page += 1;
        renderPage();
      });
    };
    renderPage();
  } catch (err) {
    el.innerHTML = `<p class="text-xs text-red-500">${err.message || 'Could not load credit activity'}</p>`;
  }
}

async function loadTeacherMetaManager(refresh = true) {
  const subjectList = document.getElementById('my-subject-list');
  const classList = document.getElementById('my-class-list');
  if (!subjectList || !classList) return;
  let subjectRows = [];
  let classRows = [];

  const renderRows = (list, rows, type) => {
    const resource = type === 'subject' ? 'subjects' : 'classes';
    if (!rows.length) {
      list.innerHTML = '<p class="text-xs text-gray-400">No personal records yet</p>';
      return;
    }
    list.innerHTML = rows.map((row) => `<div class="flex items-center justify-between gap-2 py-1 border-b border-gray-100 dark:border-gray-700 last:border-0">
      <span class="truncate">${row.name}${type === 'subject' && row.code ? ` <span class="text-[10px] text-gray-400">(${row.code})</span>` : ''}</span>
      <span class="shrink-0 flex gap-2"><button type="button" data-edit-meta="${row.id}" class="text-xs text-primary-600">Edit</button><button type="button" data-delete-meta="${row.id}" class="text-xs text-red-500">Delete</button></span>
    </div>`).join('');
    list.querySelectorAll('[data-edit-meta]').forEach((button) => button.addEventListener('click', async () => {
      const row = rows.find((item) => String(item.id) === button.dataset.editMeta);
      const name = await inputModal({ title: `Edit ${type} name`, label: `${type === 'subject' ? 'Subject' : 'Class'} name`, value: row?.name || '' });
      if (!name?.trim()) return;
      const code = type === 'subject' ? await inputModal({ title: 'Edit subject code', label: 'Subject code (optional)', value: row?.code || '' }) : null;
      if (type === 'subject' && code === null) return;
      try {
        const current = type === 'subject' ? subjectRows : classRows;
        let updated = { ...row, name: name.trim(), ...(type === 'subject' ? { code: code?.trim() || null } : {}) };
        if (!row?.local_only) {
          await api(`/meta/${resource}/${button.dataset.editMeta}`, { method: 'PUT', body: JSON.stringify({ name: updated.name, ...(type === 'subject' ? { code: updated.code } : {}) }) });
        }
        await updateCachedMeta({ [type === 'subject' ? 'subjects' : 'classes']: current.map((item) => item.id == row.id ? updated : item) });
        toast(`${type === 'subject' ? 'Subject' : 'Class'} updated`, 'success');
        await loadTeacherMetaManager(false);
      } catch (err) { toast(err.message || 'Update failed', 'error'); }
    }));
    list.querySelectorAll('[data-delete-meta]').forEach((button) => button.addEventListener('click', async () => {
      if (!await confirmModal({ title: `Delete ${type}?`, message: `Existing questions will keep their text but lose this ${type} selection.`, confirmLabel: 'Delete', danger: true })) return;
      try {
        const current = type === 'subject' ? subjectRows : classRows;
        const deleted = current.find((item) => String(item.id) === button.dataset.deleteMeta);
        if (!deleted?.local_only) await api(`/meta/${resource}/${button.dataset.deleteMeta}`, { method: 'DELETE' });
        await updateCachedMeta({ [type === 'subject' ? 'subjects' : 'classes']: current.filter((item) => item.id != button.dataset.deleteMeta) });
        toast(`${type === 'subject' ? 'Subject' : 'Class'} deleted`, 'success');
        await loadTeacherMetaManager(false);
      } catch (err) { toast(err.message || 'Delete failed', 'error'); }
    }));
  };

  try {
    const meta = await loadMetaData({ refresh: refresh && isLoggedIn() });
    subjectRows = (meta.subjects || []).filter((row) => row.local_only || row.teacher_id != null);
    classRows = (meta.classes || []).filter((row) => row.local_only || row.teacher_id != null);
    renderRows(subjectList, subjectRows, 'subject');
    renderRows(classList, classRows, 'class');
  } catch (err) {
    subjectList.innerHTML = classList.innerHTML = `<p class="text-xs text-red-500">${err.message || 'Could not load metadata'}</p>`;
  }

  const subjectForm = document.getElementById('my-subject-form');
  if (subjectForm && !subjectForm.dataset.bound) {
    subjectForm.dataset.bound = '1';
    subjectForm.addEventListener('submit', async (event) => {
      event.preventDefault();
      try {
        const name = subjectForm.name.value.trim();
        const code = subjectForm.code.value.trim() || null;
        const cached = await updateCachedMeta({});
        const localSubject = { id: `local-${crypto.randomUUID()}`, name, code, local_only: true };
        await updateCachedMeta({ subjects: [...cached.subjects, localSubject] });
        if (navigator.onLine && localStorage.getItem('gs_token')) {
          try {
            const response = await api('/meta/subjects', { method: 'POST', body: JSON.stringify({ name, code }) });
            const latest = await updateCachedMeta({});
            await updateCachedMeta({ subjects: latest.subjects.map((item) => item.id === localSubject.id ? { ...response.data, teacher_id: getUser()?.id } : item) });
          } catch (_) {}
        }
        subjectForm.reset();
        toast('Subject saved successfully', 'success');
        await loadTeacherMetaManager(false);
      } catch (err) { toast(err.message || 'Could not add subject', 'error'); }
    });
  }
  const classForm = document.getElementById('my-class-form');
  if (classForm && !classForm.dataset.bound) {
    classForm.dataset.bound = '1';
    classForm.addEventListener('submit', async (event) => {
      event.preventDefault();
      try {
        const name = classForm.name.value.trim();
        const cached = await updateCachedMeta({});
        const localClass = { id: `local-${crypto.randomUUID()}`, name, local_only: true };
        await updateCachedMeta({ classes: [...cached.classes, localClass] });
        if (navigator.onLine && localStorage.getItem('gs_token')) {
          try {
            const response = await api('/meta/classes', { method: 'POST', body: JSON.stringify({ name }) });
            const latest = await updateCachedMeta({});
            await updateCachedMeta({ classes: latest.classes.map((item) => item.id === localClass.id ? { ...response.data, teacher_id: getUser()?.id } : item) });
          } catch (_) {}
        }
        classForm.reset();
        toast('Class saved successfully', 'success');
        await loadTeacherMetaManager(false);
      } catch (err) { toast(err.message || 'Could not add class', 'error'); }
    });
  }
}

async function loadSchoolAdmin() {
  try {
    const [info, teachers] = await Promise.all([
      api('/school/info'),
      api('/school/teachers'),
    ]);
    const sch = info.data || {};
    const codeEl = document.getElementById('sch-code');
    if (codeEl) codeEl.textContent = sch.code || '—';
    const n = document.getElementById('sch-name');
    const a = document.getElementById('sch-address');
    if (n) n.value = sch.name || '';
    if (a) a.value = sch.address || '';
    const schoolSettings = sch.paper_settings || {};
    const schoolFontSize = document.getElementById('sch-pdf-font-size');
    const schoolFontFamily = document.getElementById('sch-pdf-font-family');
    const schoolShowMarks = document.getElementById('sch-show-marks');
    if (schoolFontSize) schoolFontSize.value = String(schoolSettings.pdf_font_size || 11);
    if (schoolFontFamily) schoolFontFamily.value = schoolSettings.pdf_font_family || 'dejavusans';
    if (schoolShowMarks) schoolShowMarks.checked = schoolSettings.show_marks !== false;
    const schoolSettingDefaults = { paper_size: 'A4', orientation: 'portrait', margin_top: 10, margin_bottom: 10, margin_left: 8, margin_right: 8, header_height: 80, footer_text: 'End of Paper', show_logo: true, show_school_name: true };
    Object.entries(schoolSettingDefaults).forEach(([key, fallback]) => {
      const element = document.getElementById(`sch-${key.replaceAll('_', '-')}`);
      if (element) element.type === 'checkbox' ? (element.checked = schoolSettings[key] !== false) : (element.value = Object.prototype.hasOwnProperty.call(schoolSettings, key) ? schoolSettings[key] : fallback);
    });

    const list = document.getElementById('teacher-list');
    const rows = teachers.data || [];
    if (!list) return;
    if (!rows.length) {
      list.innerHTML = '<p class="text-gray-400 text-xs">No teachers yet</p>';
    } else {
      list.innerHTML = rows.map((t) => `
        <div class="flex items-center justify-between gap-2 py-1.5 border-b border-gray-50 dark:border-gray-700">
          <div>
            <p class="font-medium text-sm">${t.name}</p>
            <p class="text-xs text-gray-500">${t.email} · ${t.role}</p>
          </div>
          ${t.role === 'teacher' ? `<span class="shrink-0 flex items-center gap-2"><button data-id="${t.id}" class="btn-edit-teacher text-xs text-primary-600">Edit</button><button data-id="${t.id}" class="btn-delete-teacher text-xs text-red-500">Delete</button><button data-id="${t.id}" data-active="${Number(t.is_active) !== 0 ? '1' : '0'}" class="btn-toggle-teacher text-xs ${Number(t.is_active) !== 0 ? 'text-red-500' : 'text-green-600'}">${Number(t.is_active) !== 0 ? 'Deactivate' : 'Activate'}</button></span>` : '<span class="text-xs text-gray-400">Admin</span>'}
        </div>
      `).join('');
      list.querySelectorAll('.btn-toggle-teacher').forEach((b) => {
        b.addEventListener('click', async () => {
          const active = b.dataset.active === '1';
          if (!await confirmModal({ title: `${active ? 'Deactivate' : 'Activate'} teacher?`, message: active ? 'The teacher will no longer be able to sign in until reactivated.' : 'The teacher will be able to sign in again.', confirmLabel: active ? 'Deactivate' : 'Activate', danger: active })) return;
          try {
            await api('/school/teachers/' + b.dataset.id, { method: 'PATCH', body: JSON.stringify({ active: !active }) });
            toast(active ? 'Teacher deactivated' : 'Teacher activated', 'success');
            loadSchoolAdmin();
          } catch (err) {
            toast(err.message, 'error');
          }
        });
      });
      list.querySelectorAll('.btn-edit-teacher').forEach((b) => {
        b.addEventListener('click', async () => {
          const teacher = rows.find((item) => String(item.id) === b.dataset.id);
          const name = await inputModal({ title: 'Edit teacher', label: 'Teacher name', value: teacher?.name || '' });
          if (name === null) return;
          const email = await inputModal({ title: 'Edit teacher', label: 'Teacher email', value: teacher?.email || '', type: 'email' });
          if (email === null) return;
          const password = await inputModal({ title: 'Edit teacher password', label: 'New password (leave empty to keep current password)', type: 'password' });
          if (password === null) return;
          try {
            await api('/school/teachers/' + b.dataset.id, { method: 'PUT', body: JSON.stringify({ name: name.trim(), email: email.trim(), password }) });
            toast('Teacher updated', 'success');
            loadSchoolAdmin();
          } catch (err) { toast(err.message || 'Could not update teacher', 'error'); }
        });
      });
      list.querySelectorAll('.btn-delete-teacher').forEach((b) => {
        b.addEventListener('click', async () => {
          const teacher = rows.find((item) => String(item.id) === b.dataset.id);
          if (!await confirmModal({ title: 'Delete teacher?', message: `Delete ${teacher?.name || 'this teacher'} and their account data? This cannot be undone.`, confirmLabel: 'Delete', danger: true })) return;
          try {
            await api('/school/teachers/' + b.dataset.id, { method: 'DELETE' });
            toast('Teacher deleted', 'success');
            loadSchoolAdmin();
          } catch (err) { toast(err.message || 'Could not delete teacher', 'error'); }
        });
      });
    }

    const form = document.getElementById('create-teacher-form');
    if (form && !form.dataset.bound) {
      form.dataset.bound = '1';
      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const f = e.target;
        try {
          const res = await api('/school/teachers', {
            method: 'POST',
            body: JSON.stringify({
              name: f.name.value.trim(),
              email: f.email.value.trim(),
              password: f.password.value || undefined,
            }),
          });
          toast('Teacher created. Password: ' + (res.data?.password || ''), 'success');
          f.reset();
          loadSchoolAdmin();
        } catch (err) {
          toast(err.message, 'error');
        }
      });
    }

    const saveBtn = document.getElementById('btn-save-school');
    if (saveBtn && !saveBtn.dataset.bound) {
      saveBtn.dataset.bound = '1';
      saveBtn.addEventListener('click', async () => {
        try {
          await api('/school/info', {
            method: 'PUT',
            body: JSON.stringify({
              name: document.getElementById('sch-name').value.trim(),
              address: document.getElementById('sch-address').value.trim(),
              paper_settings: {
                pdf_font_size: Number(document.getElementById('sch-pdf-font-size').value),
                pdf_font_family: document.getElementById('sch-pdf-font-family').value,
                show_marks: document.getElementById('sch-show-marks').checked,
                paper_size: document.getElementById('sch-paper-size').value,
                orientation: document.getElementById('sch-orientation').value,
                margin_top: Number(document.getElementById('sch-margin-top').value),
                margin_bottom: Number(document.getElementById('sch-margin-bottom').value),
                margin_left: Number(document.getElementById('sch-margin-left').value),
                margin_right: Number(document.getElementById('sch-margin-right').value),
                header_height: Number(document.getElementById('sch-header-height').value),
                footer_text: document.getElementById('sch-footer-text').value,
                show_logo: document.getElementById('sch-show-logo').checked,
                show_school_name: document.getElementById('sch-show-school-name').checked,
              },
            }),
          });
          const logo = document.getElementById('sch-logo').files?.[0];
          if (logo) await api('/school/logo', { method: 'POST', body: JSON.stringify({ image: await fileToDataUrl(logo) }) });
          toast('School updated', 'success');
        } catch (err) {
          toast(err.message, 'error');
        }
      });
    }
  } catch (err) {
    console.warn(err);
  }
}

async function loadHeaders() {
  const el = document.getElementById('header-list');
  if (!el) return;
  try {
    const res = await api('/headers');
    const list = res.data || [];
    if (list[0]) localStorage.setItem('gs_exam_header', JSON.stringify(list[0]));
    if (!list.length) {
      el.innerHTML = '<p class="text-xs text-gray-400">No headers saved yet</p>';
      return;
    }
      const header = list[0];
      el.innerHTML = `<p class="text-xs text-primary-600">Current header: ${header.school_name}</p>`;
      const form = document.getElementById('header-form');
      if (form) {
        form.dataset.headerId = header.id;
        form.school_name.value = header.school_name || '';
        form.extra_line.value = header.extra_line || '';
      }
  } catch (err) {
    el.innerHTML = '<p class="text-xs text-red-500">' + (err.message || 'Error') + '</p>';
  }
}

function fileToDataUrl(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = () => resolve(reader.result);
    reader.onerror = reject;
    reader.readAsDataURL(file);
  });
}
