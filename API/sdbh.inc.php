<?php
  $servername = "localhost";
  $dBUsername = "root";
  $dBPassword = "";
  $dBName = "sssprinkler";
   
  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Set MySQLi to throw exceptions
  try {
    $sconn = mysqli_connect($servername, $dBUsername, $dBPassword, $dBName);
  }
  catch (mysqli_sql_exception $e) {
    echo json_encode(array('errorpage' => './errordocs/err001', 'error' => print_r($e, 1)));
    exit();
  }

  if (!$sconn) {
      exit("Connection failed: ".mysqli_connect_error());
  }