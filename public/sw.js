const CACHE = 'dudukan-v2';
const HALAMAN_OFFLINE = '/offline.html';

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(CACHE).then((cache) => cache.add(HALAMAN_OFFLINE)).then(() => self.skipWaiting()));
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches
            .keys()
            .then((kunci) => Promise.all(kunci.filter((k) => k !== CACHE).map((k) => caches.delete(k))))
            .then(() => self.clients.claim()),
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    if (request.method !== 'GET' || url.origin !== self.location.origin) {
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith(fetch(request).catch(() => caches.match(HALAMAN_OFFLINE)));
        return;
    }

    // Berkas di /build/ berganti nama setiap rilis, jadi aman disimpan. Data API tidak pernah disimpan.
    if (url.pathname.startsWith('/build/')) {
        event.respondWith(
            caches.match(request).then(
                (tersimpan) =>
                    tersimpan ??
                    fetch(request).then((respons) => {
                        if (respons.ok) {
                            const salinan = respons.clone();
                            caches.open(CACHE).then((cache) => cache.put(request, salinan));
                        }
                        return respons;
                    }),
            ),
        );
    }
});
