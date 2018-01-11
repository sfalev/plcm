<?php
require_once "../settings.php";
require_once "../functions.php";
require_once "../vars.php";
require_once "../auth.php";

//pre_print($_GET);exit;

// if 'td' is empty there is no sense to continue...

if (array_key_exists("td",$_GET)) {
	$td = explode("-",$_GET['td']);
} else exit;
$type = 0;

////////////////////////////////////////////////////////////////////////

// Action = ADD

////////////////////////////////////////////////////////////////////////

if ($_GET['action'] == 'add') {
	if($_GET['element'] == 'element-bit') {
		$type = 1;
	}
	if($_GET['element'] == 'element-label') {
		$type = 2;
	}
	if($_GET['element'] == 'element-clear') {
		$type = 0;
		
		// clear cell
		
		$sql = "UPDATE dbelement SET type=0 where dashboard_id=:dashboard_id and row=:row and col=:col";
		$res = $dbh->prepare($sql);
		$res->bindParam(":dashboard_id",$_GET['dashboard_id'], 	PDO::PARAM_INT);
		$res->bindParam(":row", 		$td[1], 				PDO::PARAM_INT);
		$res->bindParam(":col", 		$td[2], 				PDO::PARAM_INT);
		$res->execute();
		echo "0";
		exit;
	}
	if ($type) {
		if ($td[0] == "td") {
			
			// is this element already exist ?
			
			$sql = "SELECT * FROM dbelement where dashboard_id=:dashboard_id and row=:row and col=:col LIMIT 1";
			$res = $dbh->prepare($sql);
			$res->bindParam(":dashboard_id",$_GET['dashboard_id'], 	PDO::PARAM_INT);
			$res->bindParam(":row", 		$td[1], 				PDO::PARAM_INT);
			$res->bindParam(":col", 		$td[2], 				PDO::PARAM_INT);
			$res->execute();
			$num = $res->rowCount();
			
			// no, it's new!
			
			if ($num == 0) {
				$sql = "INSERT INTO dbelement (dashboard_id,name,description,type,row,col,active,color0,color1) VALUES (:dashboard_id,'bit','',:type,:row,:col,1,1,4)";
				$res = $dbh->prepare($sql);
				$res->bindParam(":dashboard_id",$_GET['dashboard_id'], 	PDO::PARAM_INT);
				$res->bindParam(":type", 		$type, 					PDO::PARAM_INT);
				$res->bindParam(":row", 		$td[1], 				PDO::PARAM_INT);
				$res->bindParam(":col", 		$td[2], 				PDO::PARAM_INT);
				$res->execute();
				echo "0"; 		// new element was successfully added
				exit;	
			} else {
				
				// element exists, is it empty ?
				
				$el = $res->fetch(PDO::FETCH_ASSOC);
				
				// yes, it's empty
				
				if ($el['type'] == 0) {
					$sql = "UPDATE dbelement SET type=:type WHERE dashboard_id=:dashboard_id AND row=:row AND col=:col";
					$res = $dbh->prepare($sql);
					$res->bindParam(":dashboard_id",$_GET['dashboard_id'], 	PDO::PARAM_INT);
					$res->bindParam(":type", 		$type, 					PDO::PARAM_INT);
					$res->bindParam(":row", 		$td[1], 				PDO::PARAM_INT);
					$res->bindParam(":col", 		$td[2], 				PDO::PARAM_INT);
					$res->execute();
					echo "0"; // element successfully updated
					exit;
				} else {
					echo "3";exit;	// element already exists
				}
			}
		} else {
			echo "2";			// element was not TD
			exit;
		}
	
	} else {
		echo "1";				// element was not selected
		exit;
	}
}

////////////////////////////////////////////////////////////////////////

// Action = GET

////////////////////////////////////////////////////////////////////////

