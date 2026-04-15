importScripts('https://www.gstatic.com/firebasejs/10.8.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.8.1/firebase-messaging-compat.js');

firebase.initializeApp({
  apiKey: "AIzaSyCjY3XJoaq7uGe8TdaQFw_c2YLJZSQUqpY",
  authDomain: "buscadoc-b204b.firebaseapp.com",
  projectId: "buscadoc-b204b",
  storageBucket: "buscadoc-b204b.firebasestorage.app",
  messagingSenderId: "754493965978",
  appId: "1:754493965978:web:769a90bb14471891594123"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon: '/logo.png'
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});