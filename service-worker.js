const CACHE_NAME = 'insight-smart-cache-v1';
const urlsToCache = [
  './',              // Main page
  'Try.html',
  'manifest.json',
  'settings.html',
  'settings.js',
  'insight .jpg',
  'insight .jpg'
];

// Install Event: Cache the essential files
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('Caching essential files');
        return cache.addAll(urlsToCache);
      })
  );
});

// Activate Event: Clean old caches
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

// Fetch Event: Serve from cache first, then network, and dynamically cache new files
self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request).then((cachedResponse) => {
      if (cachedResponse) {
        // Return from cache
        return cachedResponse;
      }

      // If not in cache, fetch from network
      return fetch(event.request).then((networkResponse) => {
        // Save a copy to cache
        return caches.open(CACHE_NAME).then((cache) => {
          // Important: only cache valid responses
          if (event.request.url.startsWith('http') && networkResponse.status === 200) {
            cache.put(event.request, networkResponse.clone());
          }
          return networkResponse;
        });
      }).catch(() => {
        // Optionally: serve a fallback page if offline and resource not found
        // return caches.match('/offline.html');
      });
    })
  );
});
