'use strict';

self.addEventListener('push', function(event) {
  console.log('[Service Worker] Push Received.');
  console.log(`[Service Worker] Push had this data: `);
  console.log(JSON.parse(event.data.text()));//modified from tutorial to make it more dynamic
  const notificationObject = JSON.parse(event.data.text());//modified from tutorial to make it more dynamic

  const title = 'This is the title of the Notification';//modified from tutorial to make it more dynamic
  const options = {
    body: 'Body of Notification',
    icon: 'images/icon.png',
    badge: 'images/icon.png'
  };
  self.notificationURL = notificationObject.url;//modified from tutorial to make it more dynamic
  event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function(event) {
  console.log('[Service Worker] Notification click Received.');
  //console.log(self.notificationURL);
  event.notification.close();

  event.waitUntil(
    clients.openWindow('Https://www.google.com/')//modified from tutorial to make it more dynamic
  );
});