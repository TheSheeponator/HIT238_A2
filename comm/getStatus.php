<?php

class wrapper {
    public $zoneStatus;
}
class zone {
    public $id;
    public $data;
}
class zoneData {
    public $status;
    public $staTime;
    public $finTime;
    // public $duration;
    public $title;
}
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
ini_set('session.cookie_samesite', 'None');
session_start();

require '../includes/sdbh.inc.php';


$sql = 'SELECT id, name, start1, end1, status FROM times';
$result = mysqli_query($sconn, $sql);

if (!$result) {
    mysqli_close($sconn);
    echo json_encode(array('errorpage' => '//errordocs/err002'));
    exit();
} else {
    function GetTimeDiff ($start, $end) {
        $startT = explode(":", $start);
        $endT = explode(":", $end);
        $sMili = ((int)$startT[0] * 3600000) + ((int)$startT[1] * 60000);
        $eMili = ($endT[0] * 3600000) + ($endT[1] * 60000);
        $diff = $eMili - $sMili;
        $hours = floor($diff / 1000 / 60 / 60);
        $diff -= $hours * 1000 * 60 * 60;
        $minutes = floor($diff / 1000 / 60);
        if ($hours < 0 ) { $hours += 24; }
        return ($hours == 0 ? "" : $hours."h ").($hours != 0 && $minutes == 0 ? "" : $minutes." min");
    }

    $output = array();

    while($row = mysqli_fetch_assoc($result)) {

        $id = $row['id'];
        $title = $row['name'];
        $status = $row['status'];
        
        $sqlstart = $row['start1'];
        $sqlend = $row['end1'];
        
        $staTime = date('h:i', strtotime($sqlstart));
        $finTime = date('h:i', strtotime($sqlend));
        // $duration = GetTimeDiff($sqlstart, $sqlend);
        
        
        if (explode(":", $sqlstart)[0] >= 12) {
            $staTime .= " pm";
        } else {
            $staTime .= " am";
        }
        if (explode(":", $sqlend)[0] >= 12) {
            $finTime .= " pm";
        } else {
            $finTime .= " am";
        }
        
        $out = new zoneData();
        
        if ($status == 0) {
            // Blue (stand-by), ON, OFF
            $out->status = '0';
            $out->staTime = $staTime;
            $out->finTime = $finTime;
            // $out->duration = $duration;
            $out->title = $title;
        }
        elseif ($status == 1) {
            // Green (water on), ON, OFF
            $out->status = '1';
            $out->staTime = $staTime;
            $out->finTime = $finTime;
            // $out->duration = $duration;
            $out->title = $title;
        }
        elseif ($status == 2) {
            // Red (water off/manual), ON, AUTO
            $out->status = '2';
            $out->staTime = $staTime;
            $out->finTime = $finTime;
            // $out->duration = $duration;
            $out->title = $title;
        }
        elseif ($status == 3) {
            // Dark-Green (water on/manual), OFF, AUTO
            $out->status = '3';
            $out->staTime = $staTime;
            $out->finTime = $finTime;
            // $out->duration = $duration;
            $out->title = $title;
        }
        elseif ($status == 4) {
            // Orange (water off/weather), ON, OFF
            $out->status = '4';
            $out->staTime = $staTime;
            $out->finTime = $finTime;
            // $out->duration = $duration;
            $out->title = $title;
        }
        else {
            // grey (error - number not expected)
            $out->status = '5';
            $out->staTime = '00:00';
            $out->finTime = '00:00';
            // $out->duration = '0';
            $out->title = '--------';
        }
        $zone = new zone();
        $zone->data = $out;
        $zone->id = $id;
        array_push($output, $zone);
    }
    $wrapper = new wrapper();
    $wrapper->zoneStatus = $output;
    echo json_encode($wrapper);
    mysqli_close($sconn);
    exit();
}