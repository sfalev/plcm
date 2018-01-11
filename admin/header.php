<?php $page = $_SERVER['PHP_SELF'];?>

<!DOCTYPE html>
<html>
<head>
	<title>PLCM admin area</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="../css/bootstrap.min.css">
	<script src="../js/jquery.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
	<script src="..js/md5.js"></script>
</head>
<body>
	
<nav class="navbar navbar-inverse navbar-fixed-top">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="../index.php"><span class="glyphicon glyphicon-eye-open"></span>&nbsp;<font color="#ffffff">PLCM</font></a>
			<a class="navbar-brand" href="index.php">&nbsp;/ admin </a>
		</div>
		<ul class="nav navbar-nav">
			<li <?php if(strpos($page,"plcs.php")) echo " class=\"active\""; ?>><a href="plcs.php">PLCs</a></li>
			<li <?php if(strpos($page,"dashboards.php")) echo " class=\"active\""; ?>><a href="dashboards.php">Dashboards</a></li>
			<li <?php if(strpos($page,"users.php")) echo " class=\"active\""; ?>><a href="users.php">Users and groups</a></li>
			<li <?php if(strpos($page,"param.php")) echo " class=\"active\""; ?>><a href="param.php">Defines</a></li>
			<li <?php if(strpos($page,"param_plcm.php")) echo " class=\"active\""; ?>><a href="param_plcm.php">Settings</a></li>
		</ul>
		<ul class="nav navbar-nav navbar-right">
			<li><a href="#"  data-toggle="modal" data-target="#askLogout"><span class="glyphicon glyphicon-log-in"></span>
				<?php 
					if ($user_id > 1) {
						echo " Logout: ".$user['name'];
					} else {
						echo " Login";
					}
				?></a>
			</li>
		</ul>
	</div>							
</nav> 
<!-- Modal -->
<div id="askLogout" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
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
