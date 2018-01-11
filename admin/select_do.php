<?php
require_once "../settings.php";
require_once "../functions.php";
require_once "../auth.php";

//pre_print($_GET);exit;

////////////////////////////////////////////////////////////////////////

// Get information from the database and put it into JSON

////////////////////////////////////////////////////////////////////////

if ($_GET['table'] == 'item') {
	
	$sql = "SELECT id,name FROM item WHERE plc_id=:plc_id";
	$res = $dbh->prepare($sql);
	$res->bindParam(":plc_id",$_GET['plc_id'], 	PDO::PARAM_INT);
	$res->execute();
	$num = $res->rowCount();
	
	if ($num) {
		for($i = 0; $i < $num; $i++) {
			$item = $res->fetch(PDO::FETCH_ASSOC);
			$items[$i] = $item;
		}
	$items_json = json_encode($items);
	echo $items_json;
	}
	exit;
}
?>
