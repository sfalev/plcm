<?php
require_once "../settings.php";
require_once "../functions.php";
require_once "../vars.php";
require_once "../auth.php";

//pre_print($_GET);exit;

$sql = "SELECT * FROM dashboard WHERE id=:dashboard_id LIMIT 1";
$res = $dbh->prepare($sql);
$res->bindParam(":dashboard_id",$_GET['dashboard_id'], 	PDO::PARAM_INT);
$res->execute();
$num = $res->rowCount();

if ($num) {
	$db = $res->fetch(PDO::FETCH_ASSOC);
	$cols = $db['cols'];
	$rows = $db['rows'];
} else exit;


$out = "<table class='tddb'>";

for ($r = 0; $r <= $rows; $r++) {
	$out = $out."<tr height='60px'>\n";
	for ($c = 0; $c <= $cols; $c++) {
		$tdid = "td-".$r."-".$c;
		$num = 0;
		
		// select content for TD
		
		$sql = "SELECT * FROM dbelement WHERE dashboard_id=:dashboard_id and row=:row and col=:col and type>0 LIMIT 1";
		$res = $dbh->prepare($sql);
		$res->bindParam(":dashboard_id",	$_GET['dashboard_id'], 	PDO::PARAM_INT);
		$res->bindParam(":row", 			$r, 					PDO::PARAM_INT);
		$res->bindParam(":col", 			$c, 					PDO::PARAM_INT);
		$res->execute();
		$num = $res->rowCount();
		$cell = "";
		$db_el = "";
		if ($num) {
			$db_el = $res->fetch(PDO::FETCH_ASSOC);
			if($db_el['type'] == 1) {
				$cell = $el_bit." ".$db_el['label'];
			}
			if($db_el['type'] == 2) {
				$cell = $el_label." ".$db_el['label'];
			}
		}
		$hdr = "";
		$scripts = "";
		if ($r == 0) $hdr = $c;
		if ($c == 0) $hdr = $r;
		if (!$hdr) $scripts = " onclick='tdclick(id)'";
		if ($r == 0 and $c == 0) {
			$hdr = "";
			$scripts = "";
		}
		$colspan = "";
		if ($db_el['colspan'] > 1) {
			$colspan = " colspan='".$db_el['colspan']."'";
			$c = $c + $db_el['colspan'];
		}
		$out = $out."<td ".$colspan." id='".$tdid."' class='tddb' ".$scripts.">".$hdr.$cell."</td>\n";
	}
	$out = $out."</tr>\n";
}

$out = $out."</table>";
echo $out;
?>
