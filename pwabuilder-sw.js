// Import Workbox
importScripts('https://storage.googleapis.com/workbox-cdn/releases/5.1.2/workbox-sw.js');

const CACHE_NAME = "pwabuilder-cache-v1";
const OFFLINE_PAGE = "index.html"; // Make sure you have this file in your root directory

const ASSETS_TO_CACHE = [
  "/",
  "/index.php", 
  "/dist/css/style.min.css",
  "/script.js",
  "/assets/images/logo.png", 
  "/assets/libs/jquery/dist/jquery.min.js",
  "/assets/libs/popper.js/dist/umd/popper.min.js",
  "/assets/libs/bootstrap/dist/js/bootstrap.min.js"
];

// Listen for install event & cache assets
self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        return cache.addAll(ASSETS_TO_CACHE);
      })
      .then(() => self.skipWaiting())
  );
});

// Activate the new service worker & delete old caches
self.addEventListener("activate", (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cache) => {
          if (cache !== CACHE_NAME) {
            return caches.delete(cache);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// Intercept fetch requests & serve from cache
self.addEventListener("fetch", (event) => {
  event.respondWith(
    fetch(event.request).catch(() => {
      return caches.match(event.request).then((cachedResponse) => {
        return cachedResponse || caches.match(OFFLINE_PAGE);
      });
    })
  );
});
