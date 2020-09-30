<?php
// Check if there is a valid session with this connection.
if (!isset($_SESSION['userId'])) {
    echo json_encode(array("redirect" => "//index"));
    exit();
}
// Check if the session has expired.
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 900)) {
    session_unset();
    session_destroy();
    
    echo json_encode(array("redirect" => "//index?error=sessionexpired"));
    exit();
}   
// Check if request is not ajax.
if(empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    header("Location: /errordocs/err003");
    exit();
}
// Reset session timeout.
$_SESSION['LAST_ACTIVITY'] = time();

if (isset($_POST['adduser-submit'])) { 
    require 'dbh.inc.php';

    session_start();
    $_SESSION['LAST_ACTIVITY'] = time();

    $userName = $_POST['newuid'];
    $email = $_POST['newmail'];
    $pwd = $_POST['newpwd'];
    $PasswordRepeat = $_POST['newpwd-repeat'];

    if (empty($userName) || empty($email) || empty($pwd) || empty($PasswordRepeat)) {
        echo json_encode(array("error" => "emptyFields"));
        exit();
    }
    else if (!preg_match("/^[a-zA-Z0-9]*$/", $userName)) {
        echo json_encode(array("error" => "invUid"));
        exit();
    }
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(array("error" => "invEmail"));
        exit();
    }
    else if ($pwd !== $PasswordRepeat) {
        echo json_encode(array("error" => "pwdNoMatch"));
        exit();
    }
    else {
        
        $sql = "SELECT uidUsers FROM sysUsers WHERE uidUsers=?";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            echo json_encode(array("error" => "internal"));
            exit();
        }
        else {
            mysqli_stmt_bind_param($stmt, "s", $userName);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            $resultCheck = mysqli_stmt_num_rows($stmt);
            if ($resultCheck > 0) {
                echo json_encode(array("error" => "uidTaken"));
                exit();
            }
            else {

                $sql = "INSERT INTO sysUsers (uidUsers, emailUsers, pwdUsers) VALUES (?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    echo json_encode(array("error" => "internal"));
                    exit();
                }
                else {
                    $hasedPwd = password_hash($pwd, PASSWORD_DEFAULT);

                    mysqli_stmt_bind_param($stmt, "sss", $userName, $email, $hasedPwd);
                    mysqli_stmt_execute($stmt);
                    echo json_encode(array("success" => true));
                    exit();
                }
            }
        }
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
  } else {
    echo header("", true, 403);
    exit();
  }