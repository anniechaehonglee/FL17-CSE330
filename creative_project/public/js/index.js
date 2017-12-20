/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module CP
 * File name: index.js
 * Reference: 	for the infinite loop portion, https://codepen.io/mavrK/pen/NRLXJq?q=step+sequencer&limit=all&type=type-pens
				for the sound, https://howlerjs.com/
				for the wave files, https://freesound.org/people/pinkyfinger/packs/4409/
 */

//create music notes to be assigned to each box using HowlerJS
//gs means g#
var note_gs = new Howl({src:['sound/0GS.wav']});
var note_a = new Howl({src:['sound/A.wav']});
var note_as = new Howl({src:['sound/AS.wav']});
var note_b = new Howl({src:['sound/B.wav']});
var note_c = new Howl({src:['sound/C.wav']});
var note_cs = new Howl({src:['sound/CS.wav']});
var note_d = new Howl({src:['sound/D.wav']});
var note_ds = new Howl({src:['sound/DS.wav']});
var note_e = new Howl({src:['sound/E.wav']});
var note_f = new Howl({src:['sound/F.wav']});
var note_fs = new Howl({src:['sound/FS.wav']});
var note_g = new Howl({src:['sound/G.wav']});


function playNote(note){

	switch(note){

		case "0GS":
			note_gs.play();
			break;
		case "A":
			note_a.play();
			break;
		case "AS":
			note_as.play();
			break;
		case "B":
			note_b.play();
			break;
		case "C":
			note_c.play();
			break;
		case "CS":
			note_cs.play();
			break;
		case "D":
			note_d.play();
			break;
		case "DS":
			note_ds.play();
			break;
		case "E":
			note_e.play();
			break;
		case "F":
			note_f.play();
			break;
		case "FS":
			note_fs.play();
			break;
		case "G":
			note_g.play();
			break;
		default:
			break;
	}
}

$("#start").click(function(){
	
	//top left box is 1,1
	var col = 1;
	var row = 1;
	var notes = ["G", "FS", "F", "E", "DS", "D", "CS", "C", "B", "AS", "A", "0GS"];
	interval = setInterval(function() {

		//when the bar reaches the end, start from the beginning
		if(col === 13){
			
			col = 1;
		}

		$(".col").removeClass("playing")
		var colNum = ".col#" + col;
		$(colNum).addClass("playing");
		var rows = $(colNum).children();

		$.each(rows, function(){
			if(row === 13){
				row = 1;
			}

			if($(this).hasClass("on")){
				var note = notes[row-1];
				playNote(note);
			}

			row++;
		});

		col++;

	}, 500); // 500ms/col
});

//stop the loop
$("#stop").click(function(){
	
	$(".col").removeClass("playing");
	clearInterval(interval);
});

//turn on and off the box
$(".note").click(function(){
	
	if($(this).hasClass("on")){
		
		$(this).removeClass("on");
	}
	else{
		
		$(this).addClass("on");
	}
});

//pass the currently selected pattern to server and save in database
function save(){

    var user = $("#id").val();
    var title = $("#title").val();
	var coords = "";
	var cols = $(".col");

	//look for the boxes that are turned on and get their coordinates
	for(var i = 1; i < 13; ++i){
		
		var row = $(cols[i - 1]).children();
		for(var j = 1; j < 13; ++j){
		
			if($(row[j - 1]).hasClass("on")){
		
				coords = coords.concat(i + "," + j + " ");
			}
		}
	}

	$.post("http://ec2-34-235-166-91.compute-1.amazonaws.com:3000/save", {user: user, title: title, coords: coords}, function(data){
		
		if(data == 'successful'){
		
			alert("Successfully saved");
		}
		else{
		
			alert("Failed to save");
		}
	});
}

//fetch data from the database and append on the page
$("#temp").click(function(){
	
	$.post("http://ec2-34-235-166-91.compute-1.amazonaws.com:3000/load", function(data){

		var json_arr = JSON.parse(data);
		
		console.log(json_arr);
		
		var user = "";
		var title = "";
		var coord = "";
		var td = "";
		
		for(var i in json_arr){
			
			user = json_arr[i].user;
			title = json_arr[i].title;
			coord = json_arr[i].coor;
			//console.log(coord);
			td = "<tr class=\"entry\"><td class=\"username\">"+user+"</td><td class=\"title\">"+title+"</td><td class=\"coords\">"+coord+"</td></tr>";
			$("#works").append(td);
			
			$(".entry").click(function(){
			
				var remove = $(".note");
				$.each(remove, function(){
				
					if($(this).hasClass("on")){
						
						$(this).removeClass("on");
					}
				});
				var user = $(this).find(".username").html();
				var temp = $(this).find(".coords").html();
				var coords = temp.split(" ");
				var cols = $(".col");

				coords.forEach(function(coord){

					var temp = coord.split(",");
					var col = temp[0];
					var row = temp[1];

					for(var i = 1; i < 13; ++i){
						if(i == col){
				
							var rows = $(cols[i-1]).children();
							for(var j = 1; j < 13; ++j){
				
								if(j == row){
									
									$(rows[j - 1]).addClass("on");
								}
							}
						}
					}
				});
			});
		}
	});
});

//show the selected pattern on the board
$(".entry").click(function(){
	
	//reset first
	var remove = $(".note");
	$.each(remove, function(){
	
		if($(this).hasClass("on")){
	
			$(this).removeClass("on");
		}
	});

	var user = $(this).find(".username").html();
	var temp = $(this).find(".coords").html();
	var coords = temp.split(" ");
	var cols = $(".col");

	coords.forEach(function(coord){

		var temp = coord.split(",");
		var col = temp[0];
		var row = temp[1];

		for(var i = 1; i < 13; ++i){
			if(i == col){
			
				var rows = $(cols[i - 1]).children();
				for(var j = 1; j < 13; ++j){
					if(j == row){
				
						$(rows[j - 1]).addClass("on");
					}
				}
			}
		}
	});
});

$("#save").click(function(){
	
	$(".modal").fadeIn();
});

$(".close").click(function(){
	
	$(".modal").fadeOut();
});
