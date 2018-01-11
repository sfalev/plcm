<?php
require_once "../settings.php";
require_once "../functions.php";
require_once "../auth.php";

//pre_print($_POST);exit;

$sql = "UPDATE param_plcm SET value=:varvalue WHERE name=:varname";

$res = $dbh->prepare($sql);
$res->bindParam(":varvalue", 		$_POST['varvalue'],		PDO::PARAM_STR);
$res->bindParam(":varname", 		$_POST['varname'], 		PDO::PARAM_STR);
$res->execute();

header("location:param_plcm.php");

?>
