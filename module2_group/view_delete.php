<?php

/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module 2 - group portion
 * File name: view_delete.php
 * opens the file and deletes it from the server
 */

//continues a session
session_start();

//catches the direct url access
if($_SESSION['enter'] != true){

    header("Location: file_share.php");
}

$fileDir = "/home/hakkyung/users/".$_SESSION['id']."/".$_POST["file"];

//view case
if(isset($_POST["view"])){

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($fileDir);

    //based on the $mime, if it is a type which the browser can open, open with browser. If not, it force downloads the file.
    switch($mime){

      //not exhaustive cases
      case 'text/plain':
      case 'text/html':
      case 'image/png':
      case 'image/jpeg':
      case 'image/gif':
      case 'image/bmp':
      case 'application/pdf':

        header("Content-Type: ".$mime);
        readfile($fileDir);
        break;

        //referred to http://php.net/manual/en/function.readfile.php
      default:
        	header('Content-Description: File Transfer');
        	header('Content-Type: application/octet-stream');
        	header('Content-Disposition: attachment; filename="'.basename($_POST["file"]).'"');
        	header('Expires: 0');
        	header('Cache-Control: must-revalidate');
        	header('Pragma: public');
        	header('Content-Length: ' . filesize($_POST["file"]));
        	readfile($_POST["file"]);
          break;
    }
}
//delete case
else if(isset($_POST["delete"])){

    //unlink the directory
    unlink($fileDir);
    echo htmlentities($_POST["file"])." has been successfully deleted.";
}
?>
