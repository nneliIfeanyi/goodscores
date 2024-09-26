const staticCacheName = 'site-static-v4';
const dynamicCacheName = 'site-dynamic-v6';
const assets = [
  'site.webmanifest',
  'index.html',
  'app.js',
  'assets/css/style.css',
  'assets/vendor/bootstrap-icons/bootstrap-icons.css',
  'assets/vendor/bootstrap/css/bootstrap.min.css',
  'https://fonts.gstatic.com',
  'https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i',
  'fallback.html'
];

// cache size limit function
const limitCacheSize = (name, size) => {
  caches.open(name).then(cache => {
    cache.keys().then(keys => {
      if(keys.length > size){
        cache.delete(keys[0]).then(limitCacheSize(name, size));
      }
    });
  });
};

// install event
self.addEventListener('install', evt => {
  //console.log('service worker installed');
  evt.waitUntil(
    caches.open(staticCacheName).then((cache) => {
      console.log('caching shell assets');
      cache.addAll(assets);
    })
  );
});

// activate event
self.addEventListener('activate', evt => {
  //console.log('service worker activated');
  evt.waitUntil(
    caches.keys().then(keys => {
      //console.log(keys);
      return Promise.all(keys
        .filter(key => key !== staticCacheName && key !== dynamicCacheName)
        .map(key => caches.delete(key))
      );
    })
  );
});

// fetch event
self.addEventListener('fetch', evt => {
  //console.log('fetch event', evt);
  evt.respondWith(
    caches.match(evt.request).then(cacheRes => {
      return cacheRes || fetch(evt.request).then(fetchRes => {
        return caches.open(dynamicCacheName).then(cache => {
          cache.put(evt.request.url, fetchRes.clone());
          // check cached items size
          limitCacheSize(dynamicCacheName, 0);
          return fetchRes;
        })
      });
    }).catch(() => {
      if(evt.request.url.indexOf('.html') > -1){
        return caches.match('fallback.html');
      } 
    })
  );
});
// self.addEventListener("fetch", event => {
//     event.respondWith(
//       caches.match(event.request).then(cacheRes => {
//          return cacheRes || fetch(event.request).then(fetchRes => {
//             return caches.open(dynamicCache).then(cache => {
//                //cache.put(event.request.url, fetchRes.clone());
//                limitCache(dynamicCache, 15);
//                return fetchRes;
//             })
//          });
//       }).catch(() => caches.match('fallback.php'))
//    );

// });