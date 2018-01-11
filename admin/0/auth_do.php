<?php
require_once "../settings.php";
require_once "../functions.php";

//pre_print($_GET);exit;

$sql = "SELECT * FROM user WHERE namehash=:namehash AND password=:password AND active=1";
$res = $dbh->prepare($sql);
$res->bindParam(":namehash", 	$_GET['login'], 	PDO::PARAM_STR);
$res->bindParam(":password", 	$_GET['password'], 	PDO::PARAM_STR);
$res->execute();
$num = $res->rowCount();

if ($num == 1) {
	$user = $res->fetch(PDO::FETCH_ASSOC);
	$userhash = myhash(32);
	$sql = "UPDATE user SET userhash=:userhash WHERE id=:user_id";
	$res = $dbh->prepare($sql);
	$res->bindParam(":userhash",$userhash, 	PDO::PARAM_STR);
	$res->bindParam(":user_id", $user['id'],PDO::PARAM_STR);
	$res->execute();
	setcookie("userhash",$userhash);
	echo "0";
} else {
	echo "Invalid login or password";
	exit;
}

?>
