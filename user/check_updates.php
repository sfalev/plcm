<?php
require_once "../settings.php";
require_once "../functions.php";
require_once "../vars.php";

//pre_print($_GET);exit;
$ask = json_decode($_GET['askj']);
$answer = [];

foreach($ask as $a) {

	$socket_id = $a[0];
	$dashboard_id = (int)$a[1];
	$last_id = $a[2];
	$num = 0;
	
	// if this $dashboard_id exists 
	
	$sql = "SELECT id FROM dashboard WHERE id=".$dashboard_id;
	$res = $dbh->query($sql);
	$num = $res->rowCount();
	
	if ($num) {
		
		// if last_id == 0
		// create view with last records from 'log' table
		
		if ($last_id == 0) {
			$sql = "CREATE OR REPLACE VIEW view_new_db".$dashboard_id." as 
					select max(id) from log where item_id in 
						(select distinct item_id from dbelement where dashboard_id=".$dashboard_id." and item_id is NOT NULL) 
					group by item_id";

			$res_log = $dbh->query($sql);
			$res->execute();
		}

		$sql = "SELECT * FROM log WHERE id IN (SELECT * FROM view_new_db".$dashboard_id.")";

		$res_log = $dbh->query($sql);
		$num_log = $res_log->rowCount();
		
		if($num_log) {
			
			$log = $res_log->fetchAll();
			
			// get last id from log
			// check if max(id) greater then last_id
			// if yes - go further, if not - nothing to do
			
			$num_log--;
			
			if ($log[$num_log]['id'] > $last_id) {
			
				// create array with last answers from table 'log' for each item
				
				$item_ids = "";
				
				foreach ($log as $l) {
					
					$key = $l['item_id'];
					$log_answers[$key] = $l['answer'];
					$log_tss[$key] = date("H:i:s",$l['ts']);
					$item_ids = $item_ids.$key.",";
					$new_last_id = $l['id'];
				}
				
				// make string with last updated items
				
				$item_ids = substr($item_ids,0,-1);

				// make array with data for dashboard
				
				$sql = "select * from dbelement where item_id in (".$item_ids.")";
				$res_dbelement = $dbh->query($sql);
				$dbelements = $res_dbelement->fetchAll();
				$answer[$socket_id]['last_id'] = $new_last_id;

				foreach ($dbelements as $dbelement) {
					
					if ($dbelement['type'] == 1) {
						
						$mask = pow(2,$dbelement['bit']-1);
						$item_id = $dbelement['item_id'];
						$bit_value = $log_answers[$item_id] & $mask;
						if ($bit_value == 0) {
							$c = $dbelement['color0'];
							if($dbelement['alert0']) $msg = $log_tss[$item_id]." - ".$dbelement['alert0']."<br>";
						} else {
							$c = $dbelement['color1'];
							if($dbelement['alert1']) $msg = $log_tss[$item_id]." - ".$dbelement['alert1']."<br>";
						}
						$c1 = "color".$c;
						$color = $$c1;
						$key = "bit-".$dbelement['row']."-".$dbelement['col'];
						$answer[$socket_id][$key] = $color;
						$answer[$socket_id]['messages'] = $msg;
					}
				}
			}
		}
	}
	
}
//pre_print($answer);
$answerj = json_encode($answer);
echo $answerj;
//$answer = json_decode($answerj);
//pre_print($answer);
?>

