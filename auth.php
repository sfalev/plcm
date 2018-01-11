<?php

// user area authentication

require_once "settings.php";
require_once "functions.php";

$user_id = 1;
$group_id = array(1,1,1);

$page = $_SERVER['PHP_SELF'];

if (array_key_exists("userhash",$_COOKIE)) {
	$userhash = $_COOKIE['userhash'];
	$sql = "SELECT * FROM user WHERE (isgroup IS NULL OR isgroup = 0) AND active=1 AND userhash=:userhash";
	$res = $dbh->prepare($sql);
	$res->bindParam(":userhash",$userhash,PDO::PARAM_STR);
	$res->execute();
	$num_user = $res->rowCount();

	if ($num_user == 1) {
		$user = $res->fetch(PDO::FETCH_ASSOC);
		$user_id = $user['id'];
		$group_id[0] = $user['group_id'];
		$group_id[1] = $user['group_id1'];
		$group_id[2] = $user['group_id2'];
		
		if ((array_search(2,$group_id) === false) and (strpos($page,"/admin/") !== false)) { // not admins to admin area are not allowed 
			header("location:../index.php");
			exit;
		}
	} 
} elseif (strpos($page,"/admin/") !== false){
	header("location:../index.php");
	exit;
}
?>
