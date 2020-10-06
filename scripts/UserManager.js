
class UserData {
    constructor(name, email, permLevel) {
        this.name = name;
        this.email = email;
        this.permLevel = permLevel;
    }
}

function getUserData() {
    var table = document.getElementById('UserTable').getElementsByTagName('tbody')[0];
    table.innerHTML = '';
    fetch('/includes/getusers', {
        method: 'GET',
        cache: 'no-cache',
        mode: 'cors',
        credentials: 'include'
    })
        .then(response => response.json())
        .then(data => {
            if (data.error != undefined && (data.error === 'invCredentials' || data.error === 'internal')) {
                window.location = '/errordocs/err403';
            }
            else if (data.userData != undefined) {
                data.userData.forEach(user => {
                    var newRow = table.insertRow();
                    var colName = newRow.insertCell();
                    var colEmail = newRow.insertCell();
                    var colPermLevel = newRow.insertCell();
                    var colRemove = newRow.insertCell();

                    colName.innerHTML = user.name;
                    colEmail.innerHTML = user.email;
                    colPermLevel.innerHTML = user.permLevel;
                    colRemove.innerHTML = (user.permLevel == '1' ? '' : `<button onclick="RemoveUser('${user.name}')">Remove</button>`);
                });
            } else {
                console.log('[UM] Got no valid response from server when requested Users got: ', data)
            }
        })
        .catch(err => console.error(err));
}

function RemoveUser(userName) {
    fetch('./includes/removeUsers.inc', {
        method: 'POST',
        cache: 'no-cache',
        mode: 'cors',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ user: userName })
    })
        .then(response => response.json())
        .then(data => {
            if (data != undefined) {
                if (data.error != undefined) {
                    console.error('[UM] Error when removing user: ', data.error);
                }
                else if (data.success != undefined) {
                    $('.popupContainer').hide();
                    getUserData();
                }
            }
        })
        .catch(err => console.log('[UM] Failed to remove user:', err));
}


$(document).ready(function () {
    $('.addUserWindowButton').click(() => {
        $('.popupContainer').show();
    });
    // closes the popup dialogue box.
    $('.popupBackground').click(() => {
        $('.popupContainer').hide();
    });

    $('.popupCloseButton').click(function(){
        $('.popupContainer').hide();
    });

    getUserData();

    $('#addUserForm').on('submit', e => {
        e.preventDefault();

        form = document.getElementById('addUserForm');

        fetch('/includes/addUser.inc', {
            method: 'POST',
            cache: 'no-cache',
            mode: 'cors',
            credentials: 'include',
            headers: {
                'Content-Type': 'json',

            },
            body: JSON.stringify({
                adduser: true,
                newuid: form[0].value,
                newmail: form[1].value,
                newpwd: form[2].value,
                'newpwd-repeat': form[3].value
            })
        })
        .then(response => response.json())
        .then((data) => {
            console.log(data);
            if (data != undefined) {
                if (data.error != undefined) {
                    var errorMsg = $('#addUserFormErrorMsg');
                    errorMsg.html('');
                    msg = '<td colspan="2" class="AUerror">';
                    if (data.error == 'emptyFields') {
                        msg += 'Please enter all fields.';
                    } else if (data.error == 'invUid') {
                        msg += 'Please enter a valid username.';
                    } else if (data.error == 'invEmail') {
                        msg += 'Please enter a valid email.';
                    } else if (data.error == 'pwdNoMatch') {
                        msg += 'Passwords do not match.';
                    } else if (data.error == 'uidTaken') {
                        msg += 'Username is taken, please try another.';
                    } else if (data.error == 'internal') {
                        msg += 'An internal error has occurred. Please try again later.';
                    } else if (data.error == 'invalidRequest') {
                        msg += 'The request is invalid.';
                    }
                    msg += '</td>';
                    errorMsg.html(msg);
                }
                if (data.success != undefined) {
                    $('.popupContainer').hide();
                    getUserData();
                }
            }
        })
        .catch((err) => console.log(err));
    })
});
