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
if($_SERVER['REQUEST_METHOD'] == 'GET') {
    header("Location: /errordocs/err003");
    exit();
}

require './checkperm.php';
checkPerm_ajax();

// Reset session timeout.
$_SESSION['LAST_ACTIVITY'] = time();

class wrapper {
    public $userData;
}
class users {
    function __construct($name, $email, $permLevel) {
        $this->name = $name;
        $this->email = $email;
        $this->permLevel = $permLevel;
    }
}

require './dbh.inc.php';

$sql = 'SELECT `uidUsers`,`emailUsers`,`Perms` FROM `sysusers`';
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    redirect("Location: ./users?error=sqlerror");
}
else {
    $result = mysqli_query($conn, $sql) or Die(mysqli_error($conn));

    $wrapper = new wrapper();
    $wrapper->userData = array();

    while ($row = mysqli_fetch_array($result)) {
        if ($row['Perms'] == "1") {
            array_push($wrapper->userData, new users($row['uidUsers'], '******', '1'));
        } else {
            array_push($wrapper->userData, new users($row['uidUsers'], $row['emailUsers'], $row['Perms']));
        }
    }
    echo json_encode($wrapper);
    mysqli_close($conn);
    exit();
}