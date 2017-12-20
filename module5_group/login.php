<?php

/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module 5 - group portion
 * File name: login.php
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

//found user from db
if($db_id != NULL){
  $exists = true;
}

//check for the password
if($exists){

  $stmt = $mysqli->prepare("SELECT hashpw, usernum FROM user WHERE username = ?");

  if(!$stmt){
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
  }

  $stmt->bind_param('s', $username_login);
  $username_login = $mysqli->real_escape_string($username_POST);
  $stmt->execute();
  $stmt->bind_result($db_pw, $usernum);
  $stmt->fetch();
  $stmt->close();

  //verify the password. Since password_verify supports random salt by default, additional salt is not required.
  $verify = password_verify($password_POST, $db_pw);
  if($verify){
    session_start();
    $_SESSION['id'] = $username_POST;
    $_SESSION['usernum'] = $usernum;
    $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
    echo json_encode(array("success" => true, "message" => "Hello!", "token" => $_SESSION['token'], "usernum" => $usernum));
    exit;
  }
  else{
    echo json_encode(array(
    "success" => false,
    "message" => "Wrong password"));
    exit;
  }
}
//user not found from the list
else{
  //send msg: usernotfound FIXME!!!

  echo json_encode(array(
    "success" => false,
    "message" => "Not a registered user"));
    exit;
}

?>
