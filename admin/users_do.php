<?php
require_once "../settings.php";
require_once "../functions.php";
require_once "../auth.php";

//pre_print($_POST);exit;
$active = 0;
if ($_POST['active'] == "on") $active = 1;

$id = $_POST['id'];
$group_id = $_POST['group_id'];

$namehash = md5(md5($_POST['name']));
$password = md5(md5($_POST['password']));

$sql = "UPDATE user SET active=:active, name=:name, group_id=:group_id, group_id1=:group_id1, namehash=:namehash, password=:password  
						WHERE id=:id";
$res = $dbh->prepare($sql);
$res->bindParam(":active", 		$active, 				PDO::PARAM_INT);
$res->bindParam(":name", 		$_POST['name'], 		PDO::PARAM_STR);
$res->bindParam(":group_id", 	$group_id,			 	PDO::PARAM_INT);
$res->bindParam(":group_id1", 	$_POST['group_id1'], 	PDO::PARAM_INT);
$res->bindParam(":namehash",	$namehash,			 	PDO::PARAM_STR);
$res->bindParam(":password", 	$password,				PDO::PARAM_STR);
$res->bindParam(":id", 			$id,					PDO::PARAM_INT);
$res->execute();

header("location:users.php?id=$group_id");

?>
