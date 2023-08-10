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
    appId: "1:520516617212:web:bf9be407e05628022ae879",
    measurementId: "G-84BF7VJGPK"
});

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    // fcp_options.link field from the FCM backend service goes there, but as the host differ, it not handled by Firebase JS Client sdk, so custom handling
    if (event.notification && event.notification.data && event.notification.data.FCM_MSG && event.notification.data.FCM_MSG.notification) {
        const url = event.notification.data.FCM_MSG.notification.click_action;
        event.waitUntil(
            self.clients.matchAll({type: 'window'}).then(windowClients => {
                // Check if there is already a window/tab open with the target URL
                for (let i = 0; i < windowClients.length; i++) {
                    const client = windowClients[i];
                    // If so, just focus it.
                    if (client.url === url && 'focus' in client) {
                        return client.focus();
                    }
                }
                // If not, then open the target URL in a new window/tab.
                if (self.clients.openWindow) {
                    console.log("open window")
                    return self.clients.openWindow(url);
                }
            })
        )
    }
}, false);


self.onnotificationclick = function (event) {
    if (event.notification && event.notification.data && event.notification.data.FCM_MSG && event.notification.data.FCM_MSG.notification) {
        const url = event.notification.data.FCM_MSG.notification.click_action;

        console.log('On notification click: ', event.notification.tag);
        event.notification.close();

        // This looks to see if the current is already open and
        // focuses if it is
        event.waitUntil(clients.matchAll({includeUncontrolled: true, type: 'window'}).then(function (clientList) {
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url === url && 'focus' in client)
                    return client.focus();
            }
            if (clients.openWindow)
                return clients.openWindow(url);
        }));
    }
};

const messaging = firebase.messaging();
messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    // Customize notification here
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: payload.notification.icon,
        data: {
            time: new Date(Date.now()).toString(),
            click_action: payload.notification.click_action,
        },
        requireInteraction: true,
        dir: 'rtl',
    };


    // self.registration.showNotification(notificationTitle,
    //     notificationOptions);
});
