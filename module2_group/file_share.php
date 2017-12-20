<!DOCTYPE html>

<?php

/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module 2 - group portion
 * File name: file_share.php
 * The very first page a user encounters. A user can register or delete the username.
 */

//creates a session
session_start();

//opens a list of user
$id = fopen(__DIR__ . '/../.safe/users.txt', "r");

//when a user tries to log in
if(isset($_POST["login"])){

	$found = false;

	$userCheck = $_POST['id'];

	if($userCheck == ""){

		header("Location: file_share.php");
		exit;
	}

	while(!feof($id)){

		$temp = trim(fgets($id));

		//a username is in the list	
		if($temp == $userCheck){

			$found = true;
			$_SESSION['id'] = $temp;

			//used to prevent improper url access later;
			$_SESSION['enter'] = true;
			header("Location: main.php");
			exit;
		}
	}

	//not found from the list
	if($found == false){

		$_SESSION['newUser'] = $userCheck;
		
		//passing empty input
		if($userCheck == ""){

		}
		else{

			echo "User not found. Add "."\"".htmlentities($userCheck)."\""." as a new user?
			<form action=\"newuser.php\" method=\"POST\">
			<input type=\"submit\" name=\"yes\" value=\"yes\">
			<input type=\"submit\" name=\"no\" value=\"no\">
			</form>";
		}
	}
}

//a user tries to delete the name
if(isset($_POST["delete"])){
	$found = false;
	$userCheck = $_POST['id'];

	while(!feof($id)){

		$temp = trim(fgets($id));

		if($temp == $userCheck){

			$found = true;
		}
	}

	//not found
	if($found == false){
		echo "There is a no such user.";
	}

	//found
	else{
		$_SESSION['deleteUser'] = $userCheck;
		echo "<div class=\"popup\">Are you sure you want to delete ".htmlentities($userCheck)."?
		<form action=\"deleteuser.php\" method=\"POST\">
		<input type=\"submit\" name=\"yes\" value=\"yes\">
		<input type=\"submit\" name=\"no\" value=\"no\">
		</form></div>";
	}
}

fclose($id);
?>

<html>
<head>
	<meta charset = "UTF-8">
	<title>PHP File Sharing System</title>
	<style>

	h1{

		text-align: center;
		font-size: 3em;
	}
	body {
		padding: 5vw;
		display: flex;
		align-items: center;
		flex-direction: column;
	}
	form {
		margin: auto;
	}
	.popup{
		margin: auto;
		padding-bottom: 15px;
	}
	</style>
</head>

<body>
	<h1>PHP File Share System</h1>
	<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method = "POST">
		<input type="text" name = "id" placeholder="user id here">
		<input type="submit" name="login" value = "login">
		<input type="submit" name="delete" value = "delete user">
	</form>

</body>

</html>
