<!DOCTYPE html>
<!--
  Name: Hakkyung Lee / Annie Chaehong Lee
  Email: hakkyung@wustl.edu / annie.lee@wustl.edu
  Assignment: Module 3 - group portion
  File name: contents.php
  This is a place where a user can see the story. Login is not required to see the story.
-->
<html>
<head>
  <title>Contents</title>
  <meta charset="utf-8">
  <link rel="stylesheet" href="stylesheet.css" type="text/css">
</head>

<body>

  <?php
  session_start();
  require("database.php");

  if(isset($_POST["toContents"])){
    $storynum_POST = $_POST["storynum"];
    $_SESSION['storynum'] = $storynum_POST;
  }

  $stmt = $mysqli->prepare("SELECT title, text, usernames.username, storynum, link FROM story join usernames on (story.usernum=usernames.usernum) where storynum = ?");

  if(!$stmt){
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
  }

  $stmt->bind_param("d", $_SESSION['storynum']);
  $stmt->execute();
  $stmt->bind_result($title_db, $text_db, $username_db, $story_num, $link);
  $stmt->fetch();
  $stmt->close();

  //if the story is a link, break and just open a new tab
  if($link == "y"){
    $location = "Location: http://".$text_db."/";
    header($location);
    exit;
  }

  echo "<header><table><tr><tb><b>".$title_db."</b></tb><tb>  author:  ".$username_db."</tb><tb id=\"login\"><a href=\"main.php\">Back</a></tb>";

  //show EDIT/DELETE button only to the author
  if($_SESSION['id'] == $username_db){
    echo "<tb id=\"login\"><form action=\"editstory.php\" method=\"POST\">
    <input type=\"submit\" value=\"edit\" name=\"edit\"/>
    <input type=\"hidden\" value=\"".$story_num."\" name=\"story_num\" />
    <input type=\"hidden\" value=\"".$title_db."\" name=\"title\" />
    <input type=\"hidden\" value=\"".$text_db."\" name=\"text\" />
    <input type=\"hidden\" name=\"token\" value=\"".$_SESSION['token']."\">
    </form></tb>";

    echo "<tb id=\"login\"><form action=\"deletestory.php\" method=\"POST\">
    <input type=\"submit\" value=\"delete\" name=\"delete\"/>
    <input type=\"hidden\" value=\"".$story_num."\" name=\"story_num\" />
    <input type=\"hidden\" name=\"token\" value=\"".$_SESSION['token']."\">
    </form></tb></tr></table></header>";
  }
  else{
    echo "</tr></table></header>";
  }
  echo "<div class=\"contents\">".$text_db."</div>";
  ?>

  <?php
  if(($_SESSION['id'] != NULL)){
    echo "
    <form action=\"comments.php\" method=\"POST\" id=\"postComments\">
      <input id=\"commentinput\" type=\"text\" name=\"comment\" placeholder=\"Comment here\">
      <input id = \"submitCmt\" type=\"submit\" name = \"submitCmt\" value = \"Post\">
      <input type=\"hidden\" name=\"token\" value=\"".$_SESSION['token']."\">
    </form>";
  }
  ?>

  <div class="comments">
    <?php
    $stmt = $mysqli->prepare("SELECT text, usernames.username, cnum, votes FROM comments join usernames on (comments.usernum=usernames.usernum) where storynum=?");

    if(!$stmt){
      printf("Query Prep Failed: %s\n", $mysqli->error);
      exit;
    }

    $stmt->bind_param('d', $_SESSION['storynum']);
    $stmt->execute();
    $stmt->bind_result($comment_db, $usercomment_db, $comment_num, $num_votes);

    while($stmt->fetch()){
      echo "<div id=\"comment\"><b>".$usercomment_db."</b>:    ";
      echo $comment_db."--votes:  ".$num_votes;

      //show EDIT/DELETE button only to the commenter
      if($_SESSION['id'] == $usercomment_db){
        echo "<form action=\"editcomment.php\" method=\"POST\">
        <input type=\"submit\" value=\"edit\" name=\"edit\"/>
        <input type=\"hidden\" value=\"".$comment_num."\" name=\"comment_num\" />
        <input type=\"hidden\" value=\"".$comment_db."\" name=\"comment_db\" />
        <input type=\"hidden\" name=\"token\" value=\"".$_SESSION['token']."\">
        </form>";

        echo "<form action=\"deletecomment.php\" method=\"POST\">
        <input type=\"submit\" value=\"delete\" name=\"delete\"/>
        <input type=\"hidden\" value=\"".$comment_num."\" name=\"comment_num\" />
        <input type=\"hidden\" name=\"token\" value=\"".$_SESSION['token']."\">
        </form>";
      }

      echo "<form action=\"up.php\" method=\"POST\">
      <input type=\"submit\" value=\"up\" name=\"up\"/>
      <input type=\"hidden\" value=\"".$comment_num."\" name=\"comment_num\" />
      <input type=\"hidden\" name=\"token\" value=\"".$_SESSION['token']."\">
      </form>";

      echo "<form action=\"down.php\" method=\"POST\">
      <input type=\"submit\" value=\"down\" name=\"down\"/>
      <input type=\"hidden\" value=\"".$comment_num."\" name=\"comment_num\" />
      <input type=\"hidden\" name=\"token\" value=\"".$_SESSION['token']."\">
      </form>";



      echo "</div>";
    }
    $stmt->close();
    ?>
  </div>
  <footer>Copyright &copy; cse330</footer>
</body>
</html>
