<?php

/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module 3 - group portion
 * File name: main.php
 * This is the very first page a user faces. A user can see the stories without logging in.
 * However, if s/he wants to post or edit, s/he needs to log in.
 */

session_start();
require("database.php");

$username_POST = $_POST['id'];
$password_POST = $_POST['password'];
$token_POST = $_POST['token'];
$token_SESSION = $_SESSION['token'];

//when a user tries to log in
//checks if the user name exists,
//if not do not login, if yes login
if(isset($_POST["login"])){

  if($username_POST == ""){

    header("Location: login.php");
    exit;
  }

  $exists = false;
  $stmt = $mysqli->prepare("SELECT username FROM usernames WHERE username = ?");

  if(!$stmt){
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
  }

  $stmt->bind_param('s', $username);
  $username = $mysqli->real_escape_string($username_POST);
  $stmt->execute();
  $stmt->bind_result($db_id);
  $stmt->fetch();
  $stmt->close();

  //found user from db
  if($db_id != NULL){
    $exists = true;
  }

  //check for the password
  if($exists){

    $stmt = $mysqli->prepare("SELECT password FROM usernames WHERE username = ?");

    if(!$stmt){
      printf("Query Prep Failed: %s\n", $mysqli->error);
      exit;
    }

    $stmt->bind_param('s', $username_login);
    $username_login = $mysqli->real_escape_string($username_POST);
    $stmt->execute();
    $stmt->bind_result($db_pw);
    $stmt->fetch();

    //verify the password. Since password_verify supports random salt by default, additional salt is not required.
    $verify = password_verify($password_POST, $db_pw);
    if($verify){
      $_SESSION['id'] = $username_POST;
    }
    else{
      header("Location: login.php");
      exit();
    }
    $stmt->close();
  }

  //user not found from the list
  else{
    header("Location: login.php");
    exit();
  }
}

if($verify){

  //CSRF case:
  if(!hash_equals($token_SESSION, $token_POST)){
    session_unset();
    session_destroy();
    die("Request forgery detected");
  }

  $_SESSION['id'] = $username_POST;
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>News Sharing Site</title>
  <meta charset="utf-8">
  <link rel="stylesheet" href="stylesheet.css" type="text/css">
</head>
<body>

  <div class="container">

    <header>
          CSE330 NEWS
          <?php

          //when logged in
          if((isset($_POST["login"])) || ($_SESSION['id'] != NULL)){
            echo "<div id=\"login\">".$_SESSION['id']."
            <form action=\"poststory.php\" method=\"POST\">
            <input id=\"btn\" type=\"submit\" value=\"post\" name=\"token\"/>
            <input type=\"hidden\" value=\"".$token_SESSION."\" name=\"token\" />
            </form>
            <a id=\"btn\" href=\"logout.php\">Logout</a></div>";
          }
          else{
            echo "<div id=\"login\"><a href=\"login.php\">Login</a></div>";
          }
          ?>

    </header>

    <div class="artcle">
      <table>
        <tr>
          <th>#</th>
          <th>Title</th>
          <th>Author</th>
        </tr>

        <?php

          //let the user filter stories based on the author
          if(isset($_POST["search"])){

            $search_name = $_POST['searchbar'];

            $stmt = $mysqli->prepare("SELECT title, storynum, usernames.username FROM story JOIN usernames ON (story.usernum = usernames.usernum) WHERE username = ?");

            if(!$stmt){
              printf("Query Prep Failed: %s\n", $mysqli->error);
              exit;
            }

            $stmt->bind_param('s', $search_name);
            $stmt->execute();
            $stmt->bind_result($title_db, $storynum_db, $username_db);

            while($stmt->fetch()){

              //inserts fetched values as a table row:
              echo "<tr><td>".$storynum_db."</td>
              <td><form action=\"contents.php\" method=\"POST\">
              <input type=\"hidden\" value=\"$storynum_db\" name=\"storynum\" />
              <input type=\"hidden\" value=\"".$token_SESSION."\" name=\"token\" />
              <input id=\"btn\" type=\"submit\" value=\"".$title_db."\" name=\"toContents\"/>
              </form>
              <td>".$username_db."</td></tr>";
            }

            $stmt->close();
          }

          //reset the filter
          else if(isset($_POST["clear"])){

            header("Location: main.php");
            exit;
          }

          //default case
          else{

            $stmt = $mysqli->prepare("SELECT title, storynum, usernames.username FROM story join usernames on (story.usernum=usernames.usernum)");

            if(!$stmt){
              printf("Query Prep Failed: %s\n", $mysqli->error);
              exit;
            }

            $stmt->execute();
            $stmt->bind_result($title_db, $storynum_db, $username_db);
            while($stmt->fetch()){

              //inserts fetched values as a table row:
              echo "<tr><td>".$storynum_db."</td>
              <td><form action=\"contents.php\" method=\"POST\">
              <input type=\"hidden\" value=\"$storynum_db\" name=\"storynum\" />
              <input type=\"hidden\" value=\"".$token_SESSION."\" name=\"token\" />
              <input id=\"btn\" type=\"submit\" value=\"".$title_db."\" name=\"toContents\"/>
              </form>
              <td>".$username_db."</td></tr>";
            }
            $stmt->close();
          }
        ?>
      </table>
    </div>

    <div class = "search">

      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method = "POST">

          <input id="searchuser" type="text" name = "searchbar" placeholder="Search for a specific user.">
          <input type="submit" name = "search" value = "search" style="min-width: 150px;">
          <input type="submit" name = "clear" value = "clear">

      </form>
    </div>

    <footer>Copyright &copy; cse330</footer>

  </div>

</body>
</html>
