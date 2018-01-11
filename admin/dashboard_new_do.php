<?php
require_once "../settings.php";
require_once "../functions.php";
require_once "../auth.php";

$sql = "INSERT INTO dashboard (name,description,cols,rows,active)
					VALUES ('new_dashboard','',4,4,0)";
$res = $dbh->query($sql);
$id = $dbh->lastInsertId(); 

header("location:dashboards.php?id=$id");

?>
