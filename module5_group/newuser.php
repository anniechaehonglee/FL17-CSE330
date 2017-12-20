<?php

/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module 5 - group portion
 * File name: newuser.php
 */

require("database.php");
header("Content-Type: application/json");

//save passed variables
$username_POST = $_POST['id'];
$password_POST = $_POST['pw'];

$exists = false;
$stmt = $mysqli->prepare("SELECT username FROM user WHERE username = ?");

if(!$stmt){
  printf("Query Prep Failed: %s\n", $mysqli->error);
  exit;
}

$stmt->bind_param('s', $username);
$username = $mysqli->real_escape_string($username_POST);
$stmt->execute();
$stmt->bind_result($db_id);
$stmt->fetch();
$stmt->close();

if($db_id != NULL){
  $exists = true;
}

if(!$exists){

  //as mentioned in main.php, password_hash provides a random salt by default, and additional use of salt is not required.
  $pw_hash = password_hash($password_POST, PASSWORD_DEFAULT);

  $stmt = $mysqli->prepare("insert into user (username, hashpw) values (?, ?)");
  if(!$stmt){
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
  }
  $stmt->bind_param('ss', $username_POST, $pw_hash);
  $stmt->execute();
  $stmt->close();

  //send result
  echo json_encode(array("success" => true, "message" => "Successfully registered!"));
  exit;
}

else{
  
  echo json_encode(array("success" => false, "message" => "Failed to register!"));
  exit;
}

?>
