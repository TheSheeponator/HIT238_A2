$(document).ready(function () {
    
    fetch('./includes/header', {
        method: 'GET',
        credentials: 'include',
        cache: 'no-cache'
    })
    .then(response => response.json())
    .then(data => {
        if (data != undefined) {
            if (data.sessionValid != undefined && data.redirect != undefined && data.disError != undefined && data.extraNav != undefined) {
                console.log(data);
                
                if (data.sessionValid != true) {
                    window.location = data.redirect;
                } else if (data.disError === 'err002') {
                    document.getElementsByTagName('main').innerHTML = fetch('./errdocs/err002');
                    return;
                }
                
                if (data.extraNav.length > 0) {
                    var navUL = document.getElementsByClassName('LinkUL')[0];
                    data.extraNav.forEach(element => {
                        newLi = document.createElement('li');
                        a = document.createElement('a');
                        a.onclick = () => {
                            window.location = element.destination;
                        }
                        a.appendChild(document.createTextNode(element.text));
                        newLi.appendChild(a);
                        navUL.appendChild(newLi);
                    });
                }
            } else {
                console.warn('[Nav] Incorrect format received, did not expect: ', data);
            }
        }
    }).catch(err => {
        console.log('[Nav] Could not get session data! Might be offline: ', err);
        console.warn('[Nav] Some Features may be disbaled due to connection issues.');
    })
    
    
    // ===== NAV BAR HAMBURGER =====
    // Modeled after Internetkultur's example.

    $('.menubtn').click(function() {
        $('.responsive-menu').addClass('expand');
        $('.menu-btn').addClass('btn-none');
    });
    $('.close-btn').click(function(){
        $('.responsive-menu').removeClass('expand');
        $('.menu-btn').removeClass('btn-none');
    });
    $('.menu-btn').click(function(){
        $('.responsive-menu').toggleClass('expand');
    });
    
    $('#logoutButton').click(function(evt){
        // redirect to logout script.
        window.location = "/includes/logout.inc";
    })
});