<?php
require_once "../settings.php";
require_once "../functions.php";
require_once "../auth.php";

//pre_print($_GET);exit;

if (array_key_exists("id",$_GET)) {
	$id = $_GET['id'];
} else {
	header("location:users.php?id=1");
	exit;
}

$sql = "SELECT group_id FROM user WHERE id=:id LIMIT 1";
$res = $dbh->prepare($sql);
$res->bindParam(":id", $id, PDO::PARAM_INT);
$res->execute();
$group = $res->fetch(PDO::FETCH_ASSOC);
$group_id = $group['group_id'];

$sql = "DELETE FROM user WHERE id=:id";
$res = $dbh->prepare($sql);
$res->bindParam(":id", $id, PDO::PARAM_INT);
$res->execute();

header("location:users.php?id=$group_id");

?>
