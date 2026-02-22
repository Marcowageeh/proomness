/*
  Service Worker for Proomnes PWA
  Caches essential assets and serves them offline. When a resource is fetched,
  it will first attempt to serve from the cache and then fall back to the network.
*/

const CACHE_NAME = 'proomnes-cache-v2';
const URLS_TO_CACHE = [
  '/',
  '/index.php',
  '/assets/css/styles.css',
  '/assets/js/main.js',
  '/assets/js/chat.js',
  '/assets/images/logo1.png',
  '/assets/images/logo2.png'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return cache.addAll(URLS_TO_CACHE);
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys => {
      return Promise.all(
        keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key))
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;
  // Skip admin and API requests from caching
  const url = new URL(event.request.url);
  if (url.pathname.startsWith('/admin/') || url.pathname.startsWith('/api/')) return;
  event.respondWith(
    caches.match(event.request).then(response => {
      return response || fetch(event.request).then(networkResponse => {
        // Dynamically cache the new resource
        return caches.open(CACHE_NAME).then(cache => {
          cache.put(event.request, networkResponse.clone());
          return networkResponse;
        });
      });
    })
  );
});

// ── Push Notification Handler ──
// Handles both admin notifications and client broadcast notifications.
// Strategy: Try client broadcast API first (public, no auth), then admin API.
self.addEventListener('push', event => {
  event.waitUntil(
    // 1) Try client broadcast first (public endpoint, always accessible)
    fetch('/api/client-push.php?action=latest')
      .then(r => r.ok ? r.json() : null)
      .then(data => {
        if (data && data.broadcast) {
          const b = data.broadcast;
          const title = '🔔 ' + (b.title || 'Proomnes');
          const body = b.body || 'لديك تحديث جديد';
          const url = b.link || '/';
          // Notify foreground clients
          self.clients.matchAll({ type: 'window' }).then(clients => {
            clients.forEach(client => {
              client.postMessage({ type: 'CLIENT_PUSH', title: b.title, body: body, link: url });
            });
          });
          return self.registration.showNotification(title, {
            body: body,
            icon: '/assets/images/logo1.png',
            badge: '/assets/images/logo1.png',
            vibrate: [300, 100, 300, 100, 500, 200, 300],
            requireInteraction: true,
            tag: 'proomnes-client-push',
            renotify: true,
            data: { url: url }
          });
        }
        // 2) No client broadcast → try admin notifications
        return fetch('/api/notifications.php?action=poll&since=0', { credentials: 'same-origin' })
          .then(r => r.ok ? r.json() : null)
          .then(adminData => {
            const n = adminData && adminData.notifications && adminData.notifications[0];
            const title = n ? ('🚨 ' + n.title) : '🔔 Proomnes';
            const body = n ? (n.body || 'إشعار جديد من الموقع') : 'لديك إشعار جديد — افتح لوحة التحكم';
            const url = n ? (n.link || '/admin/dashboard.php') : '/admin/dashboard.php';
            return self.registration.showNotification(title, {
              body: body,
              icon: '/assets/images/logo1.png',
              badge: '/assets/images/logo1.png',
              vibrate: [300, 100, 300, 100, 500, 200, 300],
              requireInteraction: true,
              tag: 'proomnes-admin-push',
              renotify: true,
              data: { url: url }
            });
          });
      })
      .catch(() => {
        return self.registration.showNotification('🔔 Proomnes', {
          body: 'لديك إشعار جديد',
          icon: '/assets/images/logo1.png',
          badge: '/assets/images/logo1.png',
          vibrate: [300, 100, 300, 100, 500],
          requireInteraction: true,
          tag: 'proomnes-push',
          data: { url: '/' }
        });
      })
  );
});

// ── Notification Click Handler ──
self.addEventListener('notificationclick', event => {
  event.notification.close();
  const targetUrl = event.notification.data?.url || '/';
  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
      // Try to focus an existing window
      for (const client of windowClients) {
        if ('focus' in client) {
          client.navigate(targetUrl);
          return client.focus();
        }
      }
      // Otherwise open new window
      if (clients.openWindow) {
        return clients.openWindow(targetUrl);
      }
    })
  );
});