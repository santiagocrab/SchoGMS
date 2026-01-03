// const CACHE_NAME = "my-site-cache-v1"
// const urlsToCache = [
//     "https://schogms.cloudhost.host/SchoGMS/serviceWorker-sw.js",
//   "offline.html",
//   "index.php", 
//   "dist/css/style.min.css",
//   "assets/images/logo.png", 
//   "assets/images/image2.png",
//   "assets/libs/jquery/dist/jquery.min.js",
//   "assets/libs/popper.js/dist/umd/popper.min.js",
//   "assets/libs/bootstrap/dist/js/bootstrap.min.js"
// ]

// async function preCache() {
//     const cache = await caches.open(CACHE_NAME)
//     return cache.addAll(urlsToCache)
// }

// self.addEventListener("install", event => {
//     console.log("Install assets success");
//     event.waitUntil(preCache())
// })

// // =================

// self.addEventListener("activate", event => {
//     console.log("activate assets success");
//     // event.waitUntil(preCache())
// })


// // ======================
// async function fetchAssets(event) {
//     try{
//         const response = await fetch(event.request)
//         return response
//     }catch (err){
//         const cache = await caches.open(CACHE_NAME)
//         return cache.match(event.request)
//     }
// }

// self.addEventListener("fetch", event => {
//     console.log("fetch assets success");
//     event.respondWith(fetchAssets(event))
// })
const CACHE_NAME = "my-site-cache-v1";
const urlsToCache = [
  "offline.html",
  "index.php",
  "dist/css/style.min.css",
  "assets/images/logo.png",
  "assets/images/image2.png",
  "assets/libs/jquery/dist/jquery.min.js",
  "assets/libs/popper.js/dist/umd/popper.min.js",
  "assets/libs/bootstrap/dist/js/bootstrap.min.js",
];

// ✅ Function to pre-cache assets
async function preCache() {
  const cache = await caches.open(CACHE_NAME);
  return cache.addAll(urlsToCache);
}

// ✅ Install event
self.addEventListener("install", (event) => {
  console.log("Service Worker: Installed");
  event.waitUntil(preCache());
});

// ✅ Activate event
self.addEventListener("activate", (event) => {
  console.log("Service Worker: Activated");
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cache) => {
          if (cache !== CACHE_NAME) {
            console.log("Service Worker: Clearing old cache", cache);
            return caches.delete(cache);
          }
        })
      );
    })
  );
});

// ✅ Fetch event
self.addEventListener("fetch", (event) => {
  console.log("Service Worker: Fetching", event.request.url);

  event.respondWith(
    fetch(event.request)
      .then((response) => {
        // Clone the response to cache it
        const responseClone = response.clone();
        caches.open(CACHE_NAME).then((cache) => {
          cache.put(event.request, responseClone);
        });
        return response;
      })
      .catch(async () => {
        const cache = await caches.open(CACHE_NAME);
        return cache.match(event.request).then((cachedResponse) => {
          return cachedResponse || caches.match("offline.html");
        });
      })
  );
});
