import { register } from '../auth.js';
import { renderLogin } from './login.js';
import { updateCreditsBadge } from '../../app.js';
import { resumeAuthenticationAction } from '../../utils/authGate.js';

export function renderRegister(initialType = 'individual') {
  const main = document.getElementById('main-content');
  document.getElementById('bottom-nav')?.classList.add('hidden');

  main.innerHTML = `
    <div class="page-enter min-h-[calc(100vh-3.5rem)] flex items-center justify-center p-4 bg-antique-gradient">
      <div class="w-full max-w-md">
        <div class="text-center mb-6">
          <h1 class="text-2xl font-bold">Create account</h1>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose how you want to register</p>
        </div>

        <!-- Type selector -->
        <div class="flex rounded-xl bg-gray-100 dark:bg-gray-800 p-1 mb-6">
          <button type="button" data-type="individual" class="type-btn flex-1 py-2 text-sm font-medium rounded-lg bg-white dark:bg-gray-700 shadow text-primary-700 dark:text-primary-300">
            Individual / Teacher
          </button>
          <button type="button" data-type="school" class="type-btn flex-1 py-2 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-400">
            School
          </button>
        </div>

        <form id="register-form" class="space-y-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
          <input type="hidden" name="type" value="individual" />

          <!-- Common fields -->
          <div>
            <label class="block text-sm font-medium mb-1.5">Full name</label>
            <input type="text" name="name" required
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm"
              placeholder="Your full name" />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1.5">Email</label>
            <input type="email" name="email" required
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm"
              placeholder="you@example.com" />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1.5">Password</label>
            <input type="password" name="password" required minlength="6"
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm"
              placeholder="Min 6 characters" />
          </div>

          <!-- Individual only: School ID -->
          <div id="school-id-field">
            <label class="block text-sm font-medium mb-1.5">School ID <span class="text-gray-400 font-normal">(optional)</span></label>
            <input type="text" name="school_id"
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm uppercase"
              placeholder="SCH-XXXXXX" />
            <p class="mt-1 text-xs text-gray-500">If you have a School ID you will be linked as a teacher and inherit Pro features.</p>
          </div>

          <!-- School only fields -->
          <div id="school-fields" class="hidden space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1.5">Admin full name</label>
              <input type="text" name="admin_name"
                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm"
                placeholder="School admin name" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1.5">School address</label>
              <input type="text" name="address"
                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm"
                placeholder="Optional" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1.5">Phone</label>
              <input type="tel" name="phone"
                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm"
                placeholder="Optional" />
            </div>
          </div>

          <div id="register-error" class="hidden text-sm text-red-600 bg-red-50 dark:bg-red-900/20 rounded-lg px-3 py-2"></div>

          <button type="submit"
            class="w-full py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold text-sm transition">
            Create account
          </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
          Already have an account?
          <button id="goto-login" class="text-primary-600 dark:text-primary-400 font-medium hover:underline">Sign in</button>
        </p>
      </div>
    </div>
  `;

  // Type toggle
  const typeBtns = document.querySelectorAll('.type-btn');
  typeBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
      typeBtns.forEach((b) => {
        b.classList.remove('bg-white', 'dark:bg-gray-700', 'shadow', 'text-primary-700', 'dark:text-primary-300');
        b.classList.add('text-gray-600', 'dark:text-gray-400');
      });
      btn.classList.add('bg-white', 'dark:bg-gray-700', 'shadow', 'text-primary-700', 'dark:text-primary-300');
      btn.classList.remove('text-gray-600', 'dark:text-gray-400');

      const type = btn.dataset.type;
      document.querySelector('input[name="type"]').value = type;

      if (type === 'school') {
        document.getElementById('school-id-field').classList.add('hidden');
        document.getElementById('school-fields').classList.remove('hidden');
        document.querySelector('input[name="name"]').placeholder = 'School name';
      } else {
        document.getElementById('school-id-field').classList.remove('hidden');
        document.getElementById('school-fields').classList.add('hidden');
        document.querySelector('input[name="name"]').placeholder = 'Your full name';
      }
    });
  });

  if (initialType === 'school') {
    document.querySelector('[data-type="school"]')?.click();
  }

  document.getElementById('goto-login')?.addEventListener('click', renderLogin);

  document.getElementById('register-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const btn = form.querySelector('button[type="submit"]');
    const errEl = document.getElementById('register-error');
    errEl.classList.add('hidden');

    const payload = {
      type: form.type.value,
      name: form.name.value.trim(),
      email: form.email.value.trim(),
      password: form.password.value,
    };

    if (payload.type === 'individual') {
      payload.school_id = form.school_id.value.trim() || undefined;
    } else {
      payload.admin_name = form.admin_name.value.trim() || undefined;
      payload.address = form.address.value.trim() || undefined;
      payload.phone = form.phone.value.trim() || undefined;
    }

    btn.disabled = true;
    btn.textContent = 'Creating…';

    try {
      const res = await register(payload);
      updateCreditsBadge(res.data.user);
      document.getElementById('bottom-nav')?.classList.remove('hidden');
      await resumeAuthenticationAction();
      document.querySelector('[data-page="dashboard"]')?.click();
    } catch (err) {
      errEl.textContent = err.message || 'Registration failed';
      errEl.classList.remove('hidden');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Create account';
    }
  });
}
