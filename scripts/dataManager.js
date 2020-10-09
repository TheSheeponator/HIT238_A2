$(document).ready(function () {
  // Setup Service Worker
  if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('/HIT238_A2s/sw.js').then(function(registration) {
        // Registration was successful
        console.log('ServiceWorker registration successful with scope: ', registration.scope);
      }, function(err) {
        // registration failed :(
        console.log('ServiceWorker registration failed: ', err);
      });
    
    // Setup Background Sync after the service worker has activated itself.
    navigator.serviceWorker.ready.then(registration => registration.sync.register('sendTimeData'))
    .then(() => {
      console.log("Sync Registered.");
    }).catch(() => {
      console.log("Sync Failed.");
    });
  }

  // OFFLINE VISUAL AID
  // Tells the user the network status through the navbar colour.
  if ('onLine' in navigator) {
    // Browser support verified
    // Start-up check.
    if (navigator.onLine) 
      onLine()
    else
      offLine()
    // Event listeners allow network status to change visually at any time
    // in the apps life.
    window.addEventListener('offline', offLine);
    window.addEventListener('online', onLine);
  }
  // Colour changing functions.
  function onLine() {
    isOnLine = true;
    $('nav .navbar').css('background-color', '#00aa88');
  }
  function offLine() {
    isOnLine = false;
    $('nav .navbar').css('background-color', '#333');
  }

  // Finally open/setup IDBs.
  openStatusDatabase();
  updateStatusData();
});