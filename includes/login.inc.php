<?php
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
        exit(); //Used to ensure the stop of code execution
    }
    else {
       $sql = "SELECT * FROM sysusers WHERE uidUsers=? OR emailUsers=?;";
       $stmt = mysqli_stmt_init($conn);
       if (!mysqli_stmt_prepare($stmt, $sql)) {
         $return_arr_json = array("error" => "internalerror");
         echo json_encode($return_arr_json);
         exit(); //Used to ensure the stop of code execution
       } else {

         mysqli_stmt_bind_param($stmt, "ss", $mailuid, $mailuid);
         mysqli_stmt_execute($stmt);
         $result = mysqli_stmt_get_result($stmt);
         if ($row = mysqli_fetch_assoc($result)) {
           $pwdCheck = password_verify($password, $row['pwdUsers']);
           if ($pwdCheck == false)
           {
            $return_arr_json = array("error" => "incorrectcredentials");
            echo json_encode($return_arr_json);
            exit(); //Used to ensure the stop of code execution
           }
           else if ($pwdCheck == true)
           {
              session_start();
              session_regenerate_id(true);
              $_SESSION['userId'] = $row['idUsers'];
              $_SESSION['userUid'] = $row['uidUsers'];

              $_SESSION['LAST_ACTIVITY'] = time();
              
              $return_arr_json = array("success" => "./control");
              echo json_encode($return_arr_json);
              exit(); //Used to ensure the stop of code execution
              //}
           }
           else {
              $return_arr_json = array("error" => "incorrectcredentials");
              echo json_encode($return_arr_json);
              exit(); //Used to ensure the stop of code execution
           }
         }
         else {
            $return_arr_json = array("error" => "incorrectcredentials");
            echo json_encode($return_arr_json);
            exit(); //Used to ensure the stop of code execution
         }
       }
    }
 }
 else {
    $return_arr_json = array("error" => "emptyfields");
    echo json_encode($return_arr_json);
 }