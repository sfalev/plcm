<html>
<head>
	<script>
		// create WebSocket
		var socket = new WebSocket('ws://localhost:8081');
		var flag_get = false;
		
		// get message
		socket.onmessage = function(event) {
		  var incomingMessage = event.data;
		  showMessage(incomingMessage); 
		  if (flag_get == false) {
			  socket.send('===msg===');
			  flag_get = true;
			}
		};

		// show message
		function showMessage(message) {
		  document.getElementById('div1').innerHTML = message;
		}
	</script>
<head>
<body>
	<div id='div1'></div>
</body>
</html>
