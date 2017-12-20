<?php

/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module 5 - group portion
 * File name: modifyevent.php
 */

require("database.php");

header("Content-Type: application/json");

//save passed variables
$eventTitle = $_POST['eventTitle'];
$startTime = $_POST['startTime'];
$endTime = $_POST['endTime'];
$eventNum = 1;

$phpdate = strtotime($startTime);
$mysqldateStart = date('Y-m-d H:i:s', $phpdate);

$phpdate2 = strtotime($endTime);
$mysqldateEnd = date('Y-m-d H:i:s', $phpdate2);

//modify the db
$stmt = $mysqli->prepare("UPDATE event SET title = ?, startDate = ?, endDate = ? WHERE eventnum = ?");

if(!$stmt){
  printf("Query Prep Failed: %s\n", $mysqli->error);
  exit;
}

$stmt->bind_param('sssi', $eventTitle, $mysqldateStart, $mysqldateEnd, $usernum);
$stmt->execute();
$stmt->close();



echo json_encode(array("success" => true, "message" => "Event modified."));
exit;


?>
