/* GoodScores service worker for the domain-root frontend. */
const CACHE_NAME = 'goodscores-v30';

// Paths are relative to the service worker at the domain root.
const ASSETS = [
  './',
  './index.html',
  './css/tailwind.css',
  './js/app.js',
  './js/modules/auth.js',
  './js/modules/aiQuestionContract.js',
  './js/modules/aiQuestions.js',
  './js/modules/pages/account.js',
  './js/modules/pages/dashboard.js',
  './js/modules/pages/login.js',
  './js/modules/pages/onboarding.js',
  './js/modules/pages/papers.js',
  './js/modules/pages/questions.js',
  './js/modules/pages/tutorials.js',
  './js/modules/pages/register.js',
  './js/utils/api.js',
  './js/utils/authGate.js',
  './js/utils/backup.js',
  './js/utils/paperRenderer.js',
  './js/utils/db.js',
  './js/utils/meta.js',
  './js/utils/modal.js',
  './js/utils/toast.js',
  './assets/tinymce/tinymce.min.js',
  './assets/icons/favicon-32.png',
  './assets/icons/icon-192.png',
  './assets/icons/icon-512.png',
  './manifest.json',
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => Promise.all(
      ASSETS.map((asset) => cache.add(asset).catch((err) => {
        console.warn(`SW precache skipped ${asset}`, err);
      }))
    ))
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k)))
    )
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  const url = event.request.url;
  const networkRequest = event.request.method === 'GET'
    ? new Request(event.request, { cache: 'no-store' })
    : event.request;

  // Never cache API / backend calls – network only
  if (url.includes('/backend/') || url.includes('/auth/') || url.includes('/questions') || url.includes('/papers') || url.includes('/credits') || url.includes('/meta/') || url.includes('/backup/')) {
    event.respondWith(
      fetch(networkRequest).catch(() =>
        new Response(JSON.stringify({ error: 'Offline – cannot reach server' }), {
          status: 503,
          headers: { 'Content-Type': 'application/json' },
        })
      )
    );
    return;
  }

  // Use cached shell code immediately. Registration.update() and the update prompt
  // still make new versions available without blocking a cold or offline startup.
  const isCode = event.request.method === 'GET' && (
    event.request.destination === 'document' ||
    event.request.destination === 'script' ||
    url.endsWith('.html') ||
    url.endsWith('.js')
  );

  if (isCode) {
    event.respondWith(
      caches.match(event.request).then((cached) => {
        const refresh = fetch(networkRequest).then((response) => {
          if (response && response.status === 200) {
            caches.open(CACHE_NAME).then((cache) => cache.put(event.request, response.clone()));
          }
          return response;
        }).catch(() => cached);
        return cached || refresh;
      })
    );
    return;
  }

  // Always prefer the server. Use the cache only when the request fails offline.
  event.respondWith(
    fetch(networkRequest).then((response) => {
      if (response && response.status === 200 && event.request.method === 'GET') {
        const clone = response.clone();
        caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
      }
      return response;
    }).catch(() => caches.match(event.request, { ignoreSearch: true }))
  );
});
