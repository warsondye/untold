const CACHE_NAME = 'insight-smart-cache-v2';
const MAX_CACHE_ITEMS = 50; // Limit to 50 files

const urlsToCache = [
  './',
  '/Try.html',
  '/manifest.json',
  '/settings.html',
  '/settings.js',
  '/insight.jpg'
];

// Install Event
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('Caching essential files');
        return cache.addAll(urlsToCache);
      })
  );
});

// Activate Event
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME) {
            console.log('Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

// Helper to limit cache size
async function limitCacheSize(cacheName, maxItems) {
  const cache = await caches.open(cacheName);
  const keys = await cache.keys();
  if (keys.length > maxItems) {
    await cache.delete(keys[0]);
    limitCacheSize(cacheName, maxItems); // Recursively remove extra files
  }
}

// Fetch Event
self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request).then((cachedResponse) => {
      if (cachedResponse) {
        return cachedResponse;
      }

      return fetch(event.request).then((networkResponse) => {
        return caches.open(CACHE_NAME).then((cache) => {
          if (event.request.url.startsWith('http') && networkResponse.status === 200) {
            cache.put(event.request, networkResponse.clone());
            limitCacheSize(CACHE_NAME, MAX_CACHE_ITEMS); // Apply limit
          }
          return networkResponse;
        });
      }).catch(() => {
        // Optional: show offline fallback page
        return caches.match('/offline.html');
      });
    })
  );
});
