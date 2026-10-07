const tutorials = [
  {
    id: 'getting-started',
    title: 'Getting started with GoodScores',
    category: 'Getting Started',
    description: 'A quick tour of the workspace and the main tools for creating questions and exam papers.',
    duration: 'Quick guide',
    embedUrl: 'https://embed.app.guidde.com/playbooks/1Tfe9q2bULQmMhX7hcVjhh?mode=videoOnly',
  },
  {
    id: 'video-guide-2',
    title: 'GoodScores video guide 2',
    category: 'Getting Started',
    description: 'Another step-by-step guide to help you use GoodScores with confidence.',
    duration: 'Quick guide',
    embedUrl: 'https://embed.app.guidde.com/playbooks/cZ8nkZSYu2UaJaNHBtjPEK?mode=videoOnly',
  },
];

function renderTutorialCard(tutorial) {
  return `
    <button type="button" data-tutorial-id="${tutorial.id}" class="group w-full text-left rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-primary-300 dark:hover:border-primary-700">
      <div class="flex items-start gap-3">
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300">
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.868v4.264a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </span>
        <span class="min-w-0">
          <span class="text-[10px] font-semibold uppercase tracking-wide text-primary-600 dark:text-primary-400">${tutorial.category}</span>
          <span class="mt-1 block font-semibold text-gray-900 dark:text-white">${tutorial.title}</span>
          <span class="mt-1 block text-xs leading-5 text-gray-500 dark:text-gray-400">${tutorial.description}</span>
          <span class="mt-2 block text-[11px] text-gray-400">${tutorial.duration}</span>
        </span>
      </div>
    </button>`;
}

let tutorialOverlayCleanup = null;

function closeTutorialOverlay() {
  if (tutorialOverlayCleanup) {
    tutorialOverlayCleanup();
    return;
  }
  document.getElementById('tutorial-video-overlay')?.remove();
}

function openTutorialOverlay(tutorial) {
    closeTutorialOverlay();
    const overlay = document.createElement('div');
    overlay.id = 'tutorial-video-overlay';
    overlay.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black/75 p-3 sm:p-6';
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-modal', 'true');
    overlay.setAttribute('aria-labelledby', 'tutorial-overlay-title');
    overlay.innerHTML = `
      <div class="relative w-full max-w-4xl overflow-hidden rounded-2xl bg-gray-950 shadow-2xl">
        <div class="flex items-center justify-between gap-3 border-b border-white/10 px-4 py-3 text-white">
          <div class="min-w-0">
            <p class="text-[10px] font-semibold uppercase tracking-wide text-primary-300">${tutorial.category}</p>
            <h2 id="tutorial-overlay-title" class="truncate text-sm font-semibold sm:text-base">${tutorial.title}</h2>
          </div>
          <button type="button" id="tutorial-overlay-close" class="shrink-0 rounded-lg p-2 text-white/80 transition hover:bg-white/10 hover:text-white" aria-label="Close tutorial" title="Close tutorial">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div class="aspect-video w-full bg-black">
          <iframe class="h-full w-full" src="${tutorial.embedUrl}" title="${tutorial.title}" allow="fullscreen; autoplay" referrerpolicy="strict-origin-when-cross-origin"></iframe>
        </div>
      </div>`;

    document.body.appendChild(overlay);
    overlay.querySelector('#tutorial-overlay-close').addEventListener('click', closeTutorialOverlay);
    overlay.addEventListener('click', (event) => {
      if (event.target === overlay) closeTutorialOverlay();
    });
    const onKeydown = (event) => {
      if (event.key === 'Escape') {
        closeTutorialOverlay();
      }
    };
    tutorialOverlayCleanup = () => {
      document.removeEventListener('keydown', onKeydown);
      overlay.querySelector('iframe')?.removeAttribute('src');
      overlay.remove();
      tutorialOverlayCleanup = null;
    };
    document.addEventListener('keydown', onKeydown);
    overlay.querySelector('#tutorial-overlay-close').focus();
  }

export function renderTutorials() {
  const main = document.getElementById('main-content');
    if (!main || !tutorials.length) return;
    closeTutorialOverlay();

  main.innerHTML = `
    <div class="page-enter max-w-3xl mx-auto p-4 pb-24 space-y-5">
      <div>
        <div class="flex items-center justify-between gap-3">
          <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-primary-600 dark:text-primary-400">Learn GoodScores</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight">Video tutorials</h1>
          </div>
          <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-600 text-white shadow-sm" aria-hidden="true">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 19h8a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          </span>
        </div>
        <p class="mt-2 max-w-xl text-sm leading-6 text-gray-500 dark:text-gray-400">Short guides to help you get more from your question bank and paper builder.</p>
      </div>

      <section aria-labelledby="tutorial-list-title">
        <div class="my-3 flex items-center justify-between gap-3">
          <h2 id="tutorial-list-title" class="font-semibold">All tutorials</h2>
          <span class="text-xs text-gray-400">${tutorials.length} guide${tutorials.length === 1 ? '' : 's'}</span>
        </div>
        <div class="grid gap-3 sm:grid-cols-2" id="tutorial-list">
          ${tutorials.map((tutorial) => renderTutorialCard(tutorial)).join('')}
        </div>
      </section>

      <p class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 mt-3 text-xs leading-5 text-amber-800 dark:border-amber-900/60 dark:bg-amber-950/30 dark:text-amber-200">Video guides need an internet connection to stream.</p>
    </div>`;

  main.querySelectorAll('[data-tutorial-id]').forEach((button) => {
    button.addEventListener('click', () => {
      const tutorial = tutorials.find((item) => item.id === button.dataset.tutorialId);
      if (tutorial) openTutorialOverlay(tutorial);
    });
  });
}
