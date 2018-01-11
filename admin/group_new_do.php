<?php
require_once "../settings.php";
require_once "../functions.php";
require_once "../auth.php";

$sql = "INSERT INTO user (name,isgroup,active) VALUES ('new_group',1,1)";
$res = $dbh->query($sql);
$id = $dbh->lastInsertId(); 

header("location:users.php?id=$id");

?>
