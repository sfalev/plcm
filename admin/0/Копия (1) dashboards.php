<?php
require_once "../settings.php";
require_once "../functions.php";

// select dashboards

$sql_dbs = "SELECT * FROM dashboard";
$res_dbs = $dbh->query($sql_dbs);
$num_dbs = $res_dbs->rowCount();
if (!$num_dbs) {
	// if no one dashboard, create new
	header("location:dashboard_new_do.php");
	exit;
}
$dbs = $res_dbs->fetchAll();

// select dashboard

if (array_key_exists("id",$_GET)) {
	$id = $_GET['id'];
} else {
	$sql = "SELECT id FROM dashboard LIMIT 1";
	$res = $dbh->query($sql);
	$db = $res->fetch(PDO::FETCH_ASSOC);
	$id = $db['id'];
}

$sql = "SELECT * FROM dashboard WHERE id=?";
$res = $dbh->prepare($sql);
$res->bindParam(1, $id, PDO::PARAM_INT);
$res->execute();
$db = $res->fetch(PDO::FETCH_ASSOC);

$tdcolor = "#dedede";							// table cell color 
$tdcolor_selected = "#bbbbbb";					// if table cell selected
$el_bit = "<div class='el-bit'></div>";			// element "bit"
$el_label = "<div class='el-label'>T</div>";	// element "label"
$el_bit = "<div class='el-bit'></div>";			// element "bit"
$el_clear = "";	// clear cell

include "header.php";
 
?>
<style>
	.affix {
		top: 60px;
		width: 98.5%;
	}
	.affix + .container-fluid {
		padding-top: 70px;
	}
	.tddb {
		background-color:<?php echo $tdcolor; ?>;
		border: 10px solid #ffffff;
		padding:20px;
		celling:20px;
		text-align: center;
	}
	#left-col {
		float: left;
		position: fixed;
		top: 50px;
		width: 205px;
		height: 100%;
		margin: 10px;
		padding: 10px;
		border: 1px solid black;
		border-radius: 10px;
	}
	#mid-col {
		position: relative;
		left: 215px;
		overflow-y: scroll;
		overflow-x: scroll;
		background: #ffffff;
	}
	#mode {
		width: 100%;
		text-align: center;
		color: #00ff00;
		visibility: hidden;
	}
	#properties {
		//text-align: center;
	}
	.el-bit {
		height: 30px;
		width: 30px;
		background: #5cb85c;
		border-radius: 5px;
		border: 1px solid #4cae4c;
	}
	.el-label {
		height: 30px;
		width: 30px;
		background: #eeeeee;
		border-radius: 5px;
		text-align: center;
		vertical-align: middle;
		font-size: 20px;
		border: 1px solid #4cae4c;
	}
	.color-white {
		color: #ffffff;
	}
</style>

<script>
	var el;			// selected element
	var tdlast;		// last clicked TD id (to reset selection)
	var mode;		// "mode" flag, (1) - insert mode
	
	// Click on the table cell

	function tdclick(td) {
		var tdcur;
		if (tdlast) {
			tdlast.style.background = "<?php echo $tdcolor; ?>";
		}
		tdcur = document.getElementById(td);
		tdcur.style.background = "<?php echo $tdcolor_selected; ?>";
		tdlast = tdcur;
		
		// show properties for selected cell
		
		str = td;
		str = str.replace("td-","");
		str = str.replace("-",":");
		document.getElementById("properties-header").innerHTML = "[" + str + "]" + " properties:";
		
		// get properties from database
		
		var xmlhttp1 = new XMLHttpRequest();
		xmlhttp1.onreadystatechange = function () {
			if (xmlhttp1.readyState == 4 && xmlhttp1.status == 200) {
				var result1 = xmlhttp1.responseText;
				//alert(result1);
				document.getElementById("properties").innerHTML = result1;
			}
		};
		var url = "dbelements_do.php?dashboard_id=<?php echo $id; ?>&action=get&td=" + td;
		xmlhttp1.open("GET", url, true);
		xmlhttp1.send();
		
		// if [INSERT MODE] is on
		
		if (mode == 1) {
			
			// add element to the database

			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function () {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var result = xmlhttp.responseText;
					if (result == 0) {
						if (el == "element-bit") tdcur.innerHTML = "<?php echo $el_bit; ?>";
						if (el == "element-label") tdcur.innerHTML = "<?php echo $el_label; ?>";
						if (el == "element-clear") tdcur.innerHTML = "<?php echo $el_clear; ?>";
					}
				}
			};
			var url = "dbelements_do.php?dashboard_id=<?php echo $id; ?>&action=add&element=" + el + "&td=" + td;
			xmlhttp.open("GET", url, true);
			xmlhttp.send();
		}
	}
	
	// update cell properties in the database
	
	function properties_update(id) {
		
		// get label id
		label = document.getElementById(id).value
		
		//get colspan id
		var colspanid = id;
		colspanid = colspanid.replace("label","colspan");
		colspan = document.getElementById(colspanid).value
		
		// update element
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function () {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				var result = xmlhttp.responseText;
				if (result == 0) {
					var tdid = id;
					tdid = tdid.replace("label","td");
					document.getElementById(tdid).innerHTML = "<?php echo $el_label ?>" + label;
				}
			}
		};
		var url = "dbelements_do.php?dashboard_id=<?php echo $id; ?>&action=update&td=" + id + "&label=" + label + "&colspan=" + colspan;
		xmlhttp.open("GET", url, true);
		xmlhttp.send();
		
	}

	
	// Click on the element, make it selected
	
	function elclick(ii) {
		document.getElementById("element-bit").style.color = "#cececece";
		document.getElementById("element-label").style.color = "#cececece";
		document.getElementById("element-clear").style.color = "#cececece";
		document.getElementById("mode").style.visibility = "hidden";
		mode = 0;
		if (el != ii) {
			yy = document.getElementById(ii);
			yy.style.color = "#00ff00";
			el = ii;
			document.getElementById("mode").style.visibility = "visible";
			mode = 1;
		} else el = "";
	}
