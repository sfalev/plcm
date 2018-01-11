<?php
require_once "../settings.php";
require_once "../functions.php";
require_once "../auth.php";

//pre_print($_GET);exit;

if (array_key_exists("group_id",$_GET)) {
	$group_id = $_GET['group_id'];
} else {
	header("location:users.php?id=1");
	exit;
}

$sql = "INSERT INTO user (name,group_id,active,isgroup) VALUES ('new_user',:group_id,1,0)";
$res = $dbh->prepare($sql);
$res->bindParam(":group_id", $group_id, PDO::PARAM_INT);
$res->execute();

header("location:users.php?id=$group_id");

?>
