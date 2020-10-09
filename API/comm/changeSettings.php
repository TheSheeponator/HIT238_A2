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
// ini_set('session.cookie_samesite', 'None');
// session_start();

// require "../Redirect.php";

// if (!isset($_SESSION['userId']) && basename($_SERVER['SCRIPT_FILENAME'], ".php") != "index") {
//     echo json_encode(array("redirect" => "//index?error=sessionexpired"));
//     exit();
// }

// if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
//     session_unset();
//     session_destroy();

//     echo json_encode(array("redirect" => "//index?error=sessionexpired"));
//     exit();
// }
$_SESSION['LAST_ACTIVITY'] = time();

$_JSONdata = json_decode(file_get_contents('php://input'), true);
if (isset($_JSONdata['staTime']) && isset($_JSONdata['finTime']) && isset($_JSONdata['loc'])) {
    require '../sdbh.inc.php';
    
    $loc = $_JSONdata['loc'];
    $start = $_JSONdata['staTime'];
    $end = $_JSONdata['finTime'];

        
    if (!preg_match("/^(0[0-9]|1[0-9]|2[0-3]|[0-9]):[0-5][0-9]$/",$start) || !preg_match("/^(0[0-9]|1[0-9]|2[0-3]|[0-9]):[0-5][0-9]$/",$end)) {
        $error_invData = '';
        if (!preg_match("/^(0[0-9]|1[0-9]|2[0-3]|[0-9]):[0-5][0-9]$/",$start)) {
            $error_invData .= 's1';
        } elseif (!preg_match("/^(0[0-9]|1[0-9]|2[0-3]|[0-9]):[0-5][0-9]$/",$end)) {
            $error_invData .= 'f1';
        } else {
            $error_invData .= '0';
        }

        echo json_encode(array("error" => "invalid", "loc" => "".$loc."", "col" => "".$error_invData."")); // 0 is an error and should display a generic message.
        exit();
    }

    $sql = "UPDATE times SET start1=?, end1=? WHERE id=?";
    $stmt = mysqli_stmt_init($sconn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo json_encode(array("error" => "internal"));
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, 'sss', $start1q, $end1q, $idq);
    }

    $start1q = $start;
    $end1q = $end;
    $idq = $loc;

    $status = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($status) {
        echo json_encode(array("success" => "True"));
    } else {
        echo json_encode(array("error" => "internal"));
    }
    exit();

} else {
    echo json_encode(array("error" => "invalid", "errorData" => $_JSONdata));
    exit();
}
