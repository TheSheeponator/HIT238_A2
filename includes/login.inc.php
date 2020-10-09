<?php
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

// session_start(['cookie_samesite' => 'Secure']);
 // Base code by (Insert name here), modified by Sean Hume, further modified for ajax use.
 if (isset($_POST['loginsubmit'])) {

  require 'dbh.inc.php';
  // require '../Redirect.php';

  $mailuid = $_POST['uid'];
  $password = $_POST['pwd'];

  $return_arr_json = array();

  if (empty($mailuid) || empty($password)) {
    $return_arr_json = array("error" => "emptyfields");
    echo json_encode($return_arr_json);
    // header("Location: https://thesheeponator.github.io/HIT238_A2?error=emptyfields&uid=".$mailuid);
    exit(); //Used to ensure the stop of code execution
  }
  else {
    $sql = "SELECT * FROM sysusers WHERE uidUsers=? OR emailUsers=?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
       $return_arr_json = array("error" => "internalerror");
       echo json_encode($return_arr_json);
      // header("Location: https://thesheeponator.github.io/HIT238_A2?error=internalerror");
      exit(); //Used to ensure the stop of code execution
    } else {
      mysqli_stmt_bind_param($stmt, "ss", $mailuid, $mailuid);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      if ($row = mysqli_fetch_assoc($result)) {
        $pwdCheck = password_verify($password, $row['pwdUsers']);
        if ($pwdCheck == false) {
          $return_arr_json = array("error" => "incorrectcredentials");
          echo json_encode($return_arr_json);
          // header("Location: https://thesheeponator.github.io/HIT238_A2?error=wrongPU&uid=".$mailuid);
          exit(); //Used to ensure the stop of code execution
        }
        else if ($pwdCheck == true)
        {
          // Setup Session
          // session_start();
          // session_regenerate_id(true);
          // $_SESSION['userId'] = $row['idUsers'];
          // $_SESSION['userUid'] = $row['uidUsers'];

          // $_SESSION['LAST_ACTIVITY'] = time();
          require './session.php';
          $newSessionID = newSession($conn, $row['idUsers']);
          if ($newSessionID != false) {
            echo json_encode(array("success" => "./control", "apiID" => $newSessionID));
            // header("Location: https://thesheeponator.github.io/HIT238_A2/control");
            exit(); //Used to ensure the stop of code execution
          } else {
             $return_arr_json = array("error" => "internalerror");
             echo json_encode($return_arr_json);
            // header("Location: https://thesheeponator.github.io/HIT238_A2?error=internalerror");
          }
        }
        else {
          $return_arr_json = array("error" => "incorrectcredentials");
          echo json_encode($return_arr_json);
          // header("Location: https://thesheeponator.github.io/HIT238_A2?error=wrongPU&uid=".$mailuid);
          exit(); //Used to ensure the stop of code execution
        }
      }
      else {
        $return_arr_json = array("error" => "incorrectcredentials");
        echo json_encode($return_arr_json);
        // header("Location: https://thesheeponator.github.io/HIT238_A2?error=wrongPU&uid=".$mailuid);
        exit(); //Used to ensure the stop of code execution
      }
    }
  }
 }
 else {
    $return_arr_json = array("error" => "emptyfields");
    echo json_encode($return_arr_json);
    // header("Location: https://thesheeponator.github.io/HIT238_A2?error=emptyfields&uid=".$mailuid);
 }