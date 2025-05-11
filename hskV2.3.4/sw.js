self.addEventListener('install', (e) => {
  e.waitUntil(
    caches.open('hsk-cache').then((cache) => {
      return cache.addAll([
        '/HSK/',
        '/HSK/index.php',
        '/HSK/style.css',
        '/HSK/manifest.json',
        '/HSK/icons/icon-192.png',
        '/HSK/icons/icon-512.png',
        '/HSK/icon.png',
        '/HSK/hsk.php',
        '/HSK/mondico.php',
        '/HSK/script.js',
        '/HSK/update_user.php',
        '/HSK/reset_progression.php'

      ]);
    })
  );
});

self.addEventListener('fetch', (e) => {
  e.respondWith(
    caches.match(e.request).then((r) => r || fetch(e.request))
  );
});
