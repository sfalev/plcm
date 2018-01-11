<?php
require_once "../settings.php";
require_once "../functions.php";
require_once "../auth.php";

//pre_print($_POST);exit;
$active = 0;
$write_mode = 0;
if ($_POST['active'] == "on") $active = 1;
if ($_POST['write_mode'] == "on") $write_mode = 1;

$len = $_POST['len'];
if ($len >= 0 and $len <= 8) $len = 8;
if ($len > 8 and $len <= 16) $len = 16;
if ($len > 16) $len = 32;

$id = $_POST['id'];
$plc_id = $_POST['plc_id'];

$sql = "UPDATE item SET active=:active, name=:name, description=:description, area=:area, DB=:DB, start=:start, 
						len=:len, mode=:mode, timer=:timer, write_mode=:write_mode, plc_id=:plc_id  
						WHERE id=:id";
$res = $dbh->prepare($sql);
$res->bindParam(":active", 		$active, 				PDO::PARAM_INT);
$res->bindParam(":name", 		$_POST['name'], 		PDO::PARAM_STR);
$res->bindParam(":description", $_POST['description'], 	PDO::PARAM_STR);
$res->bindParam(":area", 		$_POST['area'], 		PDO::PARAM_INT);
$res->bindParam(":DB", 			$_POST['DB'], 			PDO::PARAM_INT);
$res->bindParam(":start", 		$_POST['start'], 		PDO::PARAM_INT);
$res->bindParam(":len", 		$len,		 			PDO::PARAM_INT);
$res->bindParam(":mode", 		$_POST['mode'], 		PDO::PARAM_INT);
$res->bindParam(":timer", 		$_POST['timer'], 		PDO::PARAM_INT);
$res->bindParam(":write_mode", 	$write_mode, 			PDO::PARAM_INT);
$res->bindParam(":plc_id", 		$_POST['plc_id'], 		PDO::PARAM_INT);
$res->bindParam(":id", 			$id, 					PDO::PARAM_INT);
$res->execute();

header("location:plcs.php?id=$plc_id");

?>
