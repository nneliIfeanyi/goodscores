import { login, requestPasswordReset, resetPassword } from '../auth.js';
import { renderRegister } from './register.js';
import { updateCreditsBadge } from '../../app.js';
import { getUser } from '../../utils/api.js';
import { resumeAuthenticationAction } from '../../utils/authGate.js';

export function renderLogin({ message = '' } = {}) {
  const main = document.getElementById('main-content');
  document.getElementById('bottom-nav')?.classList.add('hidden');

  main.innerHTML = `
    <div class="page-enter min-h-[calc(100vh-3.5rem)] flex items-center justify-center p-4 bg-antique-gradient">
      <div class="w-full max-w-sm">
        <div class="text-center mb-8">
          <div class="inline-flex w-14 h-14 rounded-2xl bg-primary-600 text-white items-center justify-center mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Welcome back</h1>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Sign in to your GoodScores account</p>
        </div>

        <form id="login-form" class="space-y-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
          <div>
            <label class="block text-sm font-medium mb-1.5">Email</label>
            <input type="email" name="email" required
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm"
              placeholder="you@school.com" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5">Password</label>
            <input type="password" name="password" required minlength="6"
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm"
              placeholder="••••••••" />
          </div>
          ${message ? `<div class="text-sm text-primary-700 bg-primary-50 rounded-lg px-3 py-2">${message}</div>` : ''}
          <div id="login-error" class="hidden text-sm text-red-600 bg-red-50 dark:bg-red-900/20 rounded-lg px-3 py-2"></div>
          <button id="forgot-password" type="button" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">Forgot password?</button>
          <button type="submit"
            class="w-full py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold text-sm transition">
            Sign in
          </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
          Don’t have an account?
          <button id="goto-register" class="text-primary-600 dark:text-primary-400 font-medium hover:underline">Create one</button>
        </p>
      </div>
    </div>
  `;

  document.getElementById('goto-register')?.addEventListener('click', renderRegister);
  document.getElementById('forgot-password')?.addEventListener('click', () => renderForgotPassword());

  document.getElementById('login-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const btn = form.querySelector('button[type="submit"]');
    const errEl = document.getElementById('login-error');
    errEl.classList.add('hidden');

    btn.disabled = true;
    btn.textContent = 'Signing in…';

    try {
      const res = await login(form.email.value, form.password.value);
      updateCreditsBadge(res.data.user);
      document.getElementById('bottom-nav')?.classList.remove('hidden');
      await resumeAuthenticationAction();
      document.querySelector('[data-page="dashboard"]')?.click();
    } catch (err) {
      errEl.textContent = err.message || 'Login failed';
      errEl.classList.remove('hidden');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Sign in';
    }
  });
}

function renderForgotPassword() {
  const main = document.getElementById('main-content');
  main.innerHTML = `
    <div class="page-enter min-h-[calc(100vh-3.5rem)] flex items-center justify-center p-4 bg-antique-gradient">
      <div class="w-full max-w-sm">
        <div class="text-center mb-8"><h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reset your password</h1><p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter your account email and we’ll send a reset link.</p></div>
        <form id="forgot-form" class="space-y-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
          <input type="email" name="email" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm" placeholder="you@school.com" />
          <div id="forgot-message" class="hidden text-sm rounded-lg px-3 py-2"></div>
          <button type="submit" class="w-full py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold text-sm">Send reset link</button>
        </form>
        <button id="back-to-login" class="w-full mt-6 text-sm text-primary-600 hover:underline">Back to sign in</button>
      </div>
    </div>`;
  document.getElementById('back-to-login')?.addEventListener('click', renderLogin);
  document.getElementById('forgot-form')?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const form = event.target;
    const message = document.getElementById('forgot-message');
    try {
      const response = await requestPasswordReset(form.email.value.trim());
      message.textContent = response.message || 'If an account exists for that email, a password reset link has been sent.';
      message.className = 'text-sm rounded-lg px-3 py-2 text-green-700 bg-green-50';
      message.classList.remove('hidden');
    } catch (error) {
      message.textContent = error.message || 'Could not send reset email';
      message.className = 'text-sm rounded-lg px-3 py-2 text-red-600 bg-red-50';
      message.classList.remove('hidden');
    }
  });
}

export function renderResetPassword(token) {
  const main = document.getElementById('main-content');
  main.innerHTML = `
    <div class="page-enter min-h-[calc(100vh-3.5rem)] flex items-center justify-center p-4 bg-antique-gradient">
      <div class="w-full max-w-sm"><div class="text-center mb-8"><h1 class="text-2xl font-bold text-gray-900 dark:text-white">Choose a new password</h1></div>
      <form id="reset-form" class="space-y-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <input type="password" name="password" required minlength="6" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm" placeholder="New password" />
        <input type="password" name="confirm" required minlength="6" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm" placeholder="Confirm new password" />
        <div id="reset-message" class="hidden text-sm rounded-lg px-3 py-2"></div>
        <button type="submit" class="w-full py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold text-sm">Reset password</button>
      </form></div>
    </div>`;
  document.getElementById('reset-form')?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const form = event.target;
    const message = document.getElementById('reset-message');
    if (form.password.value !== form.confirm.value) {
      message.textContent = 'Passwords do not match';
      message.className = 'text-sm rounded-lg px-3 py-2 text-red-600 bg-red-50';
      message.classList.remove('hidden');
      return;
    }
    try {
      const response = await resetPassword(token, form.password.value);
      message.textContent = response.message;
      message.className = 'text-sm rounded-lg px-3 py-2 text-green-700 bg-green-50';
      message.classList.remove('hidden');
      setTimeout(renderLogin, 1000);
    } catch (error) {
      message.textContent = error.message || 'Could not reset password';
      message.className = 'text-sm rounded-lg px-3 py-2 text-red-600 bg-red-50';
      message.classList.remove('hidden');
    }
  });
}
