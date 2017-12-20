<?php

/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module 2 - group portion
 * File name: deleteuser.php
 * deletes a user from the list
 */

//continues a session
session_start();

//function used from https://paulund.co.uk/php-delete-directory-and-files-in-directory
function delete_directory($dirname) {
         if (is_dir($dirname))
           $dir_handle = opendir($dirname);
	 if (!$dir_handle)
	      return false;
	 while($file = readdir($dir_handle)) {
	       if ($file != "." && $file != "..") {
	            if (!is_dir($dirname."/".$file))
	                 unlink($dirname."/".$file);
	            else
	                 delete_directory($dirname.'/'.$file);
	       }
	 }
	 closedir($dir_handle);
	 rmdir($dirname);
	 return true;
}

//deletes the user from the list
$deleteUser = trim($_SESSION['deleteUser']);
$txt = "";
if(isset($_POST["yes"])){

    $id = fopen(__DIR__ . '/../.safe/users.txt', "r");

    while(!feof($id)){

        $temp = trim(fgets($id));

        if($temp != $deleteUser){

            $txt = $txt.$temp."\n";
        }
    }

    fclose($id);

    $id = fopen(__DIR__ . '/../.safe/users.txt', "w");
    fwrite($id, $txt);
    fclose($id);

    delete_directory("/home/hakkyung/users/".$deleteUser);

    $_SESSION['id'] = $deleteUser;
    header("Location: file_share.php");
    exit;
}

if(isset($_POST["no"])){

    header("Location: file_share.php");
    exit;
}

?>