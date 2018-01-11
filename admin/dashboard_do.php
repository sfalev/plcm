<?php
require_once "../settings.php";
require_once "../functions.php";
require_once "../auth.php";

//pre_print($_POST);exit;
$active = 0;
if ($_POST['active'] == "on") $active = 1;

$id = $_POST['id'];

$sql = "UPDATE dashboard SET active=:active, name=:name, description=:description, cols=:cols, rows=:rows, user_id=:user_id     
						WHERE id=:id";
$res = $dbh->prepare($sql);
$res->bindParam(":active", 		$active, 				PDO::PARAM_INT);
$res->bindParam(":name", 		$_POST['name'], 		PDO::PARAM_STR);
$res->bindParam(":description", $_POST['description'], 	PDO::PARAM_STR);
$res->bindParam(":cols", 		$_POST['cols'], 		PDO::PARAM_INT);
$res->bindParam(":rows", 		$_POST['rows'], 		PDO::PARAM_INT);
$res->bindParam(":user_id",		$_POST['user_id'], 		PDO::PARAM_INT);
$res->bindParam(":id", 			$id, 					PDO::PARAM_INT);
$res->execute();

header("location:dashboards.php?id=$id");

?>
