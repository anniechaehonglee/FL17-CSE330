<?php

/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module 3 - group portion
 * File name: poststory.php
 * This is where a user can post an article.
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


//-------fetch usernumber from usernames table-------
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
//---------------------------------------------------


//------insert contents into the table story---------
if(isset($_POST["post"])){
  $title_POST = $_POST['title'];
  $contents_POST = $_POST['contents'];
  $link_POST = $_POST['link'];

  $stmt = $mysqli->prepare("INSERT into story (title, text, usernum, link) values (?, ?, ?, ?)");
  if(!$stmt){
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
  }
  $stmt->bind_param('ssss', $title_POST, $contents_POST, $usernum_db, $link_POST);
  $stmt->execute();
  $stmt->close();


  //if checked as a link update table 'storyLink'
  $storynum = $mysqli->insert_id;
  if($link_POST == "y"){
    $stmt = $mysqli->prepare("insert into storyLink (storynum, link) values (?, ?)");
    if(!$stmt){
      printf("Query Prep Failed: %s\n", $mysqli->error);
      exit;
    }
    $stmt->bind_param('ds', $storynum, $contents_POST);
    $stmt->execute();
    $stmt->close();
  }

  echo "<center>story successfully uploaded<br />
  <a href=\"main.php\">Go back to main page</a></center>";
}
//---------------------------------------------------

?>

<html>
<head>
  <title>Post Story</title>
  <meta charset="utf-8">
  <link rel="stylesheet" href="stylesheet.css" type="text/css">
</head>
<body class="postStory">

  <div class="container">

    <header>
      Post a story:<div id="login"><a href="main.php">cancel</a></div>
    </header>

    <form class="postStory" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
      <input id="title" type="text" name="title" placeholder="Title" required/>
      <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
      <input id="contents" type="text" name="contents" placeholder="Story" required/>
      Is this a link to another page?
      <input type="radio" name="link" value="y" />Yes
      <input type="radio" name="link" value="n" checked />No
      <input type="submit" name="post" value="post" />
    </form>

  </div>

</body>
</html>
