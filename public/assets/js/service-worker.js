const CACHE_NAME = 'canteen-app-cache-v1';

// Fetch URLs from application.php dynamically


// Specify the path to the Laravel routes file
// const routesFile = '/routes/application.php';
// const routesEndpoint = '/application/canteen-service-worker-routes';

// Service Worker Installation

self.addEventListener('install', (event) => {
    console.log('install event triggered');

    // only GET Routes
    const staticUrlsToCache = [
        '/application/login',
        '/application/logout',
        '/application/home',
        '/application/choose-snack',
        '/application/cart',
        '/application/history',
        '/application/upcoming-meals',
        '/application/contact',
        '/application/account',
        '/application/profile',
        '/application/available-balance',
        '/application/credit-card',
        '/application/offline',
        // '/public/assets/css/application.css',
        // '/public/assets/css/master.css',
        // '/public/assets/css/canteen_custom.css',
        // '/public/assets/css/bootstrap-adds.css',
        // '/public/assets/css/vendors.css',
        // '/public/assets/css/application.css'

        // '/offline',

    ];

    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('install: URLs to cache', staticUrlsToCache);
                return cache.addAll(staticUrlsToCache)
                    .then(() => {
                        console.log('install: All URLs have been added to the cache');
                    })
                    .catch(error => {
                        console.log('install: Cache.addAll failed', error);
                        throw error;
                    });
            })
            .catch(error => {
                console.log('install: Failed to open cache', error);
                // Rethrow the error to trigger the catch block below
                throw error;
            })
    );

    // event.waitUntil(
    //     fetch(routesEndpoint)
    //         .then(response => {
    //             if (!response.ok) {
    //                 throw new Error(`Failed to fetch routes: ${response.statusText}`);
    //             }
    //             return response.json();
    //         })
    //         .then(routes => {
    //             const urlsToCache = routes.map(route => route.uri);
    //
    //             console.log('install: URLs to cache', urlsToCache);
    //
    //             return caches.open(CACHE_NAME)
    //                 .then(cache => {
    //                     // Explicitly handle the cache.addAll Promise
    //                     return cache.addAll(urlsToCache)
    //                         .then(() => {
    //                             console.log('install: All URLs have been added to the cache');
    //                         })
    //                         .catch(error => {
    //                             console.log('install: Cache.addAll failed', error);
    //                             // Rethrow the error to trigger the catch block below
    //                             throw error;
    //                         });
    //                 });
    //         })
    //         .catch(error => {
    //             console.log('Failed to fetch routes:', error);
    //             throw error; // Rethrow the error to trigger the catch block below
    //         })
    // );
});

// Service Worker Activation
self.addEventListener('activate', (event) => {

    console.log('active event triggered');

    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((name) => {
                    if (name !== CACHE_NAME) {
                        return caches.delete(name);
                    }
                })
            );
        })
    );
});

// Fetch event handling
self.addEventListener('fetch', (event) => {

    console.log('Service Worker: Fetch event');

    event.respondWith(
        caches.match(event.request).then((response) => {
            // Cache hit - return the response from the cache
            if (response) {
                console.log('Service Worker: Cache hit');
                return response;
            }

            // If the request is for the routes file, fetch and cache it
            if (event.request.url.endsWith(routesFile)) {
                console.log('Service Worker: Fetching and caching routes file');
                return fetch(event.request).then((response) => {
                    if (!response || response.status !== 200 || response.type !== 'basic') {
                        console.log('Service Worker: Error fetching routes file');
                        return response;
                    }

                    // Clone the response and cache it
                    const clonedResponse = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, clonedResponse);
                        console.log('Service Worker: Routes file cached');
                    });

                    return response;
                });
            }

            // For other requests, try the network and fallback to the offline page
            return fetch(event.request).then((response) => {
                if (!response || response.status !== 200 || response.type !== 'basic') {
                    console.log('Service Worker: Network request failed, using offline page');
                    const offlineRequest = new Request('/application/offline'); // Adjust this based on your offline page route
                    return caches.match(offlineRequest);
                }

                // Clone the response and cache it
                const clonedResponse = response.clone();
                caches.open(CACHE_NAME).then((cache) => {
                    cache.put(event.request, clonedResponse);
                    console.log('Service Worker: Response cached');
                });

                return response;
            });
        })
    );
});

