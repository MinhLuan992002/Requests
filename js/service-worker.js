// service-worker.js
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open('my-cache').then(cache => {
            return cache.addAll([
                '/',
                '/css/bootstrap.min.css',
                '/js/bootstrap.bundle.min.js',
                '/path/to/your/images', // Cập nhật theo nhu cầu
                // Thêm các tài nguyên khác cần lưu
            ]);
        })
    );
});

self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request).then(response => {
            return response || fetch(event.request);
        })
    );
});
