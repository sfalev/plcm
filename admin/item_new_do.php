<?php
require_once "../settings.php";
require_once "../functions.php";
require_once "../auth.php";

//pre_print($_GET);exit;

if (array_key_exists("plc_id",$_GET)) {
	$plc_id = $_GET['plc_id'];
} else {
	header("location:plcs.php");
	exit;
}

$sql = "INSERT INTO item (name,plc_id,area,DB,start,len,mode,timer,write_mode,active) VALUES 
							('new_item',:plc_id,3,0,0,1,0,10,0,0)";
$res = $dbh->prepare($sql);
$res->bindParam(":plc_id", $plc_id, PDO::PARAM_INT);
$res->execute();

header("location:plcs.php?id=$plc_id");

?>
