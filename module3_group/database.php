<?php
/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module 3 - group portion
 * File name: database.php
 */

$localhost = 'localhost';
$username = 'webuser';
$password = 'abcde';
$databasename = 'module3';

$mysqli = new mysqli($localhost, $username, $password, $databasename);

if($mysqli->connect_errno) {
	printf("Connection Failed: %s\n", $mysqli->connect_error);
	exit;
}

?>
