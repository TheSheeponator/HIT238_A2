<?php

function newSession($conn, $userid) {
    $newSessionID = hash('sha256', random_int(50, 9999));
    $newSessionTimeOut = new DateTime();
    $newSessionTimeOut->add(new DateInterval('PT10M'));
    $sessionSQL = "UPDATE sysusers SET sessionID='".$newSessionID."', sessionTimeOut='".$newSessionTimeOut->format('Y-m-d H:i:s')."' WHERE idusers = '".$userid."'";

    $sessionResult = mysqli_query($conn, $sessionSQL);
    if ($sessionResult) {
        return $newSessionID;
    } else {
        return false;
    }
}

function endSession($conn, $apiID) {
    $newSessionID = null;
    $newSessionTimeOut = new DateTime();
    $sessionSQL = "UPDATE sysusers SET sessionID='".$newSessionID."', sessionTimeOut='".$newSessionTimeOut->format('Y-m-d H:i:s')."' WHERE sessionID = '".$apiID."'";

    $sessionResult = mysqli_query($conn, $sessionSQL);
    if ($sessionResult) {
        return $newSessionID;
    } else {
        return false;
    }
}

function updateSessionTime($conn, $apiID) {

    $sql = "UPDATE sysusers SET sessionTimeOut = ? WHERE sessionID = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        $newSessionTimeOut = new DateTime();
        $newSessionTimeOut->add(new DateInterval('PT10M'));
        mysqli_stmt_bind_param($stmt, 'ss', $newSessionTimeOut, $apiID);
        $status = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        if ($status) {
            return true;
        }
    }
}

function checkSession($conn, $apiID) {
    $sql = "SELECT sessionTimeOut FROM sysusers WHERE sessionID = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, 's', $apiID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $resultCheck = mysqli_stmt_num_rows($stmt);
        mysqli_stmt_bind_result($stmt, $sessionTimeOut);
        mysqli_stmt_close($stmt);
        if ($resultCheck > 0) {
            $date = date_create_from_format('Y-m-d H:i:s', $sessionTimeOut);
            $now = new DateTime();
            $diff = date_diff($now, $date, true);
            if (intval(date_format($diff, "i"), 10) < 10 && intval(date_format($diff, "i"), 10) > 0) {
                return true;
            }
        }
    return false;
    }
}

function CheckPerm($conn, $apiID) {
    $sql = "SELECT `idUsers` FROM `sysusers` WHERE `sessionID` = ? AND `Perms` = 1";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, 's', $apiID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $resultCheck = mysqli_stmt_num_rows($stmt);
        mysqli_stmt_close($stmt);
        if ($resultCheck <= 0) {
            return true;
        }
    return false;
    }
}