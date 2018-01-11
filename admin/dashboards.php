<?php
require_once "../settings.php";
require_once "../functions.php";
require_once "../vars.php";
require_once "../auth.php";

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

// make array of bit colors for JS

$jscolors = "['',";
	
for ($i = 1; $i < 100; $i++) {
	$v = "color".$i;
	if (isset($$v)) {
		$jscolors = $jscolors."'".$$v."',";
	} else break;
}
$jscolors = substr($jscolors,0,-1);
$jscolors = $jscolors."]";

include "header.php";
 
?>

<style>
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
	.property-name {
		color: #ffffff;
		float: left; 
		width: 45px;
	}
	.btn-color {
		height: 20px;
		width: 30px;
		background: #5cb85c;
		border-radius: 5px;
	}
</style>

<script>
	var el;			// selected element
	var tdlast;		// last clicked TD id (to reset selection)
	var mode;		// "mode" flag, (1) - insert mode
	var dashboard_id = <?php echo $id; ?>;
	var tdcolor = "<?php echo $tdcolor; ?>";
	var tdcolor_selected = "<?php echo $tdcolor_selected; ?>";
	
////////////////////////////////////////////////////////////////////////
	
// Click on the table cell

////////////////////////////////////////////////////////////////////////

	function tdclick(td) {
		
		var tdcur;
		if (tdlast) {
			tdlast.style.background = tdcolor;
		}
		tdcur = document.getElementById(td);
		tdcur.style.background = tdcolor_selected;
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
	
////////////////////////////////////////////////////////////////////////	
	
// Update cell properties in the database

////////////////////////////////////////////////////////////////////////
	
	function properties_update(id) {
		
		var str = id;
		var element;
		var url;
		var tdcontent;
		
		if (str.search("label") == 0) element = "label";
		if (str.search("bit") == 0) element = "bit";
		
		if (element == 'label') {
			//get colspan id
			var colspanid = id;
			
			label = document.getElementById(id).value;
			colspanid = colspanid.replace("label","colspan");
			colspan = document.getElementById(colspanid).value;
			url = "dbelements_do.php?dashboard_id=" + dashboard_id + "&action=update&td=" + id + "&label=" + label + "&colspan=" + colspan;
			tdcontent = "<?php echo $el_label ?>" + label;
		}
		
		if (element == 'bit') {
			//get plc,item,bit ids
			
			var plcid = id;
			var itemid,bitid;
			var plc,item,bit;
			
			labelid = plcid.replace("bit","label");
			itemid = plcid.replace("bit","item");
			bitid = plcid.replace("bit","bit");
			color0id = plcid.replace("bit","value-bit-color0");
			color1id = plcid.replace("bit","value-bit-color1");
			alert0id = plcid.replace("bit","alert0");
			alert1id = plcid.replace("bit","alert1");
			plcid = plcid.replace("bit","plc");

			label = document.getElementById(labelid).value;
			plc = document.getElementById(plcid).value;
			item = document.getElementById(itemid).value;
			bit = document.getElementById(bitid).value;
			color0 = document.getElementById(color0id).value;
			color1 = document.getElementById(color1id).value;
			alert0 = document.getElementById(alert0id).value;
			alert1 = document.getElementById(alert1id).value;
			
			url = "dbelements_do.php?dashboard_id=" + dashboard_id + "&action=update&td=" + id + "&label=" + label + "&plc_id=" + plc + "&item_id=" + item + "&bit=" + bit + "&color0=" + color0 + "&color1=" + color1 + "&alert0=" + alert0 + "&alert1=" + alert1;
			tdcontent = "<?php echo $el_bit ?>" + label;
		}
		
		// update element
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function () {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				var result = xmlhttp.responseText;
				//alert(result);
				if (result == 0) {
					var tdid = id;
					tdid = tdid.replace("label","td");
					document.getElementById(tdid).innerHTML = tdcontent;
					dbtable_redraw(dashboard_id,tdid);
				}
			}
		};
		xmlhttp.open("GET", url, true);
		xmlhttp.send();
	}

////////////////////////////////////////////////////////////////////////
	
// Click on the element, make it selected

