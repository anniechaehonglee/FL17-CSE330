<?php

/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module 3 - group portion
 * File name: deletecomment.php
 * This is where a user can delete the comment.
 */

session_start();
require("database.php");

//CSRF case:
$token_SESSION = $_SESSION['token'];
$token_POST = $_POST['token'];
if(!hash_equals($token_SESSION, $token_POST)){
  session_unset();
  session_destroy();
  die("Request forgery detected");
}


//delete the comment based on the comment_number
$stmt = $mysqli->prepare("delete from comments where cnum = ?");

if(!$stmt){
  printf("Query Prep Failed: %s\n", $mysqli->error);
  exit;
}

$stmt->bind_param("d", $_POST['comment_num']);
$stmt->execute();
$stmt->close();

header("Location: contents.php");
exit;

 ?>
