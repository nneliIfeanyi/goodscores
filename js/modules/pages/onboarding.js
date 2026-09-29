import { renderLogin } from './login.js';
import { renderRegister } from './register.js';

const slides = [
  {
    icon: '<svg viewBox="0 0 120 120" aria-hidden="true"><rect x="25" y="20" width="70" height="80" rx="8" fill="none" stroke="currentColor" stroke-width="5"/><path d="M40 43h25M40 60h25M40 77h18M79 51v26M66 64h26" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round"/></svg>',
    title: 'Build Your Question Bank',
    text: 'Easily create and organise multiple-choice, fill-in-the-gap and theory questions for every subject and term.',
  },
  {
    icon: '<svg viewBox="0 0 120 120" aria-hidden="true"><path d="M31 15h43l15 15v75H31z" fill="none" stroke="currentColor" stroke-width="5"/><path d="M74 15v17h15M44 51h32M44 67h32M44 83h23" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round"/></svg>',
    title: 'Generate Exam Papers in Minutes',
    text: 'Select questions, arrange them, add instructions and export clean, print-ready PDFs with your school branding.',
  },
  {
    icon: '<svg viewBox="0 0 120 120" aria-hidden="true"><rect x="35" y="15" width="50" height="90" rx="10" fill="none" stroke="currentColor" stroke-width="5"/><path d="M48 30h24M51 88h18M20 54h15M85 54h15M27 38l9 9M93 38l-9 9" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round"/><path d="M49 63l9 9 15-19" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    title: 'Scan Questions Instantly',
    text: 'Use OCR to capture printed questions with your camera. Attach diagrams, manage credits and work offline when you have an active plan.',
  },
  {
    icon: '<svg viewBox="0 0 120 120" aria-hidden="true"><circle cx="43" cy="43" r="16" fill="none" stroke="currentColor" stroke-width="5"/><circle cx="78" cy="43" r="16" fill="none" stroke="currentColor" stroke-width="5"/><path d="M18 91c2-17 13-25 25-25s23 8 25 25M52 91c2-17 13-25 25-25s23 8 25 25" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round"/></svg>',
    title: 'For Teachers and Schools',
    text: 'Register as an individual teacher or as a school. Schools receive a unique ID that automatically upgrades linked teachers to Pro features.',
  },
];

function finishOnboarding() {
  localStorage.setItem('gs_onboarding_complete', '1');
  renderAccountTypeSelection();
}

function showAppHeader() {
  document.getElementById('app-header')?.classList.remove('hidden');
}

function renderAccountTypeSelection() {
  const main = document.getElementById('main-content');
  document.getElementById('app-header')?.classList.add('hidden');
  document.getElementById('bottom-nav')?.classList.add('hidden');
  main.innerHTML = `
    <div class="page-enter min-h-[calc(100vh-3.5rem)] flex items-center justify-center p-4 bg-antique-gradient">
      <div class="w-full max-w-md text-center">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-primary-600 dark:text-primary-400">Get started</p>
        <h1 class="mt-2 text-2xl font-bold">Choose your account</h1>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Start with the workspace that fits the way you teach.</p>
        <div class="mt-8 grid gap-3 text-left">
          <button data-account-type="individual" class="rounded-2xl border border-primary-200 dark:border-primary-800 bg-white dark:bg-gray-800 p-5 shadow-sm hover:-translate-y-0.5 transition">
            <span class="text-2xl" aria-hidden="true">✦</span><span class="ml-3 font-semibold">Individual teacher</span>
            <span class="mt-1 block pl-9 text-sm text-gray-500 dark:text-gray-400">Build your own question bank and papers.</span>
          </button>
          <button data-account-type="school" class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm hover:-translate-y-0.5 transition">
            <span class="text-2xl" aria-hidden="true">▦</span><span class="ml-3 font-semibold">School</span>
            <span class="mt-1 block pl-9 text-sm text-gray-500 dark:text-gray-400">Create a school workspace and link teachers.</span>
          </button>
        </div>
        <button id="account-type-login" class="mt-6 text-sm font-semibold text-primary-600 dark:text-primary-400 hover:underline">I already have an account</button>
      </div>
    </div>`;

  document.querySelectorAll('[data-account-type]').forEach((button) => {
    button.addEventListener('click', () => {
      showAppHeader();
      renderRegister(button.dataset.accountType);
    });
  });
  document.getElementById('account-type-login')?.addEventListener('click', () => {
    showAppHeader();
    renderLogin();
  });
}

