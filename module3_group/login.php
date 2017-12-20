<!DOCTYPE html>

<?php

/*
* Name: Hakkyung Lee / Annie Chaehong Lee
* Email: hakkyung@wustl.edu / annie.lee@wustl.edu
* Assignment: Module 3 - group portion
* File name: login.php
* The is the page where user can either login or register. 
*/

//creates a session
session_start();

//session is created for the first time here.
$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));


require 'database.php';
$username_POST = $_POST['id'];
$password_POST = $_POST['password'];
$passwordCheck_POST = $_POST['passwordCheck'];



// when trying to register a new user
// checks if the username already exists,
// if not register, if yes do not register
if(isset($_POST["newUser"])){
  if($username_POST == ""){
    echo "enter a valid username";
    exit;
  }

  //if password doesn't match
  if($password_POST != $passwordCheck_POST){
    echo "passwords don't match";
  }

  else{
    $exists = false;
    $stmt = $mysqli->prepare("SELECT username FROM usernames WHERE username = ?");

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
      echo $db_id;
      $exists = true;
    }

    if(!$exists){
      
      //as mentioned in main.php, password_hash provides a random salt by default, and additional use of salt is not required. 
      $pw_hash = password_hash($password_POST, PASSWORD_DEFAULT);

      $stmt = $mysqli->prepare("insert into usernames (username, password) values (?, ?)");
      if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
      }
      $stmt->bind_param('ss', $username_POST, $pw_hash);
      $stmt->execute();
      $stmt->close();
      echo "user ".$username_POST." has been created. please login to continue.";
      $stmt->close();
    }

    else{
      echo "Username already exists.";
    }
  }
}

?>

<html>
<head>
  <meta charset = "UTF-8">
  <link rel="stylesheet" href="stylesheet.css" type="text/css">
  <title>login</title>
</head>
<body class=login>

  <form class="login" action="main.php" method="POST">
    Log in to comment or post: <br />
    <input type="text" name = "id" placeholder="user id here" /><br />
    <input type="password" name="password" placeholder="password" /><br />
    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
    <input type="submit" name="login" value = "login" />
  </form>

  <form class="login" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
    Or register as a new user:<br />
    <input type="text" name="id" placeholder="user id here" /><br />
    <input type="password" name="password" placeholder="password" /><br />
    <input type="password" name="passwordCheck" placeholder="retype password" /><br />
    <input type="submit" name="newUser" value="register" />
  </form>


</body>
</html>
