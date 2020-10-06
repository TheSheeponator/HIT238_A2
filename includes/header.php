<?php

class Header {
  public $sessionValid = false;
  public $redirect = '';
  public $disError = '';
  public $extraNav = [];
}

class NavButton {
  public $text;
  public $destination;
}

// CORS
/*
  Some code from: https://stackoverflow.com/questions/8719276/cross-origin-request-headerscors-with-php-headers
*/
  header('Access-Control-Allow-Origin: https://thesheeponator.github.io');
  header('Access-Control-Allow-Credentials: true');
  header('Access-Control-Max-Age: 86400');    // cache for 1 day
  if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
    // may also be using PUT, PATCH, HEAD etc
      header('Access-Control-Allow-Methods: GET, POST, OPTIONS');         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
      header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    exit();
  }
  ini_set('session.cookie_samesite', 'None');
  session_start();

  $headerContent = new Header();

  if (!isset($_SESSION['userId'])) {
    $headerContent->sessionValid = false;
    $headerContent->redirect = '/HIT238_A2/';
  } else if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 900)) {
    session_unset();
    session_destroy();

    $headerContent->sessionValid = false;
    $headerContent->redirect = '/HIT238_A2/index?error=sessionexpired';
  } else {
    $_SESSION['LAST_ACTIVITY'] = time();

    require './dbh.inc.php';
    $userPerm = 0;

    $sql = "SELECT uidusers FROM sysusers WHERE uidUsers=? AND perms=1";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        $headerContent->sessionValid = true;
        $headerContent->disError = 'err002';
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

    if ($userPerm === 1) {
      $userPage = new NavButton();
      $userPage->text = 'User List';
      $userPage->destination = '/HIT238_A2/users';
      array_push($headerContent->extraNav, $userPage);
    }

    $headerContent->sessionValid = true;
    $headerContent->redirect = '/HIT238_A2/control';
  }
  echo json_encode($headerContent);