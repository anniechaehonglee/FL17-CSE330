<!DOCTYPE html>
<html>
<head>
          <title>Calculator</title>
  <style type="text/css">
    body{
      padding: 5vw;
      padding-top: 15vh;
      display: flex;
      align-items: center;
      flex-direction: column;
    }
  form{
    margin: auto;
    align-items: center;
  }
  .result{
    margin: auto;
  }
</style>
</head>
<body>
<form method="GET" action="<?php $_PHP_SELF ?>">
  <input type="text" name="var1" placeholder="x">
  <input type="text" name="var2" placeholder="y"><br>
  <input type="radio" name="op" value="add"> +
  <input type="radio" name="op" value="subtract"> -
  <input type="radio" name="op" value="multiply"> x
  <input type="radio" name="op" value="divide"> ÷ <br>
  <input type="submit" value="calculate">
</form>
<?php
$var1 = false;
$var2 = false;
$op = false;
if(isset($_GET["var1"])){
$var1 = $_GET["var1"];
}
if(isset($_GET["var2"])){
$var2 = $_GET["var2"];
}
if(isset($_GET["op"])){
$op = $_GET["op"];
}
  function calculate(){
    $var1 = $_GET["var1"];
    $var2 = $_GET["var2"];
    $op = $_GET["op"];
    $result;
    if($op == "add"){
      $result = $var1 + $var2;
    }
    if($op == "subtract"){
      $result = $var1 - $var2;
    }
    if($op == "multiply"){
      $result = $var1 * $var2;
    }
    if($op == "divide"){
      $result = $var1 / $var2;
    }
    echo "<div class=\"result\">result is ".$result."</div>";
  }

  if( $var1 && $var2 && $op){
    $var1 = $_GET["var1"];
    $var2 = $_GET["var2"];
    $op = $_GET["op"];
    if(ctype_digit($var1) && ctype_digit($var2)){
      calculate();
    }
    else{
      echo "<div class=\"result\">Enter a valid number</div>";
    }
  }
  else{
    echo "<div class=\"result\">One or more inputs are missing.</div>";
  }
?>
</body>
</html>