if ($_GET['action'] == 'get') {

	// select element for this cell
	
	$sql = "SELECT * FROM dbelement where dashboard_id=:dashboard_id and row=:row and col=:col LIMIT 1";
	$res = $dbh->prepare($sql);
	$res->bindParam(":dashboard_id",$_GET['dashboard_id'], 	PDO::PARAM_INT);
	$res->bindParam(":row", 		$td[1], 				PDO::PARAM_INT);
	$res->bindParam(":col", 		$td[2], 				PDO::PARAM_INT);
	$res->execute();
	$num = $res->rowCount();
	
	if ($num) {
		$el = $res->fetch(PDO::FETCH_ASSOC);
		
		// if element is "bit"
		
		if ($el['type'] == 1) {
			$label_id = "label-".$td[1]."-".$td[2];
			$plc_id = "plc-".$td[1]."-".$td[2];
			$item_id = "item-".$td[1]."-".$td[2];
			$bit_id = "bit-".$td[1]."-".$td[2];
			$bit_color0 = "bit-color0-".$td[1]."-".$td[2];
			$bit_color1 = "bit-color1-".$td[1]."-".$td[2];
			$value_bit_color0 = "value-bit-color0-".$td[1]."-".$td[2];
			$value_bit_color1 = "value-bit-color1-".$td[1]."-".$td[2];
			$alert0 = "alert0-".$td[1]."-".$td[2];
			$alert1 = "alert1-".$td[1]."-".$td[2];
			
			// select PLCs
			
			$sql = "SELECT id,name FROM plc";
			$res_plc = $dbh->prepare($sql);
			$res_plc->execute();
			$num = $res_plc->rowCount();
			
			if ($num) {
				$plcs = $res_plc->fetchAll();
			} else exit;
			
			$str_plcs = "<option></option>";
			
			foreach ($plcs as $plc) {
				$selected = "";
				if ($plc['id'] == $el['plc_id']) $selected = " selected";
				$str_plcs = $str_plcs."<option value='".$plc['id']."' ".$selected.">".$plc['name']."</option>\n";
			}
			
			// select items
			
			$sql = "SELECT id,name FROM item WHERE plc_id=:plc_id";
			$res_item = $dbh->prepare($sql);
			$res_item->bindParam(":plc_id",$el['plc_id'], 	PDO::PARAM_INT);
			$res_item->execute();
			$num = $res_item->rowCount();
			
			if ($num) {
				$items = $res_item->fetchAll();
			} //else exit;
			
			$str_items = "<option></option>";
			
			foreach ($items as $item) {
				$selected = "";
				if ($item['id'] == $el['item_id']) $selected = " selected";
				$str_items = $str_items."<option value='".$item['id']."' ".$selected.">".$item['name']."</option>\n";
			}
			
			// draw properties
			
			$c0 = "color".$el['color0'];
			$c1 = "color".$el['color1'];
			
			echo "<input id='".$label_id."' type='text' value='".$el['label']."'>
					<span class='property-name'>plc:</span>
					<select id='".$plc_id."'  name='selectplc' onchange='get_items(this.id,this.value)'>".$str_plcs."</select>\n<br>
					<span class='property-name'>item:</span>
					<select id='".$item_id."'>".$str_items."</select><br>
					<span class='property-name'>bit:</span>
					<input id='".$bit_id."' type='number' min='0' max='15' style='width:60px;' value='".$el['bit']."'><br>
					<span class='property-name'>bit=0/1:</span>
					<button id='".$bit_color0."' class='btn btn-color' onclick='change_color(this.id)' style='background:".$$c0."'></button>
					<button id='".$bit_color1."' class='btn btn-color' onclick='change_color(this.id)' style='background:".$$c1."'></button>
					<input id='".$value_bit_color0."' type='hidden' value='".$el['color0']."'>
					<input id='".$value_bit_color1."' type='hidden' value='".$el['color1']."'>
					<a href='#' data-toggle='tooltip' title='Click this two buttons to change colors for bit values.'><span class='badge'>?</span></a><br><br>
					<textarea id='".$alert0."' class='form-control' rows='2' placeholder='Alert message if bit=0'>".$el['alert0']."</textarea>
					<textarea id='".$alert1."' class='form-control' rows='2' placeholder='Alert message if bit=1'>".$el['alert1']."</textarea>
					<br><br>
					<center>
					<input type='submit' class='btn btn-success' value='Save' onclick='properties_update(\"".$bit_id."\")'>
					</center>";
			exit;
		}
		
		// if element is "label"
		
		if ($el['type'] == 2) {
			$label_id = "label-".$td[1]."-".$td[2];
			$colspan_id = "colspan-".$td[1]."-".$td[2];
			
			// draw properties
			
			echo "<input id='".$label_id."' type='text' value='".$el['label']."'>
					<span class='property-name'>cols:</span>
					<input id='".$colspan_id."' type='number' min='1' style='width:50px;' value='".$el['colspan']."'><br><br>
					<center>
					<input type='submit' class='btn btn-success' value='Save' onclick='properties_update(\"".$label_id."\")'>
					</center>";
			exit;
		}
	} else {
		echo "";
		exit;
	}
}

