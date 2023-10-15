<?php

ini_set('max_execution_time', '1700');
set_time_limit(1700);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Content-Type: application/json; charset=utf-8');
http_response_code(200);
error_reporting(E_ALL);
ini_set('display_errors', 1);

$ssToken = "";
$server = "";
$username = "";
$password = "";
$database = "";

mysqli_report(MYSQLI_REPORT_OFF);
$sqlConnect = mysqli_connect($server, $username, $password, $database);
if ($sqlConnect === false) {
  $result["state"] = false;
  $result["error"]["message"] = "error connecting to MySQL";
  echo json_encode($result);
  exit;
}
mysqli_set_charset($sqlConnect, "utf8mb4");
mysqli_options($sqlConnect, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, true);

function send_bearer($url, $token, $type = "GET", $param = []){
  $descriptor = curl_init($url);
  curl_setopt($descriptor, CURLOPT_POSTFIELDS, json_encode($param));
  curl_setopt($descriptor, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($descriptor, CURLOPT_HTTPHEADER, array("User-Agent: M-Soft Integration", "Content-Type: application/json", "Authorization: Bearer ".$token)); 
  curl_setopt($descriptor, CURLOPT_CUSTOMREQUEST, $type);
  $itog = curl_exec($descriptor);
  curl_close($descriptor);
  return $itog;
}