/**
 * TLOS Scanner Service Worker
 * Provides offline support for the check-in scanner PWA
 *
 * IMPORTANT: This SW is scoped to /admin/tickets/scanner only.
 * It must NOT intercept requests outside its scope.
 */

const CACHE_NAME = 'tlos-scanner-v2';
const SCANNER_PATH = '/admin/tickets/scanner';

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

// Fetch event - ONLY cache scanner-related assets
self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    // Only handle GET requests
    if (event.request.method !== 'GET') {
        return;
    }

    // Only intercept requests that are part of the scanner functionality
    const isScannerPage = url.pathname.startsWith(SCANNER_PATH);
    const isStaticAsset = url.pathname.startsWith('/assets/css/') ||
                          url.pathname.startsWith('/assets/js/');
    const isCDNAsset = url.hostname === 'cdnjs.cloudflare.com' ||
                       url.hostname === 'unpkg.com';

    // Skip everything that's not scanner-related
    if (!isScannerPage && !isStaticAsset && !isCDNAsset) {
        return;
    }

    // For the scanner page itself, use network-first strategy
    if (isScannerPage) {
        event.respondWith(
            fetch(event.request)
                .then((response) => {
                    if (response.ok) {
                        const responseToCache = response.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, responseToCache);
                        });
                    }
                    return response;
                })
                .catch(() => {
                    return caches.match(event.request);
                })
        );
        return;
    }

    // For static assets and CDN resources, use cache-first strategy
    if (isStaticAsset || isCDNAsset) {
        event.respondWith(
            caches.match(event.request).then((cachedResponse) => {
                if (cachedResponse) {
                    return cachedResponse;
                }
                return fetch(event.request).then((response) => {
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
        return;
    }
});
