<?php

/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module 5 - group portion
 * File name: calendar.php
 */

//cookie
ini_set("session.cookie_httponly", 1);

session_start();

//user agent consistency
$previous_ua = @$_SESSION['useragent'];
$current_ua = $_SERVER['HTTP_USER_AGENT'];

if(isset($_SESSION['useragent']) && $previous_ua !== $current_ua){
  die("Session hijack detected");
}else{
  $_SESSION['useragent'] = $current_ua;
}

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" type="text/css" href="style.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <title>Calendar</title>
</head>
<body>

  <!--login window-->
  <div class="sessionStatus">
    <div class="login">
      LOGIN:
      <input type="text" id="idlogin" placeholder="id"/>
      <input type="password" id="pwlogin" placeholder="pw" />
      <button id="login" type="button">LOGIN</button>
    </div>
    <div class="logout">
      <button id="logout" type="button">LOGOUT</button>
    </div>
    <div class="newuser">
      REGISTER TO ADD EVENTS:
      <input type="text" id="idregister" placeholder="id"/>
      <input type="password" id="pwregister" placeholder="pw" />
      <input type="password" id="pwcheck" placeholder="re-enter pw" />
      <button id="newuser" type="button">REGISTER</button>
    </div>
  </div>

  <!--calendar body-->
  <div class="calWrapper">
    <div id="calTitle">
      <button id="btnPrev" type="button">Prev</button>
      <div id="month">
        MONTH
      </div>
      <button id="btnNext" type="button">Next</button>
    </div>
    <div id="calDates">
    </div>
  </div>

  <!--event window - when pressed on the date-->
  <div class="addEvent" id="myModal">
    <div id="modal-content">
      <span class="close">&times;</span>
      <p>ADD AN EVENT</p>
      <input type="text" id="eventTitle" placeholder="Title of your event" />
      <p>When does your event start?</p>
      <input type="datetime-local" id="startTime" />
      <p>When does your event end?</p>
      <input type="datetime-local" id="endTime" />
      <button id="submitEvent">ADD</button>
    </div>
  </div>

  <!--event window - when modify-->
  <div class="modifyEvent" id="myModal2">
    <div id="modal-content2">
      <span class="close2">&times;</span>
      <p>EVENT INFO:</p>
      <div id="eventInfo"></div>
      <button id="delete">delete</button>
      <button id="modify">modify</button>
    </div>
  </div>

  <script type="text/javascript">

  var loginData;

  //login button
  $("#login").click(function(){

    var id = document.getElementById("idlogin").value;
    var pw = document.getElementById("pwlogin").value;

    //empty input
    if(id === "" || pw === ""){

      alert("Missing inputs");
      return;
    }

    login(id, pw).then(checker);

  });

  function login(id, pw){

    //ajax post request to login.php
    return $.ajax({

      type: "POST",
      url: "login.php",
      data: {id: id, pw: pw},
      success: function(dat){

        loginData = $.parseJSON(JSON.stringify(dat));

        //when successful, change the login panel
        if(loginData.success){
          $(".login").css("z-index", 0);
          $(".login").css({ opacity: 0 });
          $(".newuser").css("z-index", 0);
          $(".newuser").css({ opacity: 0 });
          $(".logout").css("z-index", 10);
          $(".logout").css({ opacity: 1 });

          //empty the input field
          $("#idlogin").val("");
          $("#pwlogin").val("");

          //show corresponding events on the calendar
          eventShow(loginData.usernum);

        }
        alert(loginData.message);
      }

    });
  }

  //send ajax post request to check.php and see if it is logged in.
  function checker(event){

    var xmlHttp = new XMLHttpRequest();
    xmlHttp.open("POST", "check.php", true);
    xmlHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
    xmlHttp.addEventListener("load", function(event){

      var jsonData = JSON.parse(event.target.responseText);
      var usernum = jsonData.usernum;
    }, false);
    xmlHttp.send(null);
  }

  //logout button
  $("#logout").click(function(){

    $.post("logout.php", function(data){

      var returnData = $.parseJSON(JSON.stringify(data));

      alert(returnData.message);

      //changes the panel
      if(returnData.success){
        $(".login").css("z-index", 10);
        $(".login").css({ opacity: 1 });
        $(".newuser").css("z-index", 10);
        $(".newuser").css({ opacity: 1 });
        $(".logout").css("z-index", 0);
        $(".logout").css({ opacity: 0 });
        $("#idlogin").val("");
        $("#pwlogin").val("");

        $(".eventButton").remove();
        $(".eventInfo").remove();
      }
    });
  });

  //new user button
  $("#newuser").click(function(){

    var id = document.getElementById("idregister").value;
    var pw = document.getElementById("pwregister").value;
    var pwcheck = document.getElementById("pwcheck").value;

    //empty input
    if(id === "" || pw === "" || pwcheck === ""){

      alert("Missing inputs");
      return;
    }
    else{

      if(pw != pwcheck){

        alert("Passwords do not match");
        return;
      }
      else{

        //post request to newuser.php
        $.post("newuser.php", {

          id: id,
          pw: pw
        }, function(data){

          var returnData = $.parseJSON(JSON.stringify(data));
          alert(returnData.message);

          //empty the input field
          if(returnData.success){
            $("#idregister").val("");
            $("#pwregister").val("");
            $("#pwcheck").val("");
          }
        });


      }
    }
  });

  function eventShow(usernum){
    // //fetch data from eventFetch.php:
    $.post("eventFetch.php",{
      usernum: usernum,
    },
    function(data){
      var returnData = $.parseJSON(JSON.stringify(data));
      //fetched information:
      var title = returnData.title;
      var startTime = returnData.startTime;
      var endTime = returnData.endTime;
      var eventnum = returnData.eventnum;

      //extract elements needed from HTML:
      var monthYear = document.getElementById("month").textContent.split(" ");
      var dates = document.getElementsByClassName("normal");

      for(var i = 0; i < startTime.length; ++i){

        //from each data string, only extract the date, month, year:
        const regex = /(\d{4})-(\d{2})-(\d{2}) (\d{2}:\d{2}:\d{2})/g;
        var startTimeArr = regex.exec(startTime[i]);
        var endTimeArr = regex.exec(endTime[i]);
        console.log(endTimeArr);
        var y = startTimeArr[1]; //year
        var m = startTimeArr[2]; //month
        var d = startTimeArr[3]; //day
        var t = startTimeArr[4];  //time

        var Months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        //after extracting, append to the calendar as a child node:
        if(Months[m-1] == monthYear[0] && y === monthYear[1]){

          //button:
          var btn = document.createElement("button");
          var t = document.createTextNode("event");
          btn.appendChild(t);
          btn.setAttribute("class", "eventButton");
          document.getElementById(d).appendChild(btn);

          //info:
          var para = document.createElement("p");
          var t = document.createTextNode("title: " + title[i] + "start time: " + startTimeArr[0] + "end time: ");
          para.appendChild(t);
          para.setAttribute("class", "eventInfo");
          btn.appendChild(para);

          //eventnum:
          var p = document.createElement("p");
          var num = document.createTextNode(eventnum[i]);
          p.appendChild(num);
          p.setAttribute("id", "eventnum");
          btn.appendChild(p);

        }

        //pop up modal for viewing, modifying, and deleting when clicked on an event:
        $(".eventButton").click(function(event){
          //on click, make modal appear/disappear:
          var modal = document.getElementById('myModal2');
          var span = document.getElementsByClassName("close2")[0];

          //add info on the modal:
          document.getElementById("eventInfo").textContent = $(this).children(".eventInfo").text();

          var e = eventnum[i];
          // e.attr('id', 'eventnum');
          $(this).append(e);


          //make it appear/disappear:s
          modal.style.display = "block";
          span.onclick = function() {
            modal.style.display = "none";
          }
          event.stopPropagation();
        });


      }

    });
  }

  $("#delete").click(function(){
    var eventnum = $('#eventnum').text();
    console.log(eventnum);
    $.post("deleteevent.php", {eventnum: eventnum}, function(data){
      var returnData = $.parseJSON(JSON.stringify(data));
      if(returnData.success){
        alert("event deleted!");
      }
    });
  });


  </script>

  <script src="calendar.js"></script>

</body>
</html>
