<?php
require_once "../settings.php";
require_once "../functions.php";
require_once "../vars.php";
require_once "../auth.php";

include "header.php";

$url = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
$url1 = "check_updates.php?&dashboard_id=".$_GET['id'];
$url = str_replace("dashboard.php",$url1,$url);
$id = (int) $_GET['id'];		//dashboard id

echo "
	<script>
		var tdlast;			// last clicked TD id (to reset selection)
		var dashboard_id = ".$id.";
		var tdcolor = '".$tdcolor."';
		var tdcolor_selected = '".$tdcolor_selected."';
		
		// create WebSocket
		var socket = new WebSocket('ws://localhost:8081');
		var flag_get = false;
				
		// get message
		socket.onmessage = function(event) {
			var incomingMessage = event.data;
			//showMessage(incomingMessage); 
			try { result = JSON.parse(incomingMessage);	}
			catch (e) { result = 'err'; }
			if (result != 'err') {
				for (key in result) {
					if (key.indexOf('bit-') == 0) {
						document.getElementById(key).style.background = result[key];
						showConnectionStatus('CONNECTED');
					}
					if (key.indexOf('messages') == 0) {
						document.getElementById('div_last_messages').innerHTML = result[key] + document.getElementById('div_last_messages').innerHTML;
					}
				}
			}
			//
			//showMessage(result);
			if (flag_get == false) {
				socket.send('".$url."');
				flag_get = true;
			}
		};

		// show connection status
		function showConnectionStatus(message) {
			var status0 = document.getElementById('status0');
			status0.innerHTML = message;
			if (message=='CONNECTED') {
				status0.style.color='#00ff00';
			} else {
				status0.style.color='#ff0000';
			}
		}
		
		// close WebSocket
		socket.onclose = function(event) {
			showConnectionStatus('DISCONNECTED');
		};
		function show_td_id(tdid) {
			document.getElementById('status1').innerHTML = tdid;
		}
		
		// reload page to try to reconnect if disconnected
		var timerId = setInterval(function() {
				var status0 = document.getElementById('status0').innerHTML;
				if (status0 == 'DISCONNECTED') {
					window.location.reload();
				}
			}, 10000);
			var tdlast;		// last clicked TD id (to reset selection)

		// Click on the table cell
		function tdclick(td) {
			var tdcur;
			tdcur = document.getElementById(td);
			if (!tdcur.style.backgroundColor) {
				tdcur.style.background = tdcolor;
			} else {
				tdcur.style.background = '';
			}
		}
		
	</script>
		
";
//pre_print ($_SERVER);
echo "<div style='padding:15px;width:80%;float:left;'>";

$sql = "SELECT * FROM dashboard WHERE id=:id AND active=1";
$res = $dbh->prepare($sql);
$res->bindParam(":id",$_GET['id'],PDO::PARAM_INT);
$res->execute();
$num = $res->rowCount();

if ($num == 1) {
	$dashboard = $res->fetch(PDO::FETCH_ASSOC);

	$sql = "SELECT * FROM dbelement WHERE dashboard_id=:dashboard_id AND active=1 AND row=:row AND col=:col";
	$res_el = $dbh->prepare($sql);
	
	if ($dashboard['rows'] and $dashboard['cols']) {
		echo "<table class='table table-bordered'>\n";
		for ($r = 0; $r < $dashboard['rows']; $r++) {
			
			echo "<tr>\n<td>&bull;</td>";
			for ($c = 1; $c < $dashboard['cols']; $c++) {
				$td_id = "td-".$r."-".$c;
				$td_id1 = "row:&nbsp;".$r."&nbsp;,&nbsp;col:&nbsp;".$c;
				$bit_id = "bit-".$r."-".$c;
				$res_el->bindParam(":dashboard_id",	$dashboard['id'],	PDO::PARAM_INT);
				$res_el->bindParam(":row",			$r,					PDO::PARAM_INT);
				$res_el->bindParam(":col",			$c,					PDO::PARAM_INT);
				$res_el->execute();
				$num_el = $res_el->rowCount();
				$td = "";
				if($num_el) {
					$el = $res_el->fetch(PDO::FETCH_ASSOC);
					$td = "";
					$colspan = "";
					if($el['type'] == 1) {
						$c0 = "color".$el['color0'];
						$c1 = "color".$el['color1'];
						$td = "<button id='".$bit_id."' class='btn btn-color' style='background:".$$c0."'></button><br>".$el['label'];
					}
					if($el['type'] == 2) {
						$td = $el['label'];
					}
					if($el['colspan']) {
						$colspan = "colspan='".$el['colspan']."'";
						$c = $c + $el['colspan'];
					}
				}
				
				echo "<td id='".$td_id."' ".$colspan." onclick=tdclick(\"".$td_id."\") onmouseover=show_td_id(\"".$td_id1."\")><br>".$td."</td>\n";
			}
			echo "<tr>\n";
		}
		echo "</table>\n";
	}

} else {
	header("location:index.php");
	exit;
}
echo "</div>";
echo "<div id='div_last_messages' style='width:20%;float:left;'>Last messages:</div>";

// bottom panel

echo "
<script>
	
	// get selected cells list

	function get_selected_cells() {
		var c,r;
		var td;
		var tdcolor;
		var cell;
		var cells = [];
		
		cols = ".$dashboard['cols'].";
		rows = ".$dashboard['rows'].";

		for(r = 1; r <= rows; r++) {
			for(c = 1; c <= cols; c++) {
				tdstr = 'td-'+r+'-'+c;
				td = document.getElementById(tdstr);
				if(td) {
					tdcolor = td.style.backgroundColor;
					
					if (tdcolor) {
						cell = r+':'+c;
						cells.push(cell);
					}
				}
			}
		}
		return cells;
	}

	// make diagram
		
	function makediagram() {
		var cells = [];
		var str;
		cells = get_selected_cells();
		str = JSON.stringify(cells);
		window.open('makediagram.php?cells='+str);

	}
	
</script>

<div id='status' style='position:fixed;right:0;bottom:0;width:100%;background-color:#000000;color:#cecece;'>
	<div id='status0' style='float:left;width:150px;'>&nbsp;</div>
	<div id='status1' style='float:left;width:150px;'></div>
	<div id='status2' style='float:left;width:50px;'>
		<a href='#' data-toggle='tooltip' title='Make diagram with selected elements.' onclick='makediagram()'>
			<img src='../pics/glyphicons-41-stats.png' border='0'>
		</a>
	</div>
</div> ";
require_once "footer.php";
?>
