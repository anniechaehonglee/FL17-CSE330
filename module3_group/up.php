<?php
session_start();
require("database.php");

$stmt = $mysqli->prepare("SELECT votes FROM comments WHERE cnum=?");
if(!$stmt){
  printf("Query Prep Failed: %s\n", $mysqli->error);
  exit;
}
$stmt->bind_param('d', $_POST['comment_num']);
$stmt->execute();
$stmt->bind_result($current_vote);
$stmt->fetch();
$stmt->close();

$update_vote = $current_vote + 1;

$stmt = $mysqli->prepare("UPDATE comments SET votes = ? WHERE cnum = ?");

if(!$stmt){
  printf("Query Prep Failed: %s\n", $mysqli->error);
  exit;
}

$stmt->bind_param('dd', $update_vote, $_POST['comment_num']);
$stmt->execute();
$stmt->close();

header("Location: contents.php");
exit;

?>
