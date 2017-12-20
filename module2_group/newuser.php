<?php
/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module 2 - group portion
 * File name: newuser.php
 * creates a new user
 */

//continues a session
session_start();

//catches the direct url access
if($_SESSION['enter'] != true){

    header("Location: file_share.php");
}

$newUser = trim($_SESSION['newUser']);

$txt = "";

//adds a new username to the list
if(isset($_POST["yes"])){

    $id = fopen(__DIR__ . '/../.safe/users.txt', "r");

    while(!feof($id)){
      
        $txt = $txt.trim(fgets($id))."\n";
    }
    fclose($id);

    $id = fopen(__DIR__ . '/../.safe/users.txt', "w");
    $txt = $txt.$newUser."\n";
    fwrite($id, $txt);

    //also makes the corresponding directory
    mkdir("/home/hakkyung/users/".$newUser, 0755);
    fclose($id);

    $_SESSION['id'] = $newUser;
    header("Location: main.php");
    exit;
}
else{
  
    header("Location: file_share.php");
    exit;
}

?>
