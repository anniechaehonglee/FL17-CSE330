<?php

/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module 2 - group portion
 * File name: logout.php
 * simply let the user logout from the website
 */

//continues a session
session_start();

//resets the session variables
session_unset();

//destroy the session
session_destroy();

//redirect to main page
header("Location: file_share.php");
exit;
?>
