import { isLoggedIn, fetchMe } from './modules/auth.js';
import { getUser, clearToken } from './utils/api.js';
import { renderLogin, renderResetPassword } from './modules/pages/login.js';
import { renderRegister } from './modules/pages/register.js';
import { renderDashboard } from './modules/pages/dashboard.js';
import { renderAccount } from './modules/pages/account.js';
import { renderQuestions } from './modules/pages/questions.js';
import { renderPapers } from './modules/pages/papers.js';
import { api } from './utils/api.js';
import { toast } from './utils/toast.js';
import { renderOnboarding } from './modules/pages/onboarding.js';


// ---------- Online / offline banner ----------
function initConnectivity() {
  const bannerId = 'offline-banner';
  function show(offline) {
    let el = document.getElementById(bannerId);
    if (!offline) {
      el?.remove();
      return;
    }
    if (!el) {
      el = document.createElement('div');
      el.id = bannerId;
      el.className = 'fixed top-14 inset-x-0 z-50 text-center text-xs font-medium py-1.5 bg-amber-500 text-white';
      el.textContent = 'You are offline – drafts save locally until you reconnect';
      document.body.appendChild(el);
    }
  }
  window.addEventListener('offline', () => show(true));
  window.addEventListener('online', () => show(false));
  if (!navigator.onLine) show(true);
}

// ---------- Theme ----------
function initTheme() {
  const saved = localStorage.getItem('gs_theme') || 'light';
  const toggle = document.getElementById('theme-toggle');
  const themeToggle = toggle?.cloneNode(true);
  if (toggle && themeToggle) toggle.replaceWith(themeToggle);
  const applyTheme = (isDark) => {
    document.documentElement.classList.toggle('dark', isDark);
    themeToggle?.setAttribute('aria-pressed', String(isDark));
    themeToggle?.setAttribute('title', isDark ? 'Switch to light mode' : 'Switch to dark mode');
  };
  applyTheme(saved === 'dark');

  themeToggle?.addEventListener('click', () => {
    const isDark = !document.documentElement.classList.contains('dark');
    applyTheme(isDark);
    localStorage.setItem('gs_theme', isDark ? 'dark' : 'light');
    window.dispatchEvent(new CustomEvent('gs-theme-change', { detail: { dark: isDark } }));
  });
}

// ---------- Splash ----------
function hideSplash() {
  const splash = document.getElementById('splash');
  if (!splash) return;
  splash.classList.add('fade-out');
  setTimeout(() => {
    if (typeof window.__goodscoresRevealApp === 'function') window.__goodscoresRevealApp();
    else {
      splash.classList.add('hidden');
      document.getElementById('app')?.classList.remove('hidden');
    }
  }, 400);
}

function fetchMeWithTimeout(timeoutMs = 4000) {
  return Promise.race([
    fetchMe(),
    new Promise((_, reject) => {
      setTimeout(() => reject(new Error('Authentication refresh timed out')), timeoutMs);
    }),
  ]);
}

// ---------- Bottom Nav ----------
function setActiveNav(page) {
  const items = document.querySelectorAll('.nav-item');
  items.forEach((item) => {
    const isActive = item.dataset.page === page;
    item.classList.toggle('active', isActive);
    item.classList.toggle('text-primary-600', isActive);
    item.classList.toggle('dark:text-primary-400', isActive);
    item.classList.toggle('text-gray-500', !isActive);
    item.classList.toggle('dark:text-gray-400', !isActive);
  });
}

function initNav() {
  const items = document.querySelectorAll('.nav-item');
  items.forEach((btn) => {
    btn.addEventListener('click', () => {
      navigate(btn.dataset.page);
    });
  });
}

// ---------- Router ----------
const pages = {
  dashboard: renderDashboard,
  questions: renderQuestions,
  papers: renderPapers,
  account: renderAccount,
};

function navigate(page) {
  const fn = pages[page] || pages.dashboard;
  const route = pages[page] ? page : 'dashboard';
  if (window.location.hash !== `#${route}`) {
    window.history.replaceState({}, '', `#${route}`);
  }
  setActiveNav(route);
  fn();
}

function getInitialPage() {
  const route = window.location.hash.slice(1);
  return pages[route] ? route : 'dashboard';
}

