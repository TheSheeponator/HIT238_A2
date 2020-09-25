<?php
  session_start();
  require "./Redirect.php";

  if (!isset($_SESSION['userId']) && basename($_SERVER['SCRIPT_FILENAME'], ".php") != "index") {
    header("Location: /index");
  }

  if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 900)) {
    session_unset();
    session_destroy();
    header("Location: /index?error=sessionexpired");
  }
  $_SESSION['LAST_ACTIVITY'] = time();

  require "./includes/dbh.inc.php";

?>

<!DOCTYPE html>
<html lang="en">
  <head>
      <meta charset="utf-8">
      <meta name=viewport content="width=device-width, initial-scale=1">
      <link rel="stylesheet" type="text/css" href="./css/style.css" />

      <link rel="manifest" href="manifest.json">
      <meta name="theme-color" content="#009578">
      <link rel="apple-touch-icon" href="img/touch-img.png">

      <title>Sprinkler System Control</title>
  </head>
  <body>
      <header>
        <nav>
          <img src="img/logo1.png" alt="logo" class="logo">
          <div class="navbar">
            <div class="menu-btn" id="menu-btn">
              <div></div>
              <span></span>
              <span></span>
              <span></span>
            </div>
            <div class="responsive-menu">
              <ul>
                <li><a href="">Control</a></li>
                <li><a href="">About</a></li>
                <li><a href="#" id="logoutButton">Logout</a>
                </li>
              </ul>
            </div>             
          </div>
        </nav>
      </header>
  </body>
</html>