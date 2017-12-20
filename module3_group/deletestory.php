 <?php

 /*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module 3 - group portion
 * File name: deletestory.php
 * This is where a user can delete the story.
 */

session_start();
require("database.php");

//token check
$token_SESSION = $_SESSION['token'];
$token_POST = $_POST['token'];
if(!hash_equals($token_SESSION, $token_POST)){
  session_unset();
  session_destroy();
  die("Request forgery detected");
}

//-------delete all the comments for the story-------
$stmt = $mysqli->prepare("delete from comments where storynum = ?");

if(!$stmt){
  printf("Query Prep Failed: %s\n", $mysqli->error);
  exit;
}

$stmt->bind_param("d", $_POST['story_num']);
$stmt->execute();
$stmt->close();
//---------------------------------------------------

//-----------------delete the story------------------
$stmt = $mysqli->prepare("delete from story where storynum = ?");

if(!$stmt){
  printf("Query Prep Failed: %s\n", $mysqli->error);
  exit;
}

$stmt->bind_param("d", $_POST['story_num']);
$stmt->execute();
$stmt->close();
//---------------------------------------------------
header("Location: main.php");
exit;

 ?>