// ---------- Credits badge ----------
export function updateCreditsBadge(user) {
  const badge = document.getElementById('credits-badge');
  const value = document.getElementById('credits-value');
  if (!badge || !value) return;

  if (user) {
    badge.classList.remove('hidden');
    if (user.is_unlimited || user.credits === -1) {
      value.textContent = 'Unlimited';
    } else {
      value.textContent = user.credits ?? 0;
    }
  } else {
    badge.classList.add('hidden');
  }
}

// ---------- PWA Install ----------
let deferredPrompt = null;
window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault();
  deferredPrompt = e;
  document.getElementById('install-banner')?.classList.remove('hidden');
});

document.getElementById('btn-install')?.addEventListener('click', async () => {
  if (!deferredPrompt) return;
  deferredPrompt.prompt();
  await deferredPrompt.userChoice;
  deferredPrompt = null;
  document.getElementById('install-banner')?.classList.add('hidden');
});

document.getElementById('btn-install-dismiss')?.addEventListener('click', () => {
  document.getElementById('install-banner')?.classList.add('hidden');
});

// ---------- Service Worker ----------
function showAppUpdatePrompt() {
  if (document.getElementById('app-update-banner')) return;
  const banner = document.createElement('div');
  banner.id = 'app-update-banner';
  banner.className = 'fixed top-16 inset-x-4 z-50 mx-auto flex max-w-md items-center gap-3 rounded-xl bg-primary-700 p-3 text-white shadow-lg';
  banner.innerHTML = `
    <p class="flex-1 text-xs font-medium">A new GoodScores version is available.</p>
    <button type="button" data-update-refresh class="rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-primary-700">Refresh</button>
    <button type="button" data-update-dismiss class="p-1 text-lg leading-none opacity-80 hover:opacity-100" aria-label="Dismiss">×</button>`;
  document.body.appendChild(banner);
  banner.querySelector('[data-update-refresh]').addEventListener('click', () => window.location.reload());
  banner.querySelector('[data-update-dismiss]').addEventListener('click', () => banner.remove());
}

if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('./sw.js', { updateViaCache: 'none' })
    .then((registration) => {
      registration.addEventListener('updatefound', () => {
        const worker = registration.installing;
        if (!worker) return;
        worker.addEventListener('statechange', () => {
          if (worker.state === 'installed' && navigator.serviceWorker.controller) {
            showAppUpdatePrompt();
          }
        });
      });
      return registration.update();
    })
    .catch(console.warn);
}

// ---------- Boot ----------
async function boot() {
  try {
    initTheme();
    initNav();
    initConnectivity();

    document.getElementById('btn-settings')?.addEventListener('click', () => {
      document.querySelector('[data-page="account"]')?.click();
    });

    await new Promise((resolve) => setTimeout(resolve, 1900));

    const resetToken = new URLSearchParams(window.location.search).get('reset_token');
    if (resetToken) {
      window.history.replaceState({}, document.title, window.location.pathname);
      hideSplash();
      renderResetPassword(resetToken);
      return;
    }

    // The question bank is local-first. Authentication is requested by gated features.
    updateCreditsBadge(getUser());
    hideSplash();
    const initialPage = getInitialPage();
    navigate(initialPage);
    if (!isLoggedIn() || !navigator.onLine) return;

    const paymentReference = new URLSearchParams(window.location.search).get('reference');
    if (paymentReference) {
      window.history.replaceState({}, document.title, window.location.pathname);
      try {
        const result = await api('/credits/verify', {
          method: 'POST',
          body: JSON.stringify({ reference: paymentReference }),
        });
        toast(result.data?.message || 'Payment confirmed', 'success');
      } catch (err) {
        toast(err.message || 'Payment could not be confirmed', 'error');
      }
    }

    try {
      const user = await fetchMeWithTimeout();
      updateCreditsBadge(user);
      hideSplash();
      navigate(initialPage);
    } catch (err) {
      if (err.status === 401) {
        console.warn('Session expired', err);
        clearToken();
        hideSplash();
        renderLogin();
        return;
      }

      // Keep the cached session available when the server is temporarily unreachable.
      console.warn('Could not refresh session; using cached user', err);
      const cachedUser = getUser();
      updateCreditsBadge(cachedUser);
      hideSplash();
      navigate(initialPage);
    }
  } catch (fatalErr) {
    // Never leave the splash spinning forever – fall back to the login screen.
    console.error('Boot failed unexpectedly', fatalErr);
    hideSplash();
    renderLogin();
  }
}

boot();
