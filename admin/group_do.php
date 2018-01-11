<?php
require_once "../settings.php";
require_once "../functions.php";
require_once "../auth.php";

//pre_print($_POST);exit;
$active = 0;
if ($_POST['active'] == "on") $active = 1;

$id = $_POST['id'];

$sql = "UPDATE user SET active=:active, name=:name WHERE id=:id";
$res = $dbh->prepare($sql);
$res->bindParam(":active", 		$active, 				PDO::PARAM_INT);
$res->bindParam(":name", 		$_POST['name'], 		PDO::PARAM_STR);
$res->bindParam(":id", 			$id, 					PDO::PARAM_INT);
$res->execute();

header("location:users.php?id=$id");

?>
