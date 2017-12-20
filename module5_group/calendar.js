/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module 5 - group portion
 * File name: calendar.js
 */


//Reference: https://codepen.io/xmark/pen/WQaXdv?limit=all&page=1&q=calendar
//We used the date related functions from the reference page
var Cal = function(divId) {

  //Store div id
  this.divId = divId;

  // Days of week, starting on Sunday
  this.DaysOfWeek = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

  // Months, stating on January
  this.Months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ];

  // Set the current month, year
  var d = new Date();

  this.currMonth = d.getMonth();
  this.currYear = d.getFullYear();
  this.currDay = d.getDate();

};

// Goes to next month
Cal.prototype.nextMonth = function() {
  if ( this.currMonth == 11 ) {
    this.currMonth = 0;
    this.currYear = this.currYear + 1;
  }
  else {
    this.currMonth = this.currMonth + 1;
  }
  this.showcurr();
  clickDates();
};

// Goes to previous month
Cal.prototype.previousMonth = function() {
  if ( this.currMonth == 0 ) {
    this.currMonth = 11;
    this.currYear = this.currYear - 1;
  }
  else {
    this.currMonth = this.currMonth - 1;
  }
  this.showcurr();
  clickDates();
};

// Show current month
Cal.prototype.showcurr = function() {
  this.showMonth(this.currYear, this.currMonth);
};

// Show month (year, month)
Cal.prototype.showMonth = function(y, m) {

  var d = new Date()
  // First day of the week in the selected month
  , firstDayOfMonth = new Date(y, m, 1).getDay()

  // Last day of the selected month
  , lastDateOfMonth =  new Date(y, m+1, 0).getDate()

  // Last day of the previous month
  , lastDayOfLastMonth = m == 0 ? new Date(y-1, 11, 0).getDate() : new Date(y, m, 0).getDate();

  document.getElementById("month").textContent = this.Months[m] + ' ' + y;


  var html = '<table>';
  // Write the header of the days of the week
  html += '<tr>';
  for(var i=0; i < this.DaysOfWeek.length;i++) {
    html += '<td class="days">' + this.DaysOfWeek[i] + '</td>';
  }
  html += '</tr>';

  // Write the days
  var i=1;
  do {

    var dow = new Date(y, m, i).getDay();

    // If Sunday, start new row
    if ( dow == 0 ) {
      html += '<tr>';
    }
    // If not Sunday but first day of the month
    // it will write the last days from the previous month
    else if ( i == 1 ) {
      html += '<tr>';
      var k = lastDayOfLastMonth - firstDayOfMonth+1;
      for(var j=0; j < firstDayOfMonth; j++) {
        html += '<td class="not-current">' + k + '</td>';
        k++;
      }
    }

    // Write the current day in the loop
    var chk = new Date();
    var chkY = chk.getFullYear();
    var chkM = chk.getMonth();
    if (chkY == this.currYear && chkM == this.currMonth && i == this.currDay) {
      html += '<td class="today" id="' + i + '">' + i + '</td>';
    } else {
      html += '<td class="normal" id="' + i + '">' + i + '</td>';
    }
    // If Saturday, closes the row
    if ( dow == 6 ) {
      html += '</tr>';
    }
    // If not Saturday, but last day of the selected month
    // it will write the next few days from the next month
    else if ( i == lastDateOfMonth ) {
      var k=1;
      for(dow; dow < 6; dow++) {
        html += '<td class="not-current">' + k + '</td>';
        k++;
      }
    }

    i++;
  }while(i <= lastDateOfMonth);

  // Closes table
  html += '</table>';

  // Write HTML to the div
  document.getElementById(this.divId).innerHTML = html;

  //events!
  eventShow2();

};

//popup modal for adding events when clicked on a date
function clickDates(){
  var dates = document.getElementsByClassName("normal");
  for(var i = 0; i < dates.length; ++i){
    dates[i].addEventListener("click", eventInfo, false);
  }

  //NOT IMPORTANT: there is a date with 'today' class
  var temp = document.getElementsByClassName("today");
  for(var i = 0; i < temp.length; ++i){
    temp[i].addEventListener("click", eventInfo, false);
  }
}


