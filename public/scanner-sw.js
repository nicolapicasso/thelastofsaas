/**
 * TLOS Scanner Service Worker
 * Provides offline support for the check-in scanner PWA
 */

const CACHE_NAME = 'tlos-scanner-v1';
const ASSETS_TO_CACHE = [
    '/admin/tickets/scanner',
    '/assets/css/admin.css',
    '/assets/js/admin.js',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css',
    'https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js'
];

// Install event - cache essential assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(ASSETS_TO_CACHE);
        })
    );
    self.skipWaiting();
});

// Activate event - clean old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name !== CACHE_NAME)
                    .map((name) => caches.delete(name))
            );
        })
    );
    self.clients.claim();
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', (event) => {
    // Skip cross-origin requests and POST requests
    if (!event.request.url.startsWith(self.location.origin) &&
        !event.request.url.includes('cdnjs.cloudflare.com') &&
        !event.request.url.includes('unpkg.com')) {
        return;
    }

    if (event.request.method !== 'GET') {
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            if (cachedResponse) {
                // Return cached version, but also update cache in background
                event.waitUntil(
                    fetch(event.request).then((response) => {
                        if (response.ok) {
                            caches.open(CACHE_NAME).then((cache) => {
                                cache.put(event.request, response);
                            });
                        }
                    }).catch(() => {})
                );
                return cachedResponse;
            }

            // Not in cache, fetch from network
            return fetch(event.request).then((response) => {
                // Cache successful responses
                if (response.ok) {
                    const responseToCache = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseToCache);
                    });
                }
                return response;
            });
        })
    );
});
