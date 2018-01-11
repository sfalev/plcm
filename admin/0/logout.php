<?php
require_once "../settings.php";
require_once "../functions.php";

//pre_print($_GET);exit;

$userhash = $_COOKIE['userhash'];

$sql = "UPDATE user SET userhash='' WHERE userhash=:userhash";
$res = $dbh->prepare($sql);
$res->bindParam(":userhash",$_COOKIE['userhash'], 	PDO::PARAM_STR);
$res->execute();
setcookie("userhash","");

echo "See you !"; 

?>
