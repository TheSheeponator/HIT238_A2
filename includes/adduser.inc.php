<?php
session_start();
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
// if($_SERVER['REQUEST_METHOD'] == 'GET') {
//     header("Location: /errordocs/err003");
//     exit();
// }

require './checkperm.php';
checkPerm_ajax();

// Reset session timeout.
$_SESSION['LAST_ACTIVITY'] = time();

$_JSONdata = json_decode(file_get_contents('php://input'), true);
if (isset($_JSONdata['adduser'])) { 
    require 'dbh.inc.php';

    $userName = $_JSONdata['newuid'];
    $email = $_JSONdata['newmail'];
    $pwd = $_JSONdata['newpwd'];
    $PasswordRepeat = $_JSONdata['newpwd-repeat'];

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
                    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

                    mysqli_stmt_bind_param($stmt, "sss", $userName, $email, $hashedPwd);
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
echo json_encode(array("error" => "invalidRequest"));
exit();
}