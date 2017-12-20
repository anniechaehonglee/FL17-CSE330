/*
 * Name: Hakkyung Lee / Annie Chaehong Lee
 * Email: hakkyung@wustl.edu / annie.lee@wustl.edu
 * Assignment: Module 6 - group portion
 * File name: chat-server.js
 */

//requires what needed
var http = require("http");
var socketio = require("socket.io");
var fs = require("fs");

//open client.html
var app = http.createServer(function(req, resp){
	fs.readFile("client.html", function(err, data){
		if(err){
			
			return resp.writeHead(500);
		}
		resp.writeHead(200);
		resp.end(data);
	});
});

//listen to 3456 port
app.listen(3456);

var io = socketio.listen(app);

var users = [];
var blacklist = [];

//default room
var rooms = [{roomName: 'main', roomType: 'main', owner: 'empty'}];

//when connected
io.sockets.on("connection", function(socket){

	//setting username:
	socket.on("username", function(data){
		socket.username = data["username"];
		// socket.join(rooms[0]);
		// socket.room = rooms[0];
		users.push(data["username"]);
		console.log(new Date().toString() + " : " + socket.username + " is connected to the server.");

		//propagates all avaiable rooms at login:
		io.sockets.emit("rooms_to_client", rooms);
	});


	//propagating new room made:
	socket.on("create_room_to_server", function(room){
		
		// set the owner
		var newroom = room;
		newroom.owner = socket.id;
		socket.join(newroom);
		socket.room = newroom;
		rooms.push(newroom);

		console.log(new Date().toString() + " : " + socket.username + " created the room: " + room["roomName"] + "_" + room["roomType"]);

		io.sockets.emit("rooms_to_client", rooms);

		var num = rooms.indexOf(newroom);
		socket.emit("newroom_callback", num);

		io.sockets.emit("rooms_to_client", rooms);
		//console.log(rooms);
	});

	//check if the room is private or not
	socket.on("private_check_to_server", function(num){

		var newroom = rooms[num];
		var isPrivate = false;
		if(newroom["roomType"] != "public"){

			isPrivate = true;
		}

		if(socket.id == newroom.owner){

			isPrivate = false;
			io.sockets.emit("rooms_to_client", rooms);
			socket.emit("private_check_to_client", {isPrivate : isPrivate, pw : newroom.roomType, num : num});
		}
		else{

			io.sockets.emit("rooms_to_client", rooms);
			socket.emit("private_check_to_client", {isPrivate : isPrivate, pw : newroom.roomType, num : num});
		}
	});

	//called when a list of users is needed to be refreshed
	function user_list(num){

		var cur_user = [];
		var newroom = rooms[num];

		if(io.sockets.adapter.rooms[newroom] != undefined){
			
			var all_sockets = io.sockets.sockets;
			for(var this_socketid in all_sockets){
				
				var this_room = io.sockets.adapter.nsp.connected[this_socketid].room
				if(this_room == newroom){
				
					var tempTuple = {username: "", socketid: ""};
					var usr = io.sockets.adapter.nsp.connected[this_socketid].username;
					tempTuple.username = usr;
					tempTuple.socketid = this_socketid;

					cur_user.push(tempTuple);
				}
			}
		}
		return cur_user;
	}

	//check if a user is banned permanently
	function checkBlacklist(banneeId, num){
		//console.log("tuple : " + tempTuple);

		for(var i = 0; i < blacklist.length; i++){

			//if id is found
			if(blacklist[i].banneeId == banneeId){
				
				//&& room is found
				if(blacklist[i].room == rooms[num]){

					return true;
				}
			}
		}

		return false;
	}

	//join a room:
	socket.on("join_room_to_server", function(num){

		var isBlackListed = checkBlacklist(socket.id, num);

		//able to join the room
		if(!isBlackListed){

			var newroom = rooms[num];
			socket.join(newroom);
			socket.room = newroom;

			socket.broadcast.to(newroom).emit('message_to_client', {id:num, username:"", message:socket.username + ' has been connected to this room.'});

			var user = user_list(num);
			io.sockets.in(socket.room).emit('list_of_users', {num : num, users : user});

			console.log(new Date().toString() + " : " + socket.username + " has been connected to room " + newroom.roomName);
			io.sockets.emit("rooms_to_client", rooms);
		}
		//permanently banned case
		else{

			socket.emit('ban_alert_to_client');
			io.sockets.emit("rooms_to_client", rooms);
		}
	});

	//leave a room:
	socket.on("leave_room_to_server", function(num){

		var newroom = rooms[num];
		socket.leave(newroom);
		socket.room = null;

		socket.broadcast.to(newroom).emit('message_to_client', {id:num, username:"", message:socket.username + ' has left this room'});

		var user = user_list(num);
		if(user.length == 0){

			console.log(new Date().toString() + " : " + socket.username + " has left the room " + newroom.roomName);
			rooms.splice(num, 1);
			io.sockets.emit("rooms_to_client", rooms);
		}
		else{

			io.sockets.in(newroom).emit('list_of_users', {num : num, users : user});
			console.log(new Date().toString() + " : " + socket.username + " has left the room " + newroom.roomName);
			io.sockets.emit("rooms_to_client", rooms);
		}
	});


	//propagating messages:
	socket.on("message_to_server", function(msg){
		
		// console.log(rooms.indexOf(socket.room));
		io.sockets.in(socket.room).emit("message_to_client", { id:rooms.indexOf(socket.room), username: socket.username, message: msg });
		console.log(new Date().toString() + " : " + socket.username + " says: " + msg + " (In room " + socket.room.roomName + ")");
	});


	//send secret messages:
	socket.on("secret_msg_to_server", function(data){
		
		var socketid = data["socketid"];
		var msg = data["msg"];
		var user = io.sockets.adapter.nsp.connected[socketid].username;
		socket.broadcast.to(socketid).emit("secret_message_to_client", { id:rooms.indexOf(socket.room), username: socket.username, message: msg });
		socket.emit("secret_message_to_client", { id: rooms.indexOf(socket.room), username: socket.username, message: msg });

		console.log(new Date().toString() + " : (Secret msg to " + user + ") " + socket.username + " says: " + msg + " (In room " + socket.room.roomName + ")");
	});

	//one time ban case
	socket.on("oneTimeBan_to_server", function(data){

		var num = data["num"];
		var myRoom = rooms[num];
		var bannee = data["bannee"];
		var socket_banned = io.sockets.adapter.nsp.connected[bannee];

		//when an owner is trying to ban him/herself
		if((socket.id == bannee) && (socket.id == myRoom.owner)){
			
			socket.emit("oneTimeBan_to_client", {num: 9998});
		}
		//an owner is trying to ban a person
		else if(socket.id == myRoom.owner){
			
			socket_banned.leave(socket.room);
			socket_banned.room = null;
			// socket_banned.join(rooms[0]);

			var msg = " banned " + socket_banned.username + " from this room (one time).";
			io.sockets.in(socket.room).emit("message_to_client", { id: rooms.indexOf(socket.room), username: socket.username, message: msg });

			socket.broadcast.to(bannee).emit("oneTimeBan_to_client", {num: num});

			var user = user_list(num);
			io.sockets.in(myRoom).emit('list_of_users', {num : num, users : user});

			console.log(new Date().toString() + " : " + socket.username + " banned " + socket_banned.username + " from " + myRoom.roomName);
		}
		//when a banner is not an owner
		else{

			socket.emit("oneTimeBan_to_client", {num: 9999});
		}

	});

	//same as above, but permanent case
	socket.on("permanentBan_to_server", function(data){

		var num = data["num"];
		var myRoom = rooms[num];
		var bannee = data["bannee"];
		var socket_banned = io.sockets.adapter.nsp.connected[bannee];

		if((socket.id == bannee) && (socket.id == myRoom.owner)){
			
			socket.emit("permanentBan_to_client", {num: 9998});
		}
		else if(socket.id == myRoom.owner){

			var tempTuple = {banneeId:"", room:""};
			tempTuple.banneeId = bannee;
			tempTuple.room = myRoom;
			blacklist.push(tempTuple);
			//console.log(blacklist);

			socket_banned.leave(socket.room);
			socket_banned.room = null;
			// socket_banned.join(rooms[0]);

			var msg = " banned " + socket_banned.username + " from this room (permanent).";
			io.sockets.in(socket.room).emit("message_to_client", { id: rooms.indexOf(socket.room), username: socket.username, message: msg });

			socket.broadcast.to(bannee).emit("permanentBan_to_client", {num: num});

			var user = user_list(num);
			io.sockets.in(myRoom).emit('list_of_users', {num : num, users : user});

			console.log(new Date().toString() + " : " + socket.username + " permanently banned " + socket_banned.username + " from " + myRoom.roomName);
		}
		else{

			socket.emit("permanentBan_to_client", {num: 9999});
		}
	});


	//shows disconnection:
	socket.on("disconnect", function(){
		
		var newroom = socket.room;
		socket.leave(newroom);
		socket.room = null;
		delete users[socket.username];
		io.sockets.emit("rooms_to_client", rooms);

		io.sockets.in(newroom).emit("message_to_client", {id:rooms.indexOf(newroom), username:"", message:socket.username + " has left this room."});

		var user = user_list(rooms.indexOf(newroom));
		io.sockets.in(newroom).emit('list_of_users', {num :rooms.indexOf(newroom), users : user});

		console.log(new Date().toString() + " : " + socket.username + " is disconnected.");
	});
});
