/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module 5 - group portion
 * File name: eventShow.js
 */

//+++++++++++++++++++++++++++++++Show Event++++++++++++++++++++++++++++++++++++
var usernum = returnData.usernum;
// //fetch data from eventFetch.php:
$.post("eventFetch.php",{
  usernum: usernum,
}, function(data){
  var returnData = $.parseJSON(JSON.stringify(data));
  //fetched information:
  var title = returnData.title;
  var startTime = returnData.startTime;
  var endTime = returnData.endTime;
  var eventnum = returnData.eventnum;

  //extract elements needed from HTML:
  var monthYear = document.getElementById("month").textContent.split(" ");
  var dates = document.getElementsByClassName("normal");

  //parse dates into an array:
  var time = [];
  var date = [];
  var month = [];
  var year = [];

  for(var i = 0; i < startTime.length; ++i){

    //from each data string, only extract the date, month, year:
    const regex = /(\d{4})-(\d{2})-(\d{2}) (\d{2}:\d{2}:\d{2})/g;
    var startTimeArr = regex.exec(startTime[i]);

    var y = startTimeArr[1]; //year
    var m = startTimeArr[2]; //month
    var d = startTimeArr[3]; //day
    var t = startTimeArr[4];  //time

    //push to the array for later use:
    year.push(y);
    month.push(m);
    date.push(d);
    time.push(t);

    var Months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    //after extracting, append to the calendar as a child node:
    if(Months[m-1] == monthYear[0] && y === monthYear[1]){

      //button:
      var btn = document.createElement("button");
      var t = document.createTextNode("event");
      btn.appendChild(t);
      btn.setAttribute("class", "eventButton");
      document.getElementById(d).appendChild(btn);

      //eventInfo:
      var para = document.createElement("p");
      var text = document.createTextNode(title[i]);
      para.appendChild(text);
      document.getElementById(d).appendChild(text);
    }

    //pop up modal for viewing, modifying, and deleting when clicked on an event:
    $(".eventButton").click(function(event){
      //on click, make modal appear/disappear:
      var modal = document.getElementById('myModal2');
      var span = document.getElementsByClassName("close2")[0];
      //add info on the modal:
      document.getElementById("eventInfo").textContent = " " + startTimeArr[0];
      //make it appear/disappear:s
      modal.style.display = "block";
      span.onclick = function() {
        modal.style.display = "none";
      }
      event.stopPropagation();
    });
  }
  //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
