<?php
require_once "../settings.php";
require_once "../functions.php";
require_once "../vars.php";
require_once "../auth.php";

include "header.php";

$ts_from = 0;
$ts_to = 0;
$num = 0;

$cells_str = $_GET['cells'];
$cells = json_decode($cells_str);
pre_print($cells);

foreach($cells as $c) {
	$cell = explode(":",$c);
	pre_print($cell);
	$sql = "SELECT * FROM dbelement WHERE row=:row AND col=:col AND active=1";
	$res = $dbh->prepare($sql);
	$res->bindParam(":row",$cell[0],PDO::PARAM_INT);
	$res->bindParam(":col",$cell[1],PDO::PARAM_INT);
	$res->execute();
	$num = $res->rowCount();
	if($num) {
		echo "000";
	}
}

if (array_key_exists('ts_from',$_GET)) {
	$ts_from_str = $_GET['ts_from'];
	$ts_from = (int) $ts_from_str;
}
if (array_key_exists('ts_to',$_GET)) {
	$ts_to_str = $_GET['ts_to'];
	$ts_to = (int) $ts_to_str;
}
echo $ts_from."===".$ts_to."<br>";

echo "
<svg viewBox='0 0 1200 400'>

<polyline fill='none' stroke='green' stroke-width='1' 
            points='50,375
                    150,375 150,325 250,325 250,375
                    350,375 350,250 450,250 450,375
                    550,375 550,175 650,175 650,375
                    750,375 750,100 850,100 850,375
                    950,375 950,25 1050,25 1050,375
                    1150,375' />
</svg>
";
echo "

<div id='status' style='position:fixed;right:0;bottom:0;width:100%;background-color:#000000;color:#cecece;'>
	<div id='status0' style='float:left;width:150px;'>&nbsp;</div>
	<div id='status1' style='float:left;width:150px;'></div>
</div> ";
require_once "footer.php";
?>
