const CACHE_NAME = 'schogms-app-v2';
const urlsToCache = [
  'offline.html',
  'index.php',
  'dist/css/style.min.css',
  'assets/images/logo.png',
  'assets/libs/jquery/dist/jquery.min.js',
  'assets/libs/popper.js/dist/umd/popper.min.js',
  'assets/libs/bootstrap/dist/js/bootstrap.min.js',
];

async function preCache() {
  const cache = await caches.open(CACHE_NAME);
  return cache.addAll(urlsToCache);
}

self.addEventListener('install', (event) => {
  event.waitUntil(preCache());
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) =>
      Promise.all(
        cacheNames.map((cache) => {
          if (cache !== CACHE_NAME) {
            return caches.delete(cache);
          }
        })
      )
    ).then(() => self.clients.claim())
  );
});

function shouldCacheRequest(request) {
  if (request.method !== 'GET') {
    return false;
  }
  const url = new URL(request.url);
  if (url.protocol !== 'http:' && url.protocol !== 'https:') {
    return false;
  }
  if (url.pathname.endsWith('.php')) {
    return false;
  }
  return true;
}

self.addEventListener('fetch', (event) => {
  if (!shouldCacheRequest(event.request)) {
    return;
  }

  event.respondWith(
    fetch(event.request)
      .then((response) => {
        if (!response || response.status !== 200 || response.type !== 'basic') {
          return response;
        }
        const responseClone = response.clone();
        caches.open(CACHE_NAME).then((cache) => {
          cache.put(event.request, responseClone);
        });
        return response;
      })
      .catch(async () => {
        const cache = await caches.open(CACHE_NAME);
        const cached = await cache.match(event.request);
        return cached || caches.match('offline.html');
      })
  );
});
