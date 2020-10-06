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

  session_start();

  $headerContent = new Header();

  if (!isset($_SESSION['userId'])) {
    // header("Location: /index");
    $headerContent->sessionValid = false;
    $headerContent->redirect = '/';
  } else if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 900)) {
    session_unset();
    session_destroy();
    // header("Location: /index?error=sessionexpired");

    $headerContent->sessionValid = false;
    $headerContent->redirect = '/index?error=sessionexpired';
  } else {
    $_SESSION['LAST_ACTIVITY'] = time();

    require "./dbh.inc.php";
    $userPerm = 0;

    $sql = "SELECT uidusers FROM sysusers WHERE uidUsers=? AND perms=1";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        // require "./errordocs/err002.php";
        // exit();

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
      $userPage->destination = './users';
      array_push($headerContent->extraNav, $userPage);
    }

    $headerContent->sessionValid = true;
    $headerContent->redirect = '/control';
  }
  echo json_encode($headerContent);