function eventInfo(){
  //sets minimum date for datetime-local: FIXME
  var date = this.textContent;

  //on click, make modal appear/disappear:
  var modal = document.getElementById('myModal');
  var span = document.getElementsByClassName("close")[0];

  modal.style.display = "block";
  span.onclick = function() {
    modal.style.display = "none";
  }

  //when hit submit pass input values using AJAX to addevents.php:
  var submit = document.getElementById('submitEvent');
  submit.onclick = function() {

    var eventTitle = document.getElementById("eventTitle").value;
    var startTime = document.getElementById("startTime").value;
    var endTime = document.getElementById("endTime").value;

    var dataString = "eventTitle=" + encodeURIComponent(eventTitle) + "&startTime=" + encodeURIComponent(startTime) + "&endTime=" + encodeURIComponent(endTime);

    var xmlHttp = new XMLHttpRequest();

    xmlHttp.open("POST", "addevent.php", true);
    xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xmlHttp.addEventListener("load", function(event){
      var jsonData = JSON.parse(event.target.responseText);
      eventShow1(startTime, eventTitle);
      alert(jsonData.message);

    }, false);
    xmlHttp.send(dataString);
  }
}

// On Load of the window
window.onload = function() {

  // Start calendar
  var c = new Cal("calDates");
  c.showcurr();

  // Bind next and previous button clicks
  document.getElementById('btnNext').onclick = function() {
    c.nextMonth();
  };
  document.getElementById('btnPrev').onclick = function() {
    c.previousMonth();
  };
  clickDates();
}



function eventShow1(startTime, title){
  var startTime = startTime;
  var title = title;
  //extract elements needed from HTML:
  var monthYear = document.getElementById("month").textContent.split(" ");
  var dates = document.getElementsByClassName("normal");

  //from each data string, only extract the date, month, year:
  const regex = /(\d{4})-(\d{2})-(\d{2})T(\d{2}:\d{2})/g;
  var startTimeArr = regex.exec(startTime);
  console.log(startTimeArr);
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
    var t = document.createTextNode("title: " + title + "start time: " + startTimeArr[0] + "end time: ");
    para.appendChild(t);
    para.setAttribute("class", "eventInfo");
    btn.appendChild(para);

  }

  //pop up modal for viewing, modifying, and deleting when clicked on an event:
  $(".eventButton").click(function(event){
    //on click, make modal appear/disappear:
    var modal = document.getElementById('myModal2');
    var span = document.getElementsByClassName("close2")[0];

    //add info on the modal:
    document.getElementById("eventInfo").textContent = $(this).children(".eventInfo").text();

    //make it appear/disappear:s
    modal.style.display = "block";
    span.onclick = function() {
      modal.style.display = "none";
    }
    event.stopPropagation();
  });


}


function eventShow2(){
  var xmlHttp = new XMLHttpRequest();
  xmlHttp.open("POST", "check.php", true);
  xmlHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
  xmlHttp.addEventListener("load", function(event){

    var jsonData = JSON.parse(event.target.responseText);
    var usernum = jsonData.usernum;
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

          //info:
          var para = document.createElement("p");
          var t = document.createTextNode("title: " + title[i] + "start time: " + startTimeArr[0] + "end time: ");
          para.appendChild(t);
          para.setAttribute("class", "eventInfo");
          btn.appendChild(para);

        }

        //pop up modal for viewing, modifying, and deleting when clicked on an event:
        $(".eventButton").click(function(event){
          //on click, make modal appear/disappear:
          var modal = document.getElementById('myModal2');
          var span = document.getElementsByClassName("close2")[0];

          //add info on the modal:
          document.getElementById("eventInfo").textContent = $(this).children(".eventInfo").text();

          //make it appear/disappear:s
          modal.style.display = "block";
          span.onclick = function() {
            modal.style.display = "none";
          }
          event.stopPropagation();
        });



      }

    });
  }, false);
  xmlHttp.send(null);





}
