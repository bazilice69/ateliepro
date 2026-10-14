// Service Worker do AteliêPro (PWA)
// Mantém o app "instalável" e mostra uma página amigável quando sem internet.
// Estratégia "rede primeiro" para não cachear dados dinâmicos desatualizados.

const CACHE = 'ateliepro-v1';
const ESSENCIAIS = [
    '/offline.html',
    '/icons/icon.svg',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE).then((cache) => cache.addAll(ESSENCIAIS)).catch(() => {})
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((chaves) =>
            Promise.all(chaves.filter((c) => c !== CACHE).map((c) => caches.delete(c)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const req = event.request;
    if (req.mode !== 'navigate') {
        return; // só cuida de navegação de páginas
    }
    event.respondWith(
        fetch(req).catch(() => caches.match('/offline.html'))
    );
});
