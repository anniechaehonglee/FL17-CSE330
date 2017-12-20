/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module CP
 * File name: myExpress.js
 */

const express = require('express');
const app = express();

var path = require('path');
var mysql = require('mysql');
var bodyParser = require('body-parser');

var connection = mysql.createConnection({
	host	: 'localhost',
	user	: 'webuser',
	password: 'abcde',
	database: 'cp'
});

app.use(bodyParser.urlencoded({extended: false}));
app.use(bodyParser.json());

//default page routing
app.get('/', function(req, res){

	//serve the index.pug file
	res.sendFile(path.join(__dirname, 'public/index.pug'));
});

//when a user is trying to save
app.post('/save', function(req, res){
	
	var user = req.body.user;
	var title = req.body.title;
	var coords = req.body.coords;
	
	console.log("user: " + user + " title: " + title + " coords: " + coords);

	connection.connect(function(err){
	
		//insert
		connection.query("INSERT INTO `data` (user, coor, title) VALUES ('" + user + "', '" + coords + "', '" + title + "')", function(err, result, fields){
			
			if(err){
			
				throw err;
			}

			console.log("Data inserted: " + result.affectedRows);
			connection.end();

			//create another connection right away to continue working on it
			connection = mysql.createConnection({

				host	: 'localhost',
				user	: 'webuser',
				password: 'abcde',
				database: 'cp'
			});
		});
	});

	res.end("successful");
});

//when a user is trying to fetch the data from the database
app.post('/load', function(req, res){
	
	var temp_coor = "";
	connection.connect(function(err){
		
		//select
		connection.query("SELECT user, coor, title FROM `data`", function(err, result, fields){
	
			if(err){
	
				throw err;
			}

			console.log(result);
	
			var returnVal = JSON.stringify(result);
	
			//return data in JSON format
			res.end(returnVal);
			
			connection.end();
			connection = mysql.createConnection({
				host	: 'localhost',
				user	: 'webuser',
				password: 'abcde',
				database: 'cp'
			});
		});
	});
});

app.use(express.static(path.join(__dirname, 'public')));
app.listen(3000, () => console.log('Example app listening on port 3000!'));
