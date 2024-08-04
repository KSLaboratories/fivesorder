console.log("SW -> OK");

self.addEventListener('install', function (event) {
    event.waitUntil(
        caches.open('sw-cache').then(function (cache) {
            return cache.addAll([
                'pwa/index.html',
                // You can add other files here, such as CSS, JS, images, etc.
            ]).then(function () {
                console.log("SW -> OK3");
            });
        })
    );
});

self.addEventListener('fetch', function (event) {
    console.log("SW -> OK3 - fetch event");
    event.respondWith(
        caches.match(event.request).then(function (response) {
            if (response) {
                return response;
            }

            return fetch(event.request).catch(function () {
                return caches.match('pwa/index.html');
            });
        })
    );
});

self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.filter(function(cacheName) {
                    return cacheName !== 'sw-cache';
                }).map(function(cacheName) {
                    return caches.delete(cacheName);
                })
            );
        })
    );
});
