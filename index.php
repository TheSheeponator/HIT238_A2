<?php
    session_start();
  
    if (isset($_SESSION['userId']) && isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] < 900)) {
      header("Location: /control.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
      <meta charset="utf-8">
      <meta name="decription" content="">
      <meta name=viewport content="width=device-width, initial-scale=1">
      <link rel="stylesheet" type="text/css" href="./css/loginStyle.css" />
      <script src="./scripts/jquery.js"></script>
      <title>LogIn</title>
  </head>
<body>
    <main>
        <div class="login">
            <form id="loginForm" method="POST">
                <p class="title">LogIn</p>

                <span id="error"><?php if (isset($_GET['error']) && $_GET['error'] == 'sessionexpired') { echo 'Your session has expired.<br>Please re-enter your credentials.'; } ?></span>
                
                <input type="text" id="uid" name="mailuid" placeholder="Username/E-mail..." autocomplete="username">
                <input type="password" id="pwd" name="pwd" placeholder="Password..." autocomplete="current-password">
                <div>
                    <button type="submit" name="loginsubmit">Login</button>
                </div>
            </form>
        </div>
    </main>
</body>
<script>
    $('#loginForm').submit(function(evt) {
        evt.preventDefault(); //Prevent default form submittion

        var posting = $.post('includes/login.inc.php', {
            uid: $('#uid').val(),
            pwd: $('#pwd').val(),
            loginsubmit: true
        }, responseHandler, 'json')
            .fail(function(data) {
                console.log("Error", data);
            });
    });
    function responseHandler(data) {
        if (data.error !== undefined) {
            if (data.error == "emptyfields") {
                $('#error').html("Please enter a<br>Username and Password.");
            } else if (data.error == "internalerror") {
                $('#error').html("Sorry, this service is currently unavailable.<br>We're trying our hardest to fix it. Please try again later.");
            } else if (data.error == "incorrectcredentials") {
                $('#error').html("Please enter valid credentials.");
            } else {
                $('#error').html("Sorry, an error occurred. Try again later.");
            }
        } else if (data.success !== undefined) {
            window.location = data.success;
        }
    };
</script>
</html>