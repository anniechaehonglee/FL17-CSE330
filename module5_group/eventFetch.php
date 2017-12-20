<?php
/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module 5 - group portion
 * File name: eventFetch.php
 */

require("database.php");
header("Content-Type: application/json");

//usernum is gottom from session variable after login
$usernum = $_POST['usernum'];

//fetch all
$stmt = $mysqli->prepare("SELECT title, startDate, endDate, eventnum FROM event WHERE usernum = ?");
if(!$stmt){
  printf("Query Prep Failed: %s\n", $mysqli->error);
  exit;
}

$stmt->bind_param('i', $usernum);
$stmt->execute();
$stmt->bind_result($title_db, $startDate_db, $endDate_db, $eventnum_db);

//arrays to store all events information in
$title = [];
$startTime = [];
$endTime = [];
$eventnum = [];

//as fetching one by one, push to the arrays
while($stmt->fetch()){
  array_push($title, $title_db);
  array_push($startTime, $startDate_db);
  array_push($endTime, $endDate_db);
  array_push($eventnum, $eventnum_db);
}

echo json_encode( array("success"=> true, "title"=>$title, "startTime"=>$startTime, "endTime"=>$endTime, "eventnum"=>$eventnum));

$stmt->close();

?>
