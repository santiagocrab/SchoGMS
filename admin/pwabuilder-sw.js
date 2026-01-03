const CACHE_NAME = "my-site-cache-v1"
const urlsToCache = [
  "offline.html",
  "index.php", 
  "dist/css/style.min.css",
  "assets/images/logo.png", 
  "assets/images/image2.png",
  "assets/libs/jquery/dist/jquery.min.js",
  "assets/libs/popper.js/dist/umd/popper.min.js",
  "assets/libs/bootstrap/dist/js/bootstrap.min.js"
]

async function preCache() {
    const cache = await caches.open(CACHE_NAME)
    return cache.addAll(urlsToCache)
}

self.addEventListener("install", (event) => {
    console.log("Install assets success");
    event.waitUntil(preCache())
});

// =================

self.addEventListener("activate", (event) => {
    console.log("activate assets success");
    event.waitUntil(preCache())
});


// ======================
async function fetchAssets(event) {
    try{
        const response = fetch(event.request)
        return response
    }catch (err){
        const cache = await caches.open(CACHE_NAME)
        return cache.match(event.request)
    }
}

self.addEventListener("fetch", (event) => {
    console.log("fetch assets success");
    event.respondWith(fetchAssets(event))
});
