<?php

/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module 5 - group portion
 * File name: deletecomment.php
 * This is where a user can delete the comment.
 */

session_start();
require("database.php");
header("Content-Type: application/json");
$eventnum = $_POST['eventnum'];

//delete the comment based on the comment_number
$stmt = $mysqli->prepare("delete from event where eventnum = ?");

if(!$stmt){
  printf("Query Prep Failed: %s\n", $mysqli->error);
  exit;
}

$stmt->bind_param("d", $eventnum);
$stmt->execute();
$stmt->close();

echo json_encode(array("success" => true, "message" => "Event modified."));
exit;

 ?>
