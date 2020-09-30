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
  $userPerm = 0;

  $sql = "SELECT uidusers FROM sysusers WHERE uidUsers=? AND perms=1";
  $stmt = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($stmt, $sql)) {
      require "./errordocs/err002.php";
      exit();
  }
  else {
    $userName = $_SESSION['userUid'];
    mysqli_stmt_bind_param($stmt, "s", $userName);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $resultCheck = mysqli_stmt_num_rows($stmt);
    if ($resultCheck > 0) {
      $userPerm = 1;
    }
  }
  mysqli_stmt_close($stmt);

?>

<!DOCTYPE html>
<html lang="en">
  <head>
      <meta charset="utf-8">
      <meta name=viewport content="width=device-width, initial-scale=1">
      <link rel="stylesheet" type="text/css" href="./css/style.css" />
      <script src="/scripts/jquery.js"></script>
      <script src="/scripts/Nav.js"></script>

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
                <li><a href="/control">Control</a></li>
                <li><a href="">About</a></li>
                <li><a href="#" id="logoutButton">Logout</a>
                <?php 
                  if ($userPerm == 1) echo '<li><a href="./users">User List</a></li>';
                ?>
                </li>
              </ul>
            </div>             
          </div>
        </nav>
      </header>
  </body>
</html>