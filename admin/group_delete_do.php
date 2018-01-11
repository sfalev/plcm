<?php
require_once "../settings.php";
require_once "../functions.php";
require_once "../auth.php";

//pre_print($_GET);exit;

if (array_key_exists("id",$_GET)) {
	$id = $_GET['id'];
} else {
	header("location:users.php?id=1");
}

$sql = "DELETE FROM user WHERE id=:id";
$res = $dbh->prepare($sql);
$res->bindParam(":id", $id, PDO::PARAM_INT);
$res->execute();

header("location:users.php?id=1");

?>
