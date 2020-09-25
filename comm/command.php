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
    if(empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header("Location: /errordocs/err003");
        exit();
    }
    // Reset session timeout.
    $_SESSION['LAST_ACTIVITY'] = time();
    

if (isset($_POST['loc'])){
    if (isset($_POST['auto'])) {
        require '../includes/sdbh.inc.php';
        

        $sql = 'UPDATE times SET status=? WHERE id=?';
        $stmt = mysqli_stmt_init($sconn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_close($sconn);
            echo json_encode(array('errorpage' => '//errordocs/err002'));
            mysqli_close($sconn);
            exit();
        }
        else {
            $location = $_POST['loc'];
            $newStatus = 0;
            mysqli_stmt_bind_param($stmt, "is", $newStatus, $location);
            $status = mysqli_stmt_execute($stmt);
            
            if ($status === false) {
                //Error, Execute did not complete.
                echo json_encode(array('errorpage' => '//errordocs/err002'));
                mysqli_close($sconn);
                exit();
            } else {
                echo json_encode(array('success' => true));
                mysqli_close($sconn);
                exit();
            }
        }

    } else if (isset($_POST['manual']) && isset($_POST['status']) && ctype_digit($_POST['status']) && ( (int)$_POST['status'] == 0 || (int)$_POST['status'] == 1 )) {
        
        require '../includes/sdbh.inc.php';

        $sql = 'UPDATE times SET status=? WHERE id=?';
        $stmt = mysqli_stmt_init($sconn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_close($sconn);
            echo json_encode(array('errorpage' => '//errordocs/err002'));
            mysqli_close($sconn);
            exit();
        }
        else {
            $location = $_POST['loc'];
            $newStatus = ((int)$_POST['status'] == "1" ? 3 : 2);
            mysqli_stmt_bind_param($stmt, "is", $newStatus, $location);
            $status = mysqli_stmt_execute($stmt);
            
            if ($status === false) {
                //Error, Execute did not complete.
                echo json_encode(array('errorpage' => '//errordocs/err002'));
                mysqli_close($sconn);
                exit();
            } else {
                echo json_encode(array('success' => true));
                mysqli_close($sconn);
                exit();
            }
        }
    } else {
        echo json_encode(array("error" => "InvalidRequest"));
        exit();
    }
} else {
    echo json_encode(array("error" => "InvalidRequest", "request" => $_POST));
    exit();
}