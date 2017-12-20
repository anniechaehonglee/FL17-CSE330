<!DOCTYPE html>

<!--
	Name: Hakkyung Lee / Annie Chaehong Lee
	Email: hakkyung@wustl.edu / annie.lee@wustl.edu
	Assignment: Module 3 - group portion
	File name: editcomment.php
	This is where a user can edit the comment. 
-->

<html>
	<head>
		<meta charset = "utf-8">
	</head>
	<body>

		<?php

		session_start();
		require("database.php");

		if(isset($_POST["editComment"])){

			//CSRF case:
			$token_SESSION = $_SESSION['token'];
			$token_POST = $_POST['token'];
			if(!hash_equals($token_SESSION, $token_POST)){
			  session_unset();
			  session_destroy();
			  die("Request forgery detected");
			}

			$newComment = $_POST['comment'];
			$comment_num = $_POST['comment_num'];

			//update the comment based on the comment_number
			$stmt = $mysqli->prepare("UPDATE comments SET text = ? WHERE cnum = ?");

			if(!$stmt){

				printf("Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}

			$stmt->bind_param('sd', $newComment, $comment_num);
			$stmt->execute();
			$stmt->close();

			header("Location: contents.php");
			exit;
		}

		$comment_num = $_POST['comment_num'];
		$comment_db = $_POST['comment_db'];


		echo "<form action = \"".$_SERVER['PHP_SELF']."\" method = \"POST\">
				<input type = \"text\" name = \"comment\" value = \"".$comment_db."\">
				<input type = \"hidden\" name = \"comment_num\" value = \"".$comment_num."\">
				<input type = \"hidden\" name = \"token\" value = \"".$_SESSION['token']."\">
				<input type = \"submit\" name = \"editComment\" value = \"edit\">
			</form>";


		?>


	</body>
</html>
