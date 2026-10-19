const CACHE = 'canchas-el-virrey-v1';
const ASSETS = ['/pwa/index.html', '/pwa/css/app.css', '/pwa/js/app.js', '/pwa/manifest.json'];

self.addEventListener('install', (event) => {
  event.waitUntil(caches.open(CACHE).then((cache) => cache.addAll(ASSETS)));
});

self.addEventListener('fetch', (event) => {
  // Solo cachea assets propios de la PWA; las llamadas a /api siempre van a la red.
  if (event.request.url.includes('/api/')) return;
  event.respondWith(
    caches.match(event.request).then((cached) => cached || fetch(event.request))
  );
});
