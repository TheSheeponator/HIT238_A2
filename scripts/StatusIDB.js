/*
  Manages all functions associated with the Status IDB.

  The following IDB interactions are based of a stackOverflow comment by 'kyunghwanjung',
  https://stackoverflow.com/questions/31703419/how-to-import-json-file-into-indexeddb.
*/
var StatusIDBSettings = {
  name: "status_storage",
  version: 1,
  tables: [
    {
      tableName: "sprinkler_status",
      keypath: "seq",
      autoIncrement: true,
      index: ["id", "status", "staTime", "finTime", "title"],
      unique: [false, false, false, false, false, false]
    }
  ]
};
var StatusIDBFuncSet = {
  //write
  addData: function(table, data, key = undefined) {
    return new Promise((resolve, reject) => {
      // Opens the IDB, when successful calls the onsuccess.
      var req = indexedDB.open(StatusIDBSettings.name, StatusIDBSettings.version);
  
      // If opening is successful, add the data to the IDB.
      req.onsuccess = event => {
        // try to add it, catching a failure.
        try {
          var db = req.result;
          var transaction = db.transaction(table, "readwrite");
          var objectStore = transaction.objectStore(table);
          var objectStoreRequest = objectStore.put(data, key);
        } catch (e) {
          console.log("[StatusIDB] addDataFunction error: table or data null ");
          console.log(e);
        }
        return resolve();
      };
      // Failure call used for debugging.
      req.onerror = event => {
        console.log("[StatusIDB] addData indexed DB open fail");
        return reject();
      };
    })
  }
};
StatusIDBFuncSet.getData = (key, table = StatusIDBSettings.tables[0].tableName) => {
  return new Promise(resolve => {
    try {
      // open the IDB.
      var req = indexedDB.open(
        StatusIDBSettings.name,
        StatusIDBSettings.version
      );

      // Only when IDB successfully opens get the data.
      req.onsuccess = event => {
        var db = req.result;
        var transaction = db.transaction(table, "readonly");
        var objectStore = transaction.objectStore(table);

        var result = objectStore.get(key);
        result.onsuccess = data => {
          return resolve(data.target.result);
        };
        // return result.target.result;
      };
    } catch (e) {
      console.error(e);
    }
  });
};

function updateStatusData() {
  return new Promise(resolve => {
    var requestURL = "https://320298.spinetail.cdu.edu.au/API/comm/getStatus";
    var method = "POST";
    var headers = {
      Accept: "application/json",
      "Content-Type": "application/json"
    };
    fetch(requestURL, {
      headers: headers,
      method: method
    })
      .then(response => {
        if (parseInt(response.clone().status) < 400) {
          // fetch was successful, store it in the IDB.
          response.clone().json()
            .then(result => {
              for (var i in result.zoneStatus) {
                StatusIDBFuncSet.addData(
                  StatusIDBSettings.tables[0].tableName,
                  result.zoneStatus[i].data,
                  result.zoneStatus[i].id

                  // Change Colour of map area
                  );
                  switch (result.zoneStatus[i].data.status) {
                    case '0':
                      colour = '29cdff';
                      break;
                    case '1':
                      colour = '4CAF50';
                      break;
                    case '2':
                      colour = 'e60000';
                      break;
                    case '3':
                      colour = '006600';
                      break;
                    case '4':
                      colour = 'ff9933';
                      break;
                    default:
                      colour = '808080';
                      break;
                  }
                  $('#controlImg').mapster('set',true,'#FL'/*+data.zoneStatus[i].id*/, {stroke: false, fillOpacity : 0.5, fillColor: colour} );
              }
              if (result.zoneStatus.length > 0) {
                var date = new Date();
                var days = [
                  "Sunday",
                  "Monday",
                  "Tuesday",
                  "Wednesday",
                  "Thursday",
                  "Friday",
                  "Saturday"
                ];
                StatusIDBFuncSet.addData(
                  StatusIDBSettings.tables[0].tableName,
                  { dateTime: date },
                  "localTime"
                );
                $("#updateTime").html(
                  `Updated: ${date.getHours()}:${date.getMinutes()}, ${days[date.getDay()]} ${date.getDate()}`
                );
                return resolve;
              }
            })
            .catch((err) => {
              console.error(`[StatusIDB] ERROR when updating status: ${err}`);
              console.log(response.clone().json());

              StatusIDBFuncSet.getData("localTime").then(result => {
                var date = result.dateTime;
                var days = [
                  "Sunday",
                  "Monday",
                  "Tuesday",
                  "Wednesday",
                  "Thursday",
                  "Friday",
                  "Saturday"
                ];
                $("#updateTime").html(
                  `Updated: ${date.getHours()}:${date.getMinutes()}, ${days[date.getDay()]} ${date.getDate()}`
                );
                return resolve;
              });
            })
            .then(() => {
              if (CurrentSelection) mapClick(CurrentSelection, false);
            });
        } else {
          console.error(
            "[StatusIDB] Server returned error for /comm/getStaus:",
            response.clone().status
          );
        }
      })
      .catch(err => {
        console.log("[StatusIDB] Failed to get new status from server");
      });
  });
}

function openStatusDatabase() {
  var indexedDBOpenRequest = indexedDB.open("status_storage");

  indexedDBOpenRequest.onerror = error => {
    console.log(
      "[StatusIDB] ERROR: An error occurred and the IDB database could not be made."
    );
  };

  indexedDBOpenRequest.onupgradeneeded = event => {
    // Executes if the database needs to update.
    var db = event.target.result;

    for (var i in StatusIDBSettings.tables) {
      var OS = db.createObjectStore(StatusIDBSettings.tables[i].tableName, {
        keyPath: StatusIDBSettings.tables[i].keyPath,
        autoIncrement: StatusIDBSettings.tables[i].autoIncrement
      });
    }
  };

  indexedDBOpenRequest.onsuccess = () => {
    STATUS_db = indexedDBOpenRequest.result;
  };
}

