// Give the service worker access to Firebase Messaging.
// Note that you can only use Firebase Messaging here. Other Firebase libraries
// are not available in the service worker.importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');
/*
Initialize the Firebase app in the service worker by passing in the messagingSenderId.
*/
firebase.initializeApp({
    apiKey: "AIzaSyCnDkYAfzlrYwjXVG9Csx2OYZ8tJzkPXvM",
    authDomain: "alansarcenter-c93d5.firebaseapp.com",
    databaseURL: "https://alansarcenter-c93d5.firebaseio.com",
    projectId: "alansarcenter-c93d5",
    storageBucket: "alansarcenter-c93d5.appspot.com",
    messagingSenderId: "520516617212",
    appId: "1:520516617212:web:2e876da05504afd12ae879",
    measurementId: "G-3ER6SP33B6"
});

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();
messaging.onBackgroundMessage((payload) => {
    // console.log('[firebase-messaging-sw.js] Received background message ', payload);
//     // Customize notification here
//     const notificationTitle = payload.notification.title;
//     const notificationOptions = {
//         body: payload.notification.body,
//         icon: payload.notification.icon,
//         requireInteraction: true,
//         dir:'rtl',
//     };
//
//     self.registration.showNotification(notificationTitle,
//         notificationOptions);
});
