/*
    Created by Sean Hume - s320298
*/
// ===== Real-Time Controls =====
CurrentSelection = null;
isOnLine = false;


function resetData() {
    // Resets the displayed data on edit. Called by Manual and Auto button events.
    // Calls the same function as the image, to 'refresh' the data.
    updateStatusData();
    mapClick(CurrentSelection, false);
}

function setEditButton(Value) {
    // Sets the edit button up with on/off values as 'Value' (1/0).
    if (Value == 0) {
        $('#autoFormButton').attr('disabled', true);
    } else {
        $('#autoFormButton').removeAttr('disabled');
    }
}

function setAutoButton(Value) {
    // Sets the auto button up with on/off values as 'Value' (1/0).
    if (Value == 0 || !isOnLine) {
        $('#autoFormButton').attr('disabled', true);
    } else {
        $('#autoFormButton').removeAttr('disabled');
    }
}
function setManualButton(value) {
    // sets the manual, disabling it when 'value' = 0.
    onButton = $('#manualON');
    offButton = $('#manualOFF');
    if (value == 0 || !isOnLine) {
        onButton.attr('disabled', true);
        offButton.attr('disabled', true);
    } else if (value == 1) {
        onButton.removeAttr('disabled');
        offButton.attr('disabled', true);
    } else if (value == 2) {
        onButton.attr('disabled', true);
        offButton.removeAttr('disabled');
    } else if (value == 3) {
        onButton.removeAttr('disabled');
        offButton.removeAttr('disabled');
    }
}
// Catch button click to edit the AutoMode of the selected location.
$('#autoFormButton').click(function(evt) {
    evt.preventDefault(); //Prevent default form submittion
    setAutoButton(0);
    setManualButton(0);

    var postdata = {
        
    }
    $.post('https://320298.spinetail.cdu.edu.au/API/comm/command', {
        loc: CurrentSelection,
        auto: true
    }, (response) => {
        if (response.redirect !== undefined && typeof(response.redirect) == "string") {
            window.Location = response.redirect;
        } else if (response.connError !== undefined) {
            $('#errorMsg').html('Could not connect to server. Request not sent.');
        }
        // refresh db
        resetData();
    }, 'json').fail(() => {
        $('#errorMsg').html('Could not connect to server. Request not sent.');
    });
});

// Catch button click to edit the ManualMode ON for the selected location.
$('#manualON').click(function(evt) {
    evt.preventDefault(); //Prevent default form submittion
    setAutoButton(0);
    setManualButton(0);

    $.post("https://320298.spinetail.cdu.edu.au/API/comm/command", {
        loc: CurrentSelection,
        manual: true,
        status: 1 
    }, (response) => {
        if (response.redirect !== undefined && typeof(response.redirect) == "string") {
            window.Location = response.redirect;
        }  else if (response.connError !== undefined) {
            $('#errorMsg').html('Could not connect to server. Request not sent.');
        }
        // refresh db
        resetData();
    },'json').fail(() => {
        $('#errorMsg').html('Could not connect to server. Request not sent.');
    });
});

// Catch button click to edit the ManualMode OFF for the selected location.
$('#manualOFF').click(function(evt) {
    evt.preventDefault(); //Prevent default form submittion
    setAutoButton(0);
    setManualButton(0);
    $.post("https://320298.spinetail.cdu.edu.au/API/comm/command", {
        loc: CurrentSelection,
        manual: true,
        status: 0,
        cache: false
    }, function(response) {
        if (response.redirect !== undefined && typeof(response.redirect) == "string") {
            window.Location = response.redirect;
        } else if (response.connError !== undefined) {
            $('#errorMsg').html('Could not connect to server. Request not sent.');
        }
        // refresh db
        resetData();
    }, 'json').fail(() => {
        $('#errorMsg').html('Could not connect to server. Request not sent.');
    });
});

function timeDiff(start, end, twelveHour = false) {
    // Calculate the time difference between the start and finish times.
    // Split hour and minute.
    start = start.split(":");
    end = end.split(":");

    if (twelveHour) {
        startMeridian = start[1].split(' ')[1];
        endMeridian = end[1].split(' ')[1];
        start[1] = start[1].split(' ')[0];
        end[1] = end[1].split(' ')[0];

        if (startMeridian.toLowerCase() == 'pm' && start[0] != '12') {
            start[0] = Number(start[0]) + 12;
        } 
        if (endMeridian.toLowerCase() == 'pm' && end[0] != '12') {
            end[0] = Number(end[0]) + 12;
        }  
    }
    // convert to Date.
    var startDate = new Date(0, 0, 0, start[0], start[1], 0);
    var endDate = new Date(0, 0, 0, end[0], end[1], 0);
    // Get the difference.
    var diff = endDate.getTime() - startDate.getTime();
    // Convert UTC milliseconds to hours and minutes.
    var hours = Math.floor(diff / 1000 / 60 / 60);
    diff -= hours * 1000 * 60 * 60;
    var minutes = Math.floor(diff / 1000 / 60);
    
    // If hours is less than 0, add 24. This fixes issue with the above calc.
    if (hours < 0)
        hours = hours + 24;
    // Format response.
    return (hours == 0 ? "" : hours + "h ") + (hours != 0 && minutes == 0 ? "" : minutes + " min");
}

