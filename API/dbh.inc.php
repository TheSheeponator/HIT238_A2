<?php
  $servername = "localhost";
  $dBUsername = "root";
  $dBPassword = "";
  $dBName = "loginsys";

  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Set MySQLi to throw exceptions
  try {
    $conn = mysqli_connect($servername, $dBUsername, $dBPassword, $dBName);
  }
  catch (mysqli_sql_exception $e) {
    echo json_encode(array('error' => 'internalerror'));
    die();
  }

  if (!$conn) {
      die("Connection failed: ".mysqli_connect_error());
  }