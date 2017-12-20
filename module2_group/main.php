<!DOCTYPE html>

<?php

/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module 2 - group portion
 * File name: main.php
 * This is a main page where a user sees once he/she enters the username.
 */

//continues a session
session_start();
	
	//catches the direct url access
if($_SESSION['enter'] != true){

	header("Location: file_share.php");
}

$currentUser = $_SESSION['id'];
$dir = "/home/hakkyung/users/".$currentUser;

$fileList = array();

//http://php.net/manual/en/function.opendir.php
if(is_dir($dir)){

	if($open = opendir($dir)){

		while(($file = readdir($open)) !== false){
			
			//get the list of files in the directory
			array_push($fileList, $file);
		}
		closedir($open);
	}
}
?>

<html>
<head>
	<meta charset = "UTF-8">
	<title>PHP File Sharing System</title>
	<style>
	body{
		padding: 5vw;
		display: block;
	}
	.title{
		margin: auto;
		padding-bottom: 30px;
	}
	form{
		margin: auto;
		padding-bottom: 30px;
	}
	select{
		min-width: 150px;
	}
	</style>
</head>
<body>
	<div class="title">Current user
		<?php echo " : ".htmlentities($currentUser); ?>
	</div>
	<br>

	<form enctype="multipart/form-data" action="upload.php" method="POST">
		<!-- referenced the class webpage:
		https://classes.engineering.wustl.edu/cse330/index.php?title=PHP#Other_PHP_Tips -->
		<input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
		<label for="uploadfile_input">Choose a file to upload:</label>
		<input name="uploadedfile" type="file" id="uploadfile_input" />
		<input type="submit" value="Upload File" />
	</form>

	<form action="view_delete.php" method="POST">
		<select name="file">
			<?php
			for ($i = 0; $i < count($fileList); $i++){

				//not showing "." and ".." on the list of file
				if($fileList[$i] == "." || $fileList[$i] == ".."){

					continue;
				}
				else{

					echo "<option value=\"".htmlentities($fileList[$i])."\">".htmlentities($fileList[$i])."</option>";
				}
			}
			?>
		</select>
		<input type="submit" value="View File" name="view">
		<input type="submit" value="Delete File" name="delete">
	</form>

	<form action="logout.php" method="POST">
		<input type="submit" value="Logout">
	</form>

</body>
</html>
