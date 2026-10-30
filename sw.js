/* GoodScores service worker for the domain-root frontend. */
const CACHE_NAME = 'goodscores-v35';

// Paths are relative to the service worker at the domain root.
const CORE_ASSETS = [
  './',
  './index.html',
  './css/app.css',
  './css/tailwind.css',
  './js/app.js',
  './js/modules/auth.js',
  './js/modules/aiQuestionContract.js',
  './js/modules/aiQuestions.js',
  './js/modules/pages/account.js',
  './js/modules/pages/dashboard.js',
  './js/modules/pages/diagrams.js',
  './js/modules/pages/login.js',
  './js/modules/pages/onboarding.js',
  './js/modules/pages/papers.js',
  './js/modules/pages/questions.js',
  './js/modules/pages/tutorials.js',
  './js/modules/pages/register.js',
  './js/utils/questionBank.js',
  './js/utils/api.js',
  './js/utils/authGate.js',
  './js/utils/backup.js',
  './js/utils/math.js',
  './js/utils/paperRenderer.js',
  './js/utils/db.js',
  './js/utils/meta.js',
  './js/utils/modal.js',
  './js/utils/toast.js',
  './assets/tinymce/tinymce.min.js',
];

const OPTIONAL_ASSETS = [
  './assets/icons/favicon-32.png',
  './assets/icons/icon-192.png',
  './assets/icons/icon-512.png',
  './manifest.json',
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(async (cache) => {
      await cache.addAll(CORE_ASSETS);
      await Promise.all(OPTIONAL_ASSETS.map((asset) => cache.add(asset).catch((err) => {
        console.warn(`SW precache skipped ${asset}`, err);
      })));
      await self.skipWaiting();
    })
  );
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
  const pathname = new URL(url).pathname;
  const networkRequest = event.request.method === 'GET'
    ? new Request(event.request, { cache: 'no-store' })
    : event.request;
  const isApiRoute = /^\/(?:health|dashboard|auth|school|headers|credits|meta|questions|diagrams|passages|papers|ocr|ai|backup)(?:\/|$)/.test(pathname);

  // Never cache API / backend calls – network only
  if (pathname.includes('/backend/') || isApiRoute) {
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
      Promise.all([
        caches.match(event.request),
        caches.match(event.request, { ignoreSearch: true }),
      ]).then(([exactMatch, pathMatch]) => {
        const cached = exactMatch || pathMatch;
        const refresh = fetch(networkRequest).then((response) => {
          if (response && response.status === 200) {
            caches.open(CACHE_NAME)
              .then((cache) => cache.put(event.request, response.clone()))
              .catch((error) => console.warn(`SW cache update failed for ${url}`, error));
          }
          return response;
        }).catch(() => cached || new Response(
          event.request.destination === 'document'
            ? '<!doctype html><meta name="viewport" content="width=device-width"><title>GoodScores offline</title><p>GoodScores is not available offline on this device yet. Connect to the internet and open the app once, then try again.</p>'
            : '',
          {
            status: 503,
            headers: { 'Content-Type': event.request.destination === 'document' ? 'text/html; charset=utf-8' : 'text/javascript; charset=utf-8' },
          }
        ));
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
