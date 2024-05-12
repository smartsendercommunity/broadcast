<?php

$result["state"] = true;
$input = json_decode(file_get_contents("php://input"), true);
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "GET") {
  if ($_GET["ssId"] == NULL) {
    $result["state"] = false;
    $result["error"]["message"][] = "'ssId' is missing";
    echo json_encode($result);
    exit;
  }
  if ($_GET["segment"] != NULL) {
    $sql = "SELECT * FROM `audience` WHERE `ssId` = '".$_GET["ssId"]."' AND `segment` = '".mysqli_real_escape_string($sqlConnect, $_GET["segment"])."'";
    $result["segment"] = mysqli_fetch_all(mysqli_query($sqlConnect, $sql), MYSQLI_ASSOC);
    $result["count"] = count($result["segment"]);
  } else {
    $sql = "SELECT * FROM `audience` WHERE `ssId` = '".$_GET["ssId"]."'";
    $result["segments"] = mysqli_fetch_all(mysqli_query($sqlConnect, $sql), MYSQLI_ASSOC);
    $result["count"] = count($result["segments"]);
  }
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if ($input["segment"] == NULL) {
    $result["state"] = false;
    $result["error"]["message"][] = "'segment' is missing";
  }
  if ($input["trigger"] == NULL) {
    $result["state"] = false;
    $result["error"]["message"][] = "'trigger' is missing";
  }
  if ($result["state"] == false) {
    echo json_encode($result);
    exit;
  }
  if ($input["limit"] == NULL) {
    $input["limit"] = 100;
  } else {
    settype($input["limit"], "int");
  }
  if ($input["offset"] == NULL) {
    $input["offset"] = 0;
  } else {
    settype($input["offset"], "int");
  }
  $sql = "SELECT * FROM `audience` WHERE `segment` = '".mysqli_real_escape_string($sqlConnect, $input["segment"])." GROUP BY `ssId`' ORDER BY `id` LIMIT ".$input["limit"]." OFFSET ".$input["offset"]."";
  $contacts = mysqli_fetch_all(mysqli_query($sqlConnect, $sql), MYSQLI_ASSOC);
  $result["counts"] = [
    "success" => 0,
    "error" => 0,
  ];
  if ($contacts != NULL) {
    foreach ($contacts as $oneContact) {
      $action = json_decode(send_bearer("https://api.smartsender.com/v1/contacts/".$oneContact["ssId"]."/fire", $ssToken, "POST", ["name" => $input["trigger"]]), true);
      if ($action["state"] == true) {
        $result["counts"]["success"] ++;
      } else {
        $result["counts"]["error"] ++;
      }
    }
  }
} else if ($_SERVER["REQUEST_METHOD"] == "PUT") {
  if ($input["segment"] == NULL) {
    $result["state"] = false;
    $result["error"]["message"][] = "'segment' is missing";
  }
  if ($input["ssId"] == NULL) {
    $result["state"] = false;
    $result["error"]["message"][] = "'ssId' is missing";
  }
  if ($result["state"] == false) {
    echo json_encode($result);
    exit;
  }
  $sql = "INSERT INTO `audience` (`ssId`, `segment`, `time`) VALUES ('".$input["ssId"]."', '".mysqli_real_escape_string($sqlConnect, $input["segment"])."', '".time()."')";
  $insert = mysqli_query($sqlConnect, $sql);
  if ($insert == false) {
    $result["state"] = false;
    $result["error"]["message"][] = mysqli_error($sqlConnect);
  } else {
    $result["insertId"] = mysqli_insert_id($sqlConnect);
  }
} else if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
  if ($input["segment"] == NULL) {
    $result["state"] = false;
    $result["error"]["message"][] = "'segment' is missing";
  }
  if ($input["ssId"] == NULL) {
    $result["state"] = false;
    $result["error"]["message"][] = "'ssId' is missing";
  }
  if ($result["state"] == false) {
    echo json_encode($result);
    exit;
  }
  if ($input["segment"] == "ALL_SEGMENTS") {
    $sql = "DELETE FROM `audience` WHERE `ssId` = '".$input["ssId"]."'";
  } else {
    $sql = "DELETE FROM `audience` WHERE `ssId` = '".$input["ssId"]."' AND `segment` = '".mysqli_real_escape_string($sqlConnect, $input["segment"])."'";
  }
  $delete = mysqli_query($sqlConnect, $sql);
  if ($delete == false) {
    $result["state"] = false;
    $result["error"]["message"][] = mysqli_error($sqlConnect);
  } else {
    $result["deletedRows"] = mysqli_affected_rows($sqlConnect);
  }
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
