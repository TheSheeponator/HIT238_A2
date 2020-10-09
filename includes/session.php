<?php

function newSession($conn, $userid) {
    $newSessionID = hash('sha256', random_int(50, 9999));
    $newSessionTimeOut = new DateTime();
    $newSessionTimeOut->add(new DateInterval('P10M'));
    $sessionSQL = "UPDATE sysusers SET sessionID='".$newSessionID."', sessionTimeOut='".$newSessionTimeOut->format('Y-m-d H:i:s')."' WHERE idusers = '1'";

    $sessionResult = mysqli_query($conn, $sessionSQL);
    if ($sessionResult) {
        return $newSessionID;
    } else {
        return false;
    }
}

function checkSession($conn, $apiID) {
    $sql = "SELECT sessionTimeOut FROM sysusers WHERE sessionID = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, 's', $apiID);
        $status = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        if ($status) {
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_fetch_row($result) > 0) {

                $sql = "UPDATE sysusers SET sessionTimeOut = ? WHERE sessionID = ?";
                $stmt = mysqli_stmt_init($conn);
                if (mysqli_stmt_prepare($stmt, $sql)) {
                    $newSessionTimeOut = new DateTime();
                    $newSessionTimeOut->add(new DateInterval('P10M'));
                    mysqli_stmt_bind_param($stmt, 'ss', $newSessionTimeOut, $apiID);
                    $status = mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    if ($status) {
                        return true;
                    }
                }
            }
        }
    return false;
    }
}