function mapClick(loc, resetEditMessage = true) {
    // Called when map location is clicked, passing a two letter string 'loc'.

    // Saved for other functions.
    CurrentSelection = loc;

    // Clear edit message (if any) when resetEditMessage = true.
    if (resetEditMessage)
        $('#editResultMessageContainer').html('');

    if (resetEditMessage && isOnLine) {
        $('#errorMsg').html('');
    } else if (!isOnLine) {
        $('#errorMsg').html('No Network Connection!');
    }

    StatusIDBFuncSet.getData(loc).then((response) => {
        // Check that all required response paramiters are present.
        if (response.errorpage !== undefined && typeof(response.errorpage) == "string") {
            // Loads error and redirect page.
            console.log(response);
            $('main').load(response.errorpage);
        } else if (response.status !== undefined && response.staTime !== undefined && response.finTime !== undefined && response.title !== undefined) {
            // If the status returns successful values, change the table accordingly.
            if (response.status == "0"){
                // Blue (stand-by), ON, OFF
                $('.colourChange').css({backgroundColor: '#29cdff'});
                $('#su').html("Stand-By");
                
                setAutoButton(0);
                setManualButton(3);
            } else if (response.status == "1") {
                // Green (water on), ON, OFF
                $('.colourChange').css({backgroundColor: '#4CAF50'});
                $('#su').html("ON (AUTO)");
                
                setAutoButton(0);
                setManualButton(3);
            } else if (response.status == "2") {
                // Red (water off/manual), ON, AUTO
                $('.colourChange').css({backgroundColor: '#e60000'});
                $('#su').html("OFF (MANUAL)");
                
                setAutoButton(1);
                setManualButton(1);
            } else if (response.status == "3") {
                // Dark-Green (water on/manual), OFF, AUTO
                $('.colourChange').css({backgroundColor: '#006600'});
                $('#su').html("ON (MANUAL)");

                setAutoButton(1);
                setManualButton(2);
            } else if (response.status == "4") {
                // Orange (water off/weather), ON, OFF
                $('.colourChange').css({backgroundColor: '#ff9933'});
                $('#su').html("OFF (WEATHER)");

                setAutoButton(0);
                setManualButton(3);
            } else if (response.status == "5") {
                // grey (error - number not expected)
                //Scripted error from server.
                console.log("[MAP] ERROR: ", response);
                $('.colourChange').css({backgroundColor: "#808080"});
                $('#su').html("---");
                $('#st').html("00:00");
                $('#ft').html("00:00");
                $('#diff').html("0");
                $('#title').html("== ERROR ==");
                setAutoButton(0);
                setManualButton(0);
                return;
            }

            // Set times, title, and duration values.
            $('#st').html(response.staTime);
            $('#ft').html(response.finTime);
            $('#diff').html(timeDiff(response.staTime, response.finTime, true));
            $('#title').html(response.title);

            // Enable the edit button as a location is now selected.
            $('.editWindowButton').removeAttr('disabled');
        }
    }).catch((response) => {
        // On error change colour to grey, and display error message to user.
        console.log("[MAP] ERROR: ", response);
        $('.colourChange').css({backgroundColor: "#808080"});
        $('#su').html("---");
        $('#st').html("00:00");
        $('#ft').html("00:00");
        $('#du').html("0");
        $('#title').html("== ERROR ==");
        setAutoButton(0);
        setManualButton(0);
    });
}

// ===== Edit Pop-up Controls =====
$("input[class='editINP']").focusout(() => {
    // update duration information when the user has finished entering it (onfocusout).
    var format = /^(0[0-9]|1[0-9]|2[0-3]|[0-9]):[0-5][0-9]$/;
    start = $('#startTimeINP')[0];
    end = $('#finishTimeINP')[0];
    duration = $('#duration')[0];
    
    // Check if all fields are entered, if not the user has not yet finished.
    if (start != null && end != null && duration != null) {
        if (end.value !== null && start.value !== null && duration !== null) {
            if (format.exec(start.value) && format.exec(end.value)) {
                // Set the duration value to the input box.
                duration.value = timeDiff(start.value, end.value);
                return;
            }
        }
    }
    duration.value = '';
});

