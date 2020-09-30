<?php

function checkPerm(){
    require $_SERVER['DOCUMENT_ROOT'].'/includes/dbh.inc.php';

    $sql = "SELECT uidUsers FROM sysUsers WHERE uidUsers=? AND perms=1";
    $stmt = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        require $_SERVER['DOCUMENT_ROOT'].'/errordocs/err001';
        mysqli_close($conn);
        exit();
    }
    else {
        $userName = $_SESSION['userUid'];
        mysqli_stmt_bind_param($stmt, "s", $userName);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $resultCheck = mysqli_stmt_num_rows($stmt);
        if ($resultCheck <= 0) {
            require $_SERVER['DOCUMENT_ROOT'].'/errordocs/err403.php';
            exit();
        }
        mysqli_close($conn);
    }
}

function checkPerm_ajax(){
    require $_SERVER['DOCUMENT_ROOT'].'/includes/dbh.inc.php';

    $sql = "SELECT uidUsers FROM sysUsers WHERE uidUsers=? AND perms=1";
    $stmt = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo json_encode(array("error" => "internal"));
        mysqli_close($conn);
        exit();
    }
    else {
        $userName = $_SESSION['userUid'];
        mysqli_stmt_bind_param($stmt, "s", $userName);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $resultCheck = mysqli_stmt_num_rows($stmt);
        if ($resultCheck <= 0) {
            echo json_encode(array("error" => "invCredentials"));
            exit();
        }
        mysqli_close($conn);
    }
}
