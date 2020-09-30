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
// };

require './checkperm.php';
checkPerm_ajax();

$_JSONdata = json_decode(file_get_contents('php://input'), true);
if (isset($_JSONdata['user'])) {
    require "./dbh.inc.php";

    $user = $_JSONdata['user'];

    $sql = "DELETE FROM `sysUsers` WHERE `uidUsers`=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo json_encode(array("error" => "internal"));
    }
    else {
        mysqli_stmt_bind_param($stmt, "s", $user);
        mysqli_stmt_execute($stmt);
        echo json_encode(array("success" => true));
    }
} else {
    echo json_encode(array("error" => "invRequest"));
    exit();
}