$("input[class='editINP']").keyup((e) => {
    // Check that the user entered a valid keycode, number.
    var chars = ['0','1','2','3','4','5','6','7','8','9'];
    // Finds the index of the entered key.
    var key=chars.indexOf(e.target.value.substr(e.target.value.length-1));
    // Checks if the entered key is invalid, and if so removes it.
    if(key == -1) {
        e.target.value = e.target.value.substr(0,e.target.value.length-1);
    }
    // Automatically inputs a ':' after two characters have been entered to help user input data.
    var value = e.target.value;
    /*if(value.length == 2) {
        e.target.value += ':';
    // Ensures that ':' is entered at correct position, in case it was missed above.
    } else*/ if (value.length > 2 && !value.includes(":") && key != -1) { 
        e.target.value = value.slice(0,2) + ":" + value.slice(2,value.length);
    }
});

$('#editForm').on('submit', (e) => {
    // Takes over from the default submit of the Edit form.

    // Prevents the default submission.
    e.preventDefault();
    
    // initialises values
    startVal = $('#startTimeINP')[0].value;
    finishVal = $('#finishTimeINP')[0].value;
    delayVal = $('#delay')[0].value;
    // Sets message and post data.
    var postdata = {
        loc: CurrentSelection,
        staTime: startVal,
        finTime: finishVal,
        delay: delayVal
    }
    //Preps and sends the data to the service worker.
    var msg = {
        'post_data': {
            loc: CurrentSelection,
            staTime: startVal,
            finTime: finishVal,
            delay: delayVal
        }
    }
    if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {

        PDstart = msg.post_data.staTime.split(':');
        PDfinish = msg.post_data.finTime.split(':');
        
        if (PDstart[0] > 12) {
            PDstart[0] = Number(PDstart[0]) - 12;
            startMeridian = 'pm';
        } else {
            startMeridian = 'am';
        }

        if (PDfinish[0] > 12) {
            PDfinish[0] = Number(PDfinish[0]) - 12;
            finishMeridian = 'pm';
        } else {
            finishMeridian = 'am';
        }
        msg.post_data.staTime = PDstart.join(':') + ' ' + startMeridian;
        msg.post_data.finTime = PDfinish.join(':') + ' ' + finishMeridian;

        navigator.serviceWorker.controller.postMessage(msg);
    } else { console.log("Service Worker Control is not instantiated!"); }

    url = "https://320298.spinetail.cdu.edu.au/API/comm/changeSettings";
    content = JSON.stringify(postdata);

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: content,
        mode: 'cors',
        credentials: 'include'
    })
        .then(response => response.json())
        .then(response => {
            // if successful, check if the response is not null.
            if (response != undefined) {
                // Check response values and act accordingly tos server response.
                if (response.success != undefined) {
                   // clear the data there to stop user from seeing it change. ===================== MIGHT NOT NEED
                    editInputClear();
                    // hide the popup, and display a success message.
                    $('.popupContainer').hide();
                    displayMessage(1, 'Successfully Updated Database!');

                    updateStatusData();
                    // update the displayed information.
                    mapClick(CurrentSelection, false);

                } else if (response.error != undefined && response.error == 'invalid') {
                    // invalid can also have detailed info on error.
                    if (response.error != undefined && response.loc != undefined && response.col != undefined) {
                        $('.popupContainer').hide();
                        displayMessage(0, `Invalid Request, for '${response.col}'. Please try again.`);
                    } else {
                        $('.popupContainer').hide();
                        displayMessage(0, 'Invalid Request, your change was not saved. Please try again.');
                    }
                } else if (response.error != undefined && response.error == 'internal') {
                    $('.popupContainer').hide();
                    displayMessage(0, 'An internal error occurred and your change was not saved. Please try again later.');
                } else if (response.connError != undefined) {
                    $('.popupContainer').hide();
                    displayMessage(2, 'Request could not be sent right now and will be sent later when reconnected to the internet.');
                    resetData();
                } else if (response.redirect != undefined) {
                    $('.popupContainer').hide();
                    displayMessage(2, 'Session token is invalid, Please log in again <a href="//">here</a>.');
                } else {
                    $('.popupContainer').hide();
                    displayMessage(0, 'An unknown error occurred, your change was not saved. Please try again later.');
                }
            }
        })
        .catch(err => {
            $('.popupContainer').hide();
            displayMessage(0, 'An unknown error occurred, your change was not saved. Please try again later.');
        })

    // Ajax request to server for form submission.
    // $.ajax({
    //     type: "POST",
    //     contentType: "application/json; charset=utf-8",
    //     dataType: "json",
    //     url: "https://320298.spinetail.cdu.edu.au/API/comm/changeSettings",
    //     data: JSON.stringify(postdata),
    //     success: (response) => {
    //         // if successful, check if the response is not null.
    //         if (response != undefined) {
    //             // Check response values and act accordingly tos server response.
    //             if (response.success != undefined) {
    //                // clear the data there to stop user from seeing it change. ===================== MIGHT NOT NEED
    //                 editInputClear();
    //                 // hide the popup, and display a success message.
    //                 $('.popupContainer').hide();
    //                 displayMessage(1, 'Successfully Updated Database!');

    //                 updateStatusData();
    //                 // update the displayed information.
    //                 mapClick(CurrentSelection, false);

    //             } else if (response.error != undefined && response.error == 'invalid') {
    //                 // invalid can also have detailed info on error.
    //                 if (response.error != undefined && response.loc != undefined && response.col != undefined) {
    //                     $('.popupContainer').hide();
    //                     displayMessage(0, `Invalid Request, for '${response.col}'. Please try again.`);
    //                 } else {
    //                     $('.popupContainer').hide();
    //                     displayMessage(0, 'Invalid Request, your change was not saved. Please try again.');
    //                 }
    //             } else if (response.error != undefined && response.error == 'internal') {
    //                 $('.popupContainer').hide();
    //                 displayMessage(0, 'An internal error occurred and your change was not saved. Please try again later.');
    //             } else if (response.connError != undefined) {
    //                 $('.popupContainer').hide();
    //                 displayMessage(2, 'Request could not be sent right now and will be sent later when reconnected to the internet.');
    //                 resetData();
    //             } else if (response.redirect != undefined) {
    //                 $('.popupContainer').hide();
    //                 displayMessage(2, 'Session token is invalid, Please log in again <a href="//">here</a>.');
    //             } else {
    //                 $('.popupContainer').hide();
    //                 displayMessage(0, 'An unknown error occurred, your change was not saved. Please try again later.');
    //             }
    //         }
            
    //     },
    //     error: (err) => {
    //         $('.popupContainer').hide();
    //         displayMessage(0, 'An unknown error occurred, your change was not saved. Please try again later.');
    //     }
    // });
});

