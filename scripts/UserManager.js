
class UserData {
    constructor(name, email, permLevel) {
        this.name = name;
        this.email = email;
        this.permLevel = permLevel;
    }
}

function getUserData() {
    fetch('/includes/getusers', {
        method: 'GET',
        cache: 'no-cache',
    })
        .then(response => response.json())
        .then(data => {
            if (data.userData != undefined) {
                console.log(data.userData);
                var table = document.getElementById('UserTable').getElementsByTagName('tbody')[0];
                data.userData.forEach(user => {
                    var newRow = table.insertRow();
                    var colName = newRow.insertCell();
                    var colEmail = newRow.insertCell();
                    var colPermLevel = newRow.insertCell();
                    var colRemove = newRow.insertCell();

                    colName.innerHTML = user.name;
                    colEmail.innerHTML = user.email;
                    colPermLevel.innerHTML = user.permLevel;
                    colRemove.innerHTML = (user.permLevel == '1' ? '' : `<form class="RemoveUserForm"><input type="hidden" name="user" value="${user.name}" /><input type="submit" value="Remove" name="removeUser-submit"/></form>`);
                });
            } else if (data.invPerm != undefined) {
                window.location = '/errordocs/err403';
            } else {
                console.log('[UM] Got no valid response from server when requested Users: ', data)
            }
        })
        .catch(err => console.error(err));
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
});
