/*
    Created by Sean Hume - s320298
*/

importScripts('/HIT238_A2/scripts/StatusIDB.js');
// set variables.
var CACHE_TITLE = 'SprinklerSites';
var CACHE_VERSION = 'v0.0.66';
var CACHE_NAME = CACHE_TITLE + '-' + CACHE_VERSION;
var urlsToCache = [

  '/HIT238_A2/css/style.css',
  '/HIT238_A2/control',
  '/HIT238_A2/scripts/control.js',
  '/HIT238_A2/scripts/jquery.js',
  '/HIT238_A2/errordocs/err001',
  '/HIT238_A2/errordocs/err002',
  '/HIT238_A2/errordocs/err003',
  '/HIT238_A2/img/logo1.png',
  '/HIT238_A2/scripts/jquery.imagemapster.min.js',
  '/HIT238_A2/scripts/dataManager.js',
  '/HIT238_A2/scripts/StatusIDB.js',
  '/HIT238_A2/img/House%20Plan.png'
];

var POST_db;
var STATUS_db;
var post_data;


self.addEventListener('install', function(event) {
  // Perform install steps
  // skipps the waiting of it's predecessor.
  self.skipWaiting();
  event.waitUntil(
    // opens cache and loads all URLs into it.
      caches.open(CACHE_NAME)
        .then(function(cache) {
          return cache.addAll(urlsToCache);
        })
  );
  console.log(`[SW] Installed Service Worker with cache version: ${CACHE_VERSION}`);
});

self.addEventListener('activate', (event) => {
  // Cleans up old caches, identified by lesser version numbers.
  // Claims all the open clients to this serviceWorker.
  self.clients.claim();
  event.waitUntil(
    caches.keys()
    .then((keyList) => {
      return Promise.all(keyList.map((cacheName) => {
        if (cacheName !== CACHE_NAME && cacheName.indexOf(CACHE_TITLE) === 0) {
          return caches.delete(cacheName);
        }
      }));
    })
    );
    // Open dataBases.
    openRequestDatabase();
    
    console.log('[SW] Activated Service Worker.')
});

self.addEventListener('fetch', function(event) {
  // Listens to all fetch requests, responding with cached files is available, and gets live versions if not.
  // If the request is a GET, it's most likely for a content.
  if (event.request.clone().method === 'GET') {
      event.respondWith(
        // Try to get request from cache.
        caches.match(event.request.clone())
          .then(function(response) {
            if (response) {
              // Cache hit - return response
              // console.log(`[SW] Using Cached page for: ${event.request.url}`);
              return response;
            } else {
              // No item in the cache matches the request, getting it from the web.
              // console.log(`[SW] Page not found in cache, searching web for: ${event.request.clone().url}`);
              return fetch(event.request.clone(), {
                mode: 'cors',
                credentials: 'include'
              });
            }
          })
      );
  } else if (event.request.clone().method === 'POST') {
    // If request is a POST, then most likely a ajax request.
    if (RegExp('^(.*|\/)(\/comm\/command)\/?').test(event.request.clone().url)) {
      event.respondWith(
        fetch(event.request.clone(), {
          mode: 'cors',
          credentials: 'include'
        })
          .then((response) => {
            return response;
          })
          .catch((response) => {
            var jsonError = {
              "connError": {
                networkerror: true,
                errorpage: null,
                cached: false
              }
            };
            var blob = new Blob([JSON.stringify(jsonError)], { type: "application/json"});
            return new Response(blob, {"status" : 200, "statusText": "CONNECTION_ERROR", headers: {"Content-Type" : "application/json"}});
          })
      );
    } else if (RegExp('^(.*|\/)(\/comm\/changeSettings)\/?').test(event.request.clone().url)) {
      // Connection is from/for Edit Times and is to be stored if no connection is made.
      event.respondWith(
        fetch(event.request.clone(), {
          mode: 'cors',
          credentials: 'include'
        })
        .then((response) => {
          return response;
        })
        .catch((err) => {
          // SAVE REQUEST FOR FUTURE SEND. RETURN CONFERMATION.
          savePostRequests(event.request.clone().url, post_data);

          StatusIDBFuncSet.getData(post_data.loc).then((oldData) => {
            StatusIDBFuncSet.addData(StatusIDBSettings.tables[0].tableName, {
              status: oldData.status,
              title: oldData.title,
              finTime: post_data.finTime,
              staTime: post_data.staTime,
              delay: post_data.delay
            }, post_data.loc).catch((err) => {
              console.error('[SW] ERROR when updating Status IDB: ', err);
            });
          });
          // Save postData to the StatusIDB so the user can see change offline.
          

          var jsonError = {
            "connError": {
              networkerror: true,
              errorpage: null,
              cached: true
            }
          };
          var blob = new Blob([JSON.stringify(jsonError)], { type: "application/json"});
          // console.log(`[SW] TimeData could not be sent to server, likely due to network issues, saving and will send later.`);
          return new Response(blob, {"status" : 202, "statusText": "SAVED_TO_BE_SENT", headers: {"Content-Type" : "application/json"}});
        })
      )
    } else {
      event.respondWith(
        fetch(event.request, {
          mode: 'cors',
          credentials: 'include'
        })
          .then((response) => { return response; })
          .catch((err) => {
            console.error('[SW] Fetch failed and return error:', err);
          })
      )
    }
  }
});
  
  self.addEventListener('message', (event) => {
  if (event.data.hasOwnProperty('post_data')) {
    post_data = event.data.post_data;
  } //else if (event.data.hasOwnProperty('status_data')) {
  //   status_data = event.data.status_data;
  // }
});


