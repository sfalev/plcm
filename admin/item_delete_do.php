<?php
require_once "../settings.php";
require_once "../functions.php";
require_once "../auth.php";

//pre_print($_GET);exit;

if (array_key_exists("id",$_GET)) {
	$id = $_GET['id'];
} else {
	header("location:plcs.php");
	exit;
}

if (array_key_exists("plc_id",$_GET)) {
	$plc_id = $_GET['plc_id'];
} else {
	header("location:plcs.php");
	exit;
}

$sql = "DELETE FROM item WHERE id=?";
$res = $dbh->prepare($sql);
$res->bindParam(1, $id, PDO::PARAM_INT);
$res->execute();

header("location:plcs.php?id=$plc_id");

?>
