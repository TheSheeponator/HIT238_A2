// ===== NAV BAR HAMBURGER =====
// Modeled after Internetkultur's example.
$(document).ready(function () {
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