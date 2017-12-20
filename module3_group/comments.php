<?php

/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module 3 - group portion
 * File name: comments.php
 * This is a php file that add the comments to a particular story. A login is required.
 */

session_start();
require("database.php");

$storynum_db = $_SESSION['storynum'];
$token_POST = $_POST['token'];

//CSRF case:
if(!hash_equals($token_POST, $_SESSION['token'])){
  session_unset();
  session_destroy();
  die("Request forgery detected");
}

$comments = htmlentities($_POST['comment']);

//get the currently logged in user
$stmt = $mysqli->prepare("SELECT usernum FROM usernames where username=?");

if(!$stmt){
  printf("Query Prep Failed: %s\n", $mysqli->error);
  exit;
}

$stmt->bind_param('s', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($usernum_db);
$stmt->fetch();
$stmt->close();

//insert the comment into the db
$stmt = $mysqli->prepare("INSERT into comments (text, usernum, storynum, votes) values (?, ?, ?, ?)");

if(!$stmt){

  printf("Query Prep Failed: %s\n", $mysqli->error);
  exit;
}
$default_vote = 0;

$stmt->bind_param('sddd', $comments, $usernum_db, $storynum_db, $default_vote);
$stmt->execute();
$stmt->close();

header("Location: contents.php");
exit;

?>
