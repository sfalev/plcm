<?php

// admin area authentication

require_once "../settings.php";
require_once "../functions.php";

$OK = false;
$user_id = 0;

if (array_key_exists("userhash",$_COOKIE)) {
	$userhash = $_COOKIE['userhash'];
	$sql = "SELECT * FROM user WHERE (isgroup IS NULL OR isgroup = 0) AND active=1 AND userhash=:userhash";
	$res = $dbh->prepare($sql);
	$res->bindParam(":userhash",$userhash,PDO::PARAM_STR);
	$res->execute();
	$num_user = $res->rowCount();

	if ($num_user == 1) {
		$user = $res->fetch(PDO::FETCH_ASSOC);
		$user_id = $user['id'];
		if ($user['group_id'] == 2) {			// allow this area to 'admins' group
			$OK = true;
		}		
		
	}
}

if ($OK == false) {
	echo "
	<html>
	<head>
		<title>PLCM</title>
		<meta charset='utf-8'>
		<meta name='viewport' content='width=device-width, initial-scale=1'>
		<link rel='stylesheet' href='../css/bootstrap.min.css'>
		<script src='../js/jquery.min.js'></script>
		<script src='../js/bootstrap.min.js'></script>
		<script src='../js/md5.js'></script>

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
	<center>
	<br><br><br><br><br>
	<div style='width:300px;'>
		<div id='warning' class='alert alert-danger' style='visibility:hidden;'></div>
		<center>login</center>
		<input type='text' class='form-control' id='login' name='login' size='30'><br>
		<center>password</center>
		<input type='password' class='form-control' id='password' name='password' size='30'><br><br>
		<a href='#' class='btn btn-primary' onclick=checkpassword()>OK</a>
	</div>";
exit;
}

?>
