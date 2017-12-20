<?php

/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module 5 - group portion
 * File name: addevent.php
 */

session_start();
require("database.php");

//json type
header("Content-Type: application/json");

//not logged in
if($_SESSION['usernum'] == null){
  
  echo json_encode(array("success"=>false, "message"=>"Please login to add events."));
  exit();
}
else{

  //save passed variables
  $eventTitle = $_POST['eventTitle'];
  $startTime = $_POST['startTime'];
  $endTime = $_POST['endTime'];
  $usernum = $_SESSION['usernum'];

  $phpdate = strtotime($startTime);
  $mysqldateStart = date('Y-m-d H:i:s', $phpdate);

  $phpdate2 = strtotime($endTime);
  $mysqldateEnd = date('Y-m-d H:i:s', $phpdate2);

  //insert event into db
  $stmt = $mysqli->prepare("INSERT into event (title, startDate, endDate, usernum) values (?, ?, ?, ?)");

  if(!$stmt){
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
  }

  $stmt->bind_param('sssi', $eventTitle, $mysqldateStart, $mysqldateEnd, $usernum);
  $stmt->execute();
  $stmt->close();

  //result
  echo json_encode(array("success" => true, "usernum"=>$usernum, "message" => "Event successfully added."));
  exit;
}

?>