function getObjectStore(db, storeName, mode='readwrite') {
  // retrieve Object Store.
  return db.transaction(storeName, mode).objectStore(storeName);
}

// Setup REQUEST IDB local Database
function openRequestDatabase() {
  var indexedDBOpenRequest = indexedDB.open('request_storage');

  indexedDBOpenRequest.onerror = (error) => {
    console.log("[SW] ERROR: An error occurred and the IDB database could not be made.");
  }

  indexedDBOpenRequest.onupgradeneeded = () => {
    // Executes if the database needs to update.
    indexedDBOpenRequest.result.createObjectStore('post_requests', {
      autoIncrement:  true, keyPath: 'id'
    });
  }

  indexedDBOpenRequest.onsuccess = () => {
    POST_db = indexedDBOpenRequest.result;
  }
}


function savePostRequests(url, payload) {
  // get object store and save payload into it.
  var request = getObjectStore(POST_db, 'post_requests', 'readwrite').add({
    url: url,
    payload: payload,
    method: 'POST'
  });

  request.onsuccess = (event) => {
    // console.log('[SW] Command Request saved to IDB');
  }

  request.onerror = (err) => {
    console.log(`[SW] ERROR: Command Request not saved to IDB: ${err}`);
  }
}

self.addEventListener('sync', (event) => {
  console.log("Now online!");
  if (event.tag === 'sendTimeData') {
    event.waitUntil(
      sendPostToServer()
      )
  }
});

function sendPostToServer() {
  // Open the database if the service worker has not yet activated.
  if (POST_db == undefined) {
    return;
  }

  var savedRequests = [];
  var req = getObjectStore(POST_db, 'post_requests').openCursor();

  req.onsuccess = async (event) => {
    var cursor = event.target.result;

    if (cursor) {
      // Keep moving the cursor forward to new saved requests.
      savedRequests.push(cursor.value);
      cursor.continue();
    } else {
      // At this point all post requests have been collected from IDB.
      for (let savedRequest of savedRequests) {
        // Send them to the server.
        console.log('[SW] Saved request sent to server');
        var requestURL = savedRequest.url;
        var payload = JSON.stringify(savedRequest.payload);
        var method = savedRequest.method;
        var headers = {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        };

        fetch(requestURL, {
          headers: headers,
          method: method,
          body: payload,
          mode: 'cors',
          credentials: 'include'
        }).then((response) => {
          if (response.status < 400) {
            // fetch was successful, remove it from the IDB.
            getObjectStore(POST_db, 'post_requests', 'readwrite').delete(savedRequest.id);
          }
        }).catch((err) => {
          //Triggered if the network is still down & will be replayed when the network connects again.
          console.error('[SW] Failed to send saved POST: ', error);
          // throw error so background sync keeps trying.
          throw error;
        })
      }
    }
  }
}