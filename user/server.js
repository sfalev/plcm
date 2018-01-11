const Static = require('node-static');
const WebSocket = require('ws');
const XMLHttpRequest = require("xmlhttprequest").XMLHttpRequest;
const URLSearchParams = require("urlsearchparams").URLSearchParams;

var wss = new WebSocket.Server({ port: 8081 });

var clients = [];			// sockets array
var dashboards = [];		// array of dashboard ids	
var last_ids = [];			// last id from table 'log' for which data was already sent to dashboard
var urls = [];					

var url = "";
var key;
var key1;

var result;
var resultj;

// work with socket
 
wss.on('connection', function connection(ws) {
	
	id = "sock_" + Math.round(Math.random() * 10000);	// generate socket id
	clients[id] = ws;
	console.log('socket created ' + id);
	
	ws.on('message', function incoming(message) {
		if (message.indexOf("http://") > -1) {
			url = message;
			urlsearch = new URLSearchParams(message);
			
			// cut all after script name
			url = url.substring(0,url.indexOf("?&dashboard_id"));

			dashboards[id] = urlsearch.get('dashboard_id');
			last_ids[id] = 0;
		}
	});
	
	ws.on('close', function() {
		console.log('socket closed ' + id);
		delete clients[id];
		delete urls[id];
		delete last_ids[id];
	});
	
	ws.on('error', function(error) {
		console.log('error ' + error.message);
	});
	

	
});

var timer1 = setInterval(function() {
	var xmlhttp;
	var ask = [];

	
	now = new Date();
	cur_time = now.getHours() + ":" + now.getMinutes() + ":" + now.getSeconds();
	
	// make array wich information we want to get
	i = 0;
	for (var key in clients) {
		ask[i] = [];
		ask[i][0] = key;
		ask[i][1] = dashboards[key];
		ask[i][2] = last_ids[key];
		i++;
	}
	var askj = JSON.stringify(ask);
	
	// get last updates from database
				
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			resultj = xmlhttp.responseText;
			if (resultj != "0") {
				result = JSON.parse(resultj);
			}
		}
	};
	
	url1 = url + "?&askj=" + askj;
	xmlhttp.open("GET", url1, true);
	xmlhttp.send();
	
	// send data to dashboards
	
	for (key in clients) {
		if (clients[key].readyState === WebSocket.OPEN) {
			tosend = cur_time + "  " + dashboards[key];
			if (result) {
				if(key in result) {
					last_ids[key] = result[key]['last_id'];
					resultkeyj = JSON.stringify(result[key]);
					tosend = JSON.stringify(result[key]);
				}
			} 
			clients[key].send(tosend);
		}
	}

}, 1000);
