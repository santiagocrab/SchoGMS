const CACHE_NAME = "my-site-cache-v1"
const urlsToCache = [
    "../../assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css",
    "../../assets/extra-libs/c3/c3.min.css",
    "../../assets/libs/chartist/dist/chartist.min.css",
    "../../assets/extra-libs/jvector/jquery-jvectormap-2.0.2.css",
    "../../dist/css/style.min.css",
    "../../assets/images/logo.png",
    "https://cdnjs.cloudflare.com/ajax/libs/c3/0.7.20/c3.min.css",
    "https://cdnjs.cloudflare.com/ajax/libs/c3/0.7.20/c3.min.js",
    "https://cdn.jsdelivr.net/npm/chartist/dist/chartist.min.css",
    "https://cdn.jsdelivr.net/npm/chartist/dist/chartist.min.js",
    "../../dist/js/app-style-switcher.js",
    "../../dist/js/feather.min.js",
    "../../assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js",
    "../../dist/js/sidebarmenu.js",
    "../../dist/js/custom.min.js",
    "../../assets/extra-libs/c3/d3.min.js",
    "../../assets/extra-libs/c3/c3.min.js",
    "../../assets/libs/chartist/dist/chartist.min.js",
    "../../assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js",
    "../../assets/extra-libs/jvector/jquery-jvectormap-2.0.2.min.js",
    "../../assets/extra-libs/jvector/jquery-jvectormap-world-mill-en.js",
    "../../dist/js/pages/dashboards/dashboard1.min.js",
    "../../assets/extra-libs/datatables.net/js/jquery.dataTables.min.js",
    "../../dist/js/pages/datatable/datatable-basic.init.js"
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
