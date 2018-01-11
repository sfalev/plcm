<?php $page = $_SERVER['PHP_SELF'];?>

<!DOCTYPE html>
<html>
<head>
	<title>PLCM</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="../css/bootstrap.min.css">
	<script src="../js/jquery.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
	<script src="../js/md5.js"></script>
	<script>
	function checkpassword() {
		var hash;
		var login = document.getElementById('login').value;
		var password = document.getElementById('password').value;
		hash = CryptoJS.MD5(login).toString();
		login = CryptoJS.MD5(hash).toString();
		hash = CryptoJS.MD5(password).toString();
		password = CryptoJS.MD5(hash).toString();
		
		// check login & password
		
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function () {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var result = xmlhttp.responseText;
			//alert(result);
			if (result != '0') {
				document.getElementById('warning').style.visibility = 'visible';
				document.getElementById('warning').innerHTML = result;
			} else {
				document.location = 'index.php';
			}
		}
	};
	var url = 'auth_do.php?login=' + login + '&password=' + password;
	xmlhttp.open('GET', url, true);
	xmlhttp.send();

	}
</script>
</head>
<body>
	
<nav class="navbar navbar-inverse navbar-fixed-top">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="index.php"><span class="glyphicon glyphicon-eye-open"></span>&nbsp;<font color="#ffffff">PLCM</font></a>
		</div>
		<ul class="nav navbar-nav navbar-right">
			<li>
				<?php 
					if ($user_id > 1) {
						echo " <a href='#'  data-toggle='modal' data-target='#askLogout'><span class='glyphicon glyphicon-log-in'></span> Logout: ".$user['name'];
					} else {
						echo " <a href='#'  data-toggle='modal' data-target='#askLogin'><span class='glyphicon glyphicon-log-in'></span> Login";
					}
				?></a>
			</li>
		</ul>
	</div>							
</nav> 

<!-- Modal login-->
<div id="askLogin" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal login content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Login</h4>
			</div>
			<div class="modal-body">
				<div id="warning" class="alert alert-danger" style="visibility:hidden;"></div>
				<center>
				<input id="login" type="text" placeholder="login"><br><br>
				<input id="password" type="password" placeholder="password">
				</center>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<a href="#" class="btn btn-success" onclick=checkpassword()>Login</a>
			</div>
		</div>

	</div>
</div>

<!-- Modal logout-->
<div id="askLogout" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal logout content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Logout</h4>
			</div>
			<div class="modal-body">
				<p>Do you want to logout ?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<a href="../logout.php" class="btn btn-danger">Logout</a>
			</div>
		</div>

	</div>
</div>
	
<br><br><br>