////////////////////////////////////////////////////////////////////////

// Action = UPDATE

////////////////////////////////////////////////////////////////////////

if ($_GET['action'] == 'update') {
	
	// initialize all variables
	
	$dashboard_id = 0;
	$description = "";
	$alert = "";
	$plc_id = 0;
	$item_id = 0;
	$bit = 0;
	$color0 = 1;
	$color1 = 4;
	$alert0 = "";
	$alert1 = "";
	$label = "";
	$row = 0;
	$col = 0;
	$rowspan = 0;
	$colspan = 0;
	$active = 1;
	
	// get values from GET
	
	if (array_key_exists("dashboard_id",$_GET)) {$dashboard_id = $_GET['dashboard_id'];} else exit;
	if (array_key_exists("description",$_GET)) 	$description = $_GET['description'];
	if (array_key_exists("plc_id",$_GET)) 		$plc_id = $_GET['plc_id'];
	if (array_key_exists("item_id",$_GET)) 		$item_id = $_GET['item_id'];
	if (array_key_exists("bit",$_GET)) 			$bit = $_GET['bit'];
	if ((array_key_exists("color0",$_GET)) and (!empty($_GET['color0']))) 		$color0 = $_GET['color0'];
	if ((array_key_exists("color1",$_GET)) and (!empty($_GET['color1']))) 		$color1 = $_GET['color1'];
	if (array_key_exists("alert0",$_GET)) 		$alert0 = $_GET['alert0'];
	if (array_key_exists("alert1",$_GET)) 		$alert1 = $_GET['alert1'];
	if (array_key_exists("label",$_GET)) 		$label = $_GET['label'];
	if (array_key_exists("rowspan",$_GET)) 		$rowspan = $_GET['rowspan'];
	if (array_key_exists("colspan",$_GET)) 		$colspan = $_GET['colspan'];
	if (array_key_exists("active",$_GET)) 		$active = $_GET['active'];
	
	//pre_print($_GET);
	//pre_print($plc_id);
	//pre_print($item_id);
	//pre_print($td);
	
	$sql = "UPDATE dbelement SET 	description=:description,
									plc_id=:plc_id,
									item_id=:item_id,
									bit=:bit,
									color0=:color0,
									color1=:color1,
									alert0=:alert0,
									alert1=:alert1,
									label=:label, 
									rowspan=:rowspan,
									colspan=:colspan,
									active=:active 
									WHERE dashboard_id=:dashboard_id and row=:row and col=:col";
	$res = $dbh->prepare($sql);
	$res->bindParam(":description",	$description, 	PDO::PARAM_STR);
	$res->bindParam(":plc_id",		$plc_id, 		PDO::PARAM_INT);
	$res->bindParam(":item_id",		$item_id, 		PDO::PARAM_INT);
	$res->bindParam(":bit",			$bit, 			PDO::PARAM_INT);
	$res->bindParam(":color0",		$color0, 		PDO::PARAM_INT);
	$res->bindParam(":color1",		$color1, 		PDO::PARAM_INT);
	$res->bindParam(":alert0",		$alert0, 		PDO::PARAM_STR);
	$res->bindParam(":alert1",		$alert1, 		PDO::PARAM_STR);
	$res->bindParam(":label",		$label, 		PDO::PARAM_STR);
	$res->bindParam(":rowspan",		$rowspan, 		PDO::PARAM_INT);
	$res->bindParam(":colspan",		$colspan, 		PDO::PARAM_INT);
	$res->bindParam(":active",		$active, 		PDO::PARAM_INT);
	$res->bindParam(":dashboard_id",$dashboard_id, 	PDO::PARAM_INT);
	$res->bindParam(":row", 		$td[1], 		PDO::PARAM_INT);
	$res->bindParam(":col", 		$td[2], 		PDO::PARAM_INT);
	$res->execute();
	//pre_print($res->errorInfo());
	echo "0";
	exit;
}

?>