////////////////////////////////////////////////////////////////////////
	
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
	
	// Draw the table after page loaded ////////////////////////////////
	
	window.onload = function() {
		dbtable_redraw(dashboard_id,el);
	};
	
////////////////////////////////////////////////////////////////////////
	
// Rewrite table with dashboard elements

////////////////////////////////////////////////////////////////////////
	
	function dbtable_redraw (dbid,selected_el) {
		
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function () {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				var result = xmlhttp.responseText;
				
				// redraw table
				
				document.getElementById("tddb").innerHTML = result;
				
				// select table cell
				
				str = selected_el;
				str = str.replace("bit-","td-");
				str = str.replace("label-","td-");
				tdcur = document.getElementById(str);
				tdcur.style.background = "<?php echo $tdcolor_selected; ?>";
				tdlast = tdcur;
			}
		};
		var url = "dbtable_redraw.php?dashboard_id=" + dbid + "&selected=" + selected_el;
		xmlhttp.open("GET", url, true);
		xmlhttp.send();
	}
	
////////////////////////////////////////////////////////////////////////

// Get items

////////////////////////////////////////////////////////////////////////
	
	function get_items (property_id,plc_id) {
		
		str = property_id;
		str = str.replace("plc-","item-");
		selectitem = document.getElementById(str);
		//aaa = selectplc.options[0].value;
		//selectplc.options[2] = new Option("Строка списка 2", "str2");
		//alert(selectplc.options[0].value);
		
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function () {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				var result = xmlhttp.responseText;
				
				// clear items list	
				var len = selectitem.options.length;
				for (i = 0; i < len; i++) {
					selectitem.options[i] = null;
				}
				
				// fill items list with items of current PLC
				items = JSON.parse(result);
				num = items.length;
				for(i = 0; i < num; i++){
					selectitem.options[i] = new Option(items[i]['name'],items[i]['id']);
				}
				//alert(aaa.length);
			}
		};
		var url = "select_do.php?table=item&plc_id=" + plc_id;
		xmlhttp.open("GET", url, true);
		xmlhttp.send();
		
	}
	
////////////////////////////////////////////////////////////////////////

// Change color for bit values

////////////////////////////////////////////////////////////////////////

	function change_color(bit_color_id) {
		
		colors = <?php echo $jscolors; ?>
		
		value_bit_color_id = bit_color_id;
		value_bit_color_id = value_bit_color_id.replace("bit-color","value-bit-color");
		
		var color_number = document.getElementById(value_bit_color_id).value;
		color_number++;
		if (color_number >= colors.length) color_number = 1;
		document.getElementById(bit_color_id).style.background = colors[color_number];
		document.getElementById(value_bit_color_id).value = color_number;
		
		//alert(document.getElementById(value_bit_color_id).value);
	}
	
////////////////////////////////////////////////////////////////////////	

</script>

<div id="left-col" class="navbar navbar-inverse">
	<div id="mode">[INSERT MODE]</div>
	<span class="color-white">elements:
		<a href="#" data-toggle="tooltip" title="Click element to select it and then click table cell to put element into it."><span class="badge">?</span></a>
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
					<th>access</th>
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
					<td>
						<select id="user_id" name="user_id" class="form-control">
							<option></option>
							<optgroup label="groups:">
							<?php
								// access level
								$sql = "SELECT * FROM user ORDER BY isgroup DESC,name ASC";
								$res_user = $dbh->prepare($sql);
								$res_user->execute();
								$num_user = $res_user->rowCount();
								$optgroup_done = 0;
								
								if ($num_user) {
									$users = $res_user->fetchAll();
									foreach ($users as $user) {
										$selected = "";
										$group = "";
										if ($user['id'] == $db['user_id']) $selected = " selected";
										if ($user['isgroup'] == 0 and $optgroup_done == 0) {
											echo "</optgroup>\n<optgroup label='users:'>";
											$optgroup_done = 1;
										}
										echo "<option value='".$user['id']."' ".$selected.">".$user['name']."</option>\n";
									}
								}
								echo "</optgroup>\n";
								
							?>
						</select>
					</td>
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
	</div>
	<br><br><br>
</div>

<?php

require_once "footer.php";
?>
