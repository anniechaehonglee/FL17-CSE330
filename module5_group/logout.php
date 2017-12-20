<?php

/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module 5 - group portion
 * File name: logout.php
 * simply let the user logout from the website
 */

header("Content-Type: application/json");

//continues a session
session_start();

//resets the session variables
session_unset();

//destroy the session
session_destroy();

echo json_encode(array("success" => true, "message" => "Logout!"));
exit;
?>