export function renderOnboarding() {
  const main = document.getElementById('main-content');
  document.getElementById('app-header')?.classList.add('hidden');
  document.getElementById('bottom-nav')?.classList.add('hidden');
  let current = 0;

  const draw = () => {
    const slide = slides[current];
    const isLast = current === slides.length - 1;
    main.innerHTML = `
      <section class="onboarding-slide min-h-[calc(100vh-3.5rem)] flex flex-col bg-antique-gradient">
        <div class="flex items-center justify-between px-5 pt-5">
          ${current > 0 ? '<button id="onboarding-back" class="text-sm font-semibold text-gray-500 dark:text-gray-400 hover:text-primary-600">Back</button>' : '<span></span>'}
          <span class="text-sm font-bold text-primary-700 dark:text-primary-300">GoodScores</span>
          <button id="onboarding-skip" class="text-sm font-semibold text-gray-500 dark:text-gray-400 hover:text-primary-600">Skip</button>
        </div>
        <div class="flex flex-1 flex-col items-center justify-center px-6 pb-5 text-center">
          <div class="onboarding-art flex h-56 w-56 items-center justify-center rounded-[2rem] text-primary-700 dark:text-primary-300 sm:h-64 sm:w-64">${slide.icon}</div>
          <div class="mt-8 max-w-md"><h1 class="text-2xl font-bold tracking-tight sm:text-3xl">${slide.title}</h1><p class="mt-3 text-sm leading-6 text-gray-600 dark:text-gray-300">${slide.text}</p></div>
        </div>
        <div class="px-6 pb-7 safe-bottom">
          <div class="mb-5 flex justify-center gap-2" aria-label="Onboarding progress">${slides.map((_, index) => `<span class="h-2 rounded-full transition-all ${index === current ? 'w-7 bg-primary-600' : 'w-2 bg-primary-200 dark:bg-primary-800'}"></span>`).join('')}</div>
          <button id="onboarding-next" class="w-full rounded-xl bg-primary-600 py-3 text-sm font-semibold text-white">${isLast ? 'Get Started' : 'Next'}</button>
          ${isLast ? '<button id="onboarding-login" class="mt-4 block w-full text-center text-sm font-semibold text-primary-600 dark:text-primary-400 hover:underline">I already have an account</button>' : ''}
        </div>
      </section>`;

    let touchStartX = null;
    const pager = main.querySelector('.onboarding-slide');
    pager?.addEventListener('touchstart', (event) => {
      touchStartX = event.changedTouches[0]?.clientX ?? null;
    }, { passive: true });
    pager?.addEventListener('touchend', (event) => {
      if (touchStartX === null) return;
      const distance = event.changedTouches[0].clientX - touchStartX;
      touchStartX = null;
      if (Math.abs(distance) < 45) return;
      if (distance < 0 && current < slides.length - 1) {
        current += 1;
        draw();
      } else if (distance > 0 && current > 0) {
        current -= 1;
        draw();
      }
    }, { passive: true });

    document.getElementById('onboarding-back')?.addEventListener('click', () => {
      current -= 1;
      draw();
    });
    document.getElementById('onboarding-skip')?.addEventListener('click', finishOnboarding);
    document.getElementById('onboarding-next')?.addEventListener('click', () => {
      if (isLast) finishOnboarding();
      else { current += 1; draw(); }
    });
    document.getElementById('onboarding-login')?.addEventListener('click', () => {
      localStorage.setItem('gs_onboarding_complete', '1');
      showAppHeader();
      renderLogin();
    });
  };

  draw();
}