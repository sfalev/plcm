<?php
require_once "../settings.php";
require_once "../functions.php";

//pre_print($_POST);exit;
$active = 0;
if ($_POST['active'] == "on") $active = 1;

$id = $_POST['id'];

$sql = "UPDATE plc SET active=:active, name=:name, description=:description, ip=:ip, daveProto=:daveProto, daveSpeed=:daveSpeed, 
						daveTimeout=:daveTimeout, MPI=:MPI, rack=:rack, slot=:slot  
						WHERE id=:id";
$res = $dbh->prepare($sql);
$res->bindParam(":active", 		$active, 				PDO::PARAM_INT);
$res->bindParam(":name", 		$_POST['name'], 		PDO::PARAM_STR);
$res->bindParam(":description", $_POST['description'], 	PDO::PARAM_STR);
$res->bindParam(":ip", 			$_POST['ip'], 			PDO::PARAM_STR);
$res->bindParam(":daveProto", 	$_POST['daveProto'], 	PDO::PARAM_INT);
$res->bindParam(":daveSpeed", 	$_POST['daveSpeed'], 	PDO::PARAM_INT);
$res->bindParam(":daveTimeout", $_POST['daveTimeout'], 	PDO::PARAM_INT);
$res->bindParam(":MPI", 		$_POST['MPI'], 			PDO::PARAM_INT);
$res->bindParam(":rack", 		$_POST['rack'], 		PDO::PARAM_INT);
$res->bindParam(":slot", 		$_POST['slot'], 		PDO::PARAM_INT);
$res->bindParam(":id", 			$id, 					PDO::PARAM_INT);
$res->execute();

header("location:plcs.php?id=$id");

?>
