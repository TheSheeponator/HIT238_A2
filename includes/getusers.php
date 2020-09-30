<?php

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