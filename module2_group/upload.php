<?php

/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module 2 - group portion
 * File name: upload.php
 * uploads a file to the server
 */

//continues a session
session_start();

//catches the direct url access
if($_SESSION['enter'] != true){

	header("Location: file_share.php");
}

// Get the fileName and make sure it is valid
$fileName = basename($_FILES['uploadedfile']['name']);

if( !preg_match('/^[\w_\.\-]+$/', $fileName) ){

	echo "Invalid fileName";
  	exit;
}

$fileDir = "/home/hakkyung/users/".$_SESSION['id']."/".$fileName;

if( move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $fileDir) ){

 
	echo htmlentities($fileName)." has been successfully uploaded.";
	exit;


  echo htmlentities($fileName)." has been successfully uploaded.";
  exit;

}
else{

	echo "Failed to upload ".htmlentities($fileName);
	exit;
}

?>
