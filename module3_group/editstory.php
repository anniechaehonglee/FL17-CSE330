<?php

/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module 3 - group portion
 * File name: editstory.php
 * This is where a user can edit the story. 
 */

session_start();
require("database.php");

//---------------------edit story------------------
if(isset($_POST["editstory"])){

  //CSRF case:
    $token_SESSION = $_SESSION['token'];
    $token_POST = $_POST['token'];
    
    if(!hash_equals($token_SESSION, $token_POST)){
      
      session_unset();
      session_destroy();
      die("Request forgery detected");
    }

  $title_POST = $_POST['title'];
  $contents_POST = $_POST['text'];
  $storynum = $_POST['story_num'];

  $stmt = $mysqli->prepare("update story set title = ? , text = ? where storynum = ?");

  if(!$stmt){
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
  }

  $stmt->bind_param('ssd', $title_POST, $contents_POST, $storynum);
  $stmt->execute();
  $stmt->close();

  header("Location: main.php");
  exit;
}
//---------------------------------------------------

?>

<html>
<head>
  <title>Edit Story</title>
  <meta charset="utf-8">
  <link rel="stylesheet" href="stylesheet.css" type="text/css">
</head>
<body class="postStory">

  <div class="container">

    <header>
      Edit story<div id="login"><a href="main.php">cancel</a></div>
    </header>

    <?php
    //fetch post information
    $title = $_POST['title'];
    $text = $_POST['text'];
    $storynum = $_POST['story_num'];
    echo
    "<form class=\"postStory\" action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\">
      <input type=\"hidden\" name=\"token\" value=\"".$_SESSION['token']."\">
      <input id=\"title\" type=\"text\" name=\"title\" value=\"".$title."\" required/>
      <input id=\"contents\" type=\"text\" name=\"text\" value=\"".$text."\" required/>
      <input type=\"hidden\" value=\"".$storynum."\" name=\"story_num\" />
      <input type=\"submit\" name=\"editstory\" value=\"edit\" />
    </form>";
     ?>

  </div>

</body>
</html>