</script>

<div id="left-col" class="navbar navbar-inverse">
	<div id="mode">[INSERT MODE]</div>
	<span class="color-white">elements:
		<a href="#" data-toggle="tooltip" title="Click element to select it and then table cell to put element into it."><span class="badge">?</span></a>
	</span>
	<ul class="nav navbar-nav">
		<li><a href="#" id="element-bit" onclick="elclick(id)"><span class="glyphicon glyphicon-unchecked" style="font-size: 20px;"></span><br><center>Bit</center></a></li>
		<li><a href="#" id="element-label" onclick="elclick(id)"><span class="glyphicon glyphicon-text-size" style="font-size: 20px;"></span><br><center>Label</center></a></li>
		<li><a href="#" id="element-clear" onclick="elclick(id)"><span class="glyphicon glyphicon-remove-circle" style="font-size: 20px;"></span><br><center>Clear</center></a></li>
	</ul>
	<br>
	<div id="properties-header" class="color-white">properties:</div>
	<br>
	<div id="properties"></div>
</div>
<div id="mid-col" style="padding:15px;">
	<?php 
	
		// dashboards menu
		
		echo "<a href='dashboard_new_do.php' class='btn btn-primary'>Add new dashboard</a>&nbsp;&bull;&nbsp;";
		for ($i = 0; $i < $num_dbs; $i++) {
			$btn_class = "btn-default";
			if ($dbs[$i]['id'] == $id) $btn_class = "btn-success";
			echo "<a href='dashboards.php?id=".$dbs[$i]['id']."' class='btn ".$btn_class."'>".$dbs[$i]['name']."</a>&nbsp;";
		}
	?>
		<form action="dashboard_do.php" method="POST">
			<table class="table" style="width:900px">
				<thead>
				<tr>
					<th>active</th>
					<th>dashboard name</th>
					<th>description</th>
					<th>columns</th>
					<th>rows</th>
					<th></th>
					<th></th>
				</tr>
				</thead>
				<tbody>
					<td><input id="active" type="checkbox" class="form-control" name="active" <?php if ($db['active']) echo " checked"; ?>></td>
					<td><input id="name" type="text" class="form-control" name="name" value="<?php echo $db['name'] ?>"></td>
					<td><textarea id="description" class="form-control" name="description" style="height:35px;"><?php echo $db['description'] ?></textarea></td>
					<td><input id="cols" type="number" min="0" max="999" class="form-control" name="cols" value="<?php echo $db['cols'] ?>"></td>
					<td><input id="rows" type="number" min="0" max="999" class="form-control" name="rows" value="<?php echo $db['rows'] ?>"></td>
					<td><input type="submit" class="btn btn-success" value="Save"></td>
					<td>
						<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#askDeletePLC">Delete</button>
						
						<!-- Modal -->
						
						<div id="askDeletePLC" class="modal fade" role="dialog">
							<div class="modal-dialog">

							<!-- Modal content-->
							
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title">Deleting</h4>
									</div>
									<div class="modal-body">
										<p>Do you want to delete dashboard '<?php echo $db['name'] ?>'?</p>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
										<a href="dashboard_delete_do.php?id=<?php echo $id; ?>" class="btn btn-danger">Delete</a>
									</div>
								</div>

							</div>
						</div>
					</td>
				</tbody>
			</table>
			<input type="hidden" name="id" value="<?php echo $db['id'] ?>">
		</form>
	<hr>

	<div id="tddb">
		<table class="tddb">
		<?php 
		for ($r = 0; $r <= $db['rows']; $r++) {
			echo "<tr height='60px'>\n";
			for ($c = 0; $c <= $db['cols']; $c++) {
				$tdid = "td-".$r."-".$c;
				$num = 0;
				
				// select content for TD
				
				$sql = "SELECT * FROM dbelement WHERE dashboard_id=:dashboard_id and row=:row and col=:col and type>0 LIMIT 1";
				$res_el = $dbh->prepare($sql);
				$res_el->bindParam(":dashboard_id",$id, 	PDO::PARAM_INT);
				$res_el->bindParam(":row", 		$r, 	PDO::PARAM_INT);
				$res_el->bindParam(":col", 		$c, 	PDO::PARAM_INT);
				$res_el->execute();
				$num = $res_el->rowCount();
				$cell = "";
				$db_el = "";
				if ($num) {
					$db_el = $res_el->fetch(PDO::FETCH_ASSOC);
					if($db_el['type'] == 1) {
						$cell = $el_bit;
					}
					if($db_el['type'] == 2) {
						$cell = $el_label." ".$db_el['label'];
					}
				}
				$hdr = "";
				$scripts = "";
				if ($r == 0) $hdr = $c;
				if ($c == 0) $hdr = $r;
				if (!$hdr) $scripts = "ondrop='drop(id)' ondragover='allowDrop(event)' onclick='tdclick(id)'";
				if ($r == 0 and $c == 0) {
					$hdr = "";
					$scripts = "";
				}
				$colspan = "";
				if ($db_el['colspan'] > 1) {
					$colspan = " colspan='".$db_el['colspan']."'";
					$c = $c + $db_el['colspan'];
				}
				echo "<td ".$colspan." id='".$tdid."' class='tddb' ".$scripts.">".$hdr.$cell."</td>\n";
			}
			echo "</tr>\n";
		}
		?>
		</table>
	</div>
	<br><br><br>
</div>

<?php
require_once "footer.php";
?>
