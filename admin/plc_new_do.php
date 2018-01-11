<?php
require_once "../settings.php";
require_once "../functions.php";
require_once "../auth.php";

$sql = "INSERT INTO plc (name,daveProto,daveSpeed,daveTimeout,MPI,rack,slot,active)
					VALUES ('new_plc',122,2,5000000,2,0,2,0)";
$res = $dbh->query($sql);
$id = $dbh->lastInsertId(); 

header("location:plcs.php?id=$id");

?>