function displayMessage(colour, message) {
    // Displays a message just below the status table in the 'editResultMessageContainer'.
    switch (colour) {
        case 0:
            bgColour = '#ff5050';
            brColour = '#ff0000';
            break;
        case 1:
            bgColour = '#5aff5a';
            brColour = '#308d30';
            break;
        default:
            bgColour = '#9e9e9e';
            brColour = '#616161';
            break;
    }
    $('#editResultMessageContainer').html(`<div class="editResultMessage" style="background-color: ${bgColour}; border: 2px solid ${brColour}">${message}</div>`);
}

function editInputClear() {
    // Clears the inputs to ensure the user is not given incorrect data.
    $('#startTimeINP')[0].value = '';
    $('#finishTimeINP')[0].value = '';
    $('#duration')[0].value = '';
}

/* Adapted from example at 'https://html-online.com/articles/simple-popup-box/' */
$('.editWindowButton').click(() => {
    // if a location selection has been made show the dialogue box, else disable the button, as it should not be on.
    if (CurrentSelection) {
        $('.popupContainer').show();
        setTimeValues()
    } else {
        $('.editWindowButton').attr('disabled', true);
    }
});
// closes the popup dialogue box.
$('.popupBackground').click(() => {
    $('.popupContainer').hide();
});
$('.popupCloseButton').click(function(){
    $('.popupContainer').hide();
});

// Get the time values from the database and put into the edit dialogues inputs.
// NOTE TO SELF: Could get data from DOM (status table) insted, should be accurate. =============================================================!
function setTimeValues() {
    if (CurrentSelection) {
        
        StatusIDBFuncSet.getData(CurrentSelection).then((result) => {
            if (result != undefined && result.staTime != undefined && result.finTime != undefined) {
                start = result.staTime.split(':');
                finish = result.finTime.split(':');

                start[2] = start[1].split(' ')[1];
                finish[2] = finish[1].split(' ')[1];

                start[1] = start[1].split(' ')[0];
                finish[1] = finish[1].split(' ')[0];

                if (start[2] == 'pm' && start[0] != '12') {
                    start[0] = parseInt(start[0], 10) + 12;
                }
                if (finish[2] == 'pm' && finish[0] != '12') {
                    finish[0] = parseInt(finish[0], 10) + 12;
                }
                var startTime = start[0] + ':' + start[1];
                var finishTime = finish[0] + ':' + finish[1];
                $('#startTimeINP')[0].value = startTime;
                $('#finishTimeINP')[0].value = finishTime;

                $('#duration')[0].value = timeDiff(startTime, finishTime);
            }
        });
    } else {
        $('.editWindowButton').attr('disabled', true);
        $('.popupContainer').hide();
    }
}
