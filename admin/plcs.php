<?php
require_once "../settings.php";
require_once "../functions.php";
require_once "../auth.php";

// select PLCs

$sql_plcs = "SELECT * FROM plc";
$res_plcs = $dbh->query($sql_plcs);
$num_plcs = $res_plcs->rowCount();
if (!$num_plcs) {
	// if no one PLC, create new
	header("location:plc_new_do.php");
	exit;
}
$plcs = $res_plcs->fetchAll();

// select PLC

if (array_key_exists("id",$_GET)) {
	$id = $_GET['id'];
} else {
	$sql = "SELECT id FROM plc LIMIT 1";
	$res = $dbh->query($sql);
	$plc = $res->fetch(PDO::FETCH_ASSOC);
	$id = $plc['id'];
}

$sql = "SELECT * FROM plc WHERE id=?";
$res = $dbh->prepare($sql);
$res->bindParam(1, $id, PDO::PARAM_INT);
$res->execute();
$plc = $res->fetch(PDO::FETCH_ASSOC);

// select items for PLC

$sql = "SELECT * FROM item WHERE plc_id=?";
$res = $dbh->prepare($sql);
$res->bindParam(1, $id, PDO::PARAM_INT);
$res->execute();
$items = $res->fetchAll();

// get daveProto from params

$sql_daveProto = "SELECT * FROM param WHERE type='proto'";
$res_daveProto = $dbh->query($sql_daveProto);
$daveProtos = $res_daveProto->fetchAll();
$str_daveProtos = "";

foreach ($daveProtos as $daveProto) {
	$selected = "";
	if ($daveProto['value'] == $plc['daveProto']) $selected = "selected";
	$str_daveProtos = $str_daveProtos."<option ".$selected." value=\"".$daveProto['value']."\">".$daveProto['name']."</option>\n";
}

// get daveSpeed from params

$sql_daveSpeed = "SELECT * FROM param WHERE type='speed'";
$res_daveSpeed = $dbh->query($sql_daveSpeed);
$daveSpeeds = $res_daveSpeed->fetchAll();
$str_daveSpeeds = "";

foreach ($daveSpeeds as $daveSpeed) {
	$selected = "";
	if ($daveSpeed['value'] == $plc['daveSpeed']) $selected = "selected";
	$str_daveSpeeds = $str_daveSpeeds."<option ".$selected." value=\"".$daveSpeed['value']."\">".$daveSpeed['name']."</option>\n";
}

// get areas from params

$sql_daveArea = "SELECT * FROM param WHERE type='area'";
$res_daveArea = $dbh->query($sql_daveArea);
$daveAreas = $res_daveArea->fetchAll();
$str_daveAreas = "";

foreach ($daveAreas as $daveArea) {
	$selected = "";
	if ($daveArea['value'] == $plc['daveArea']) $selected = "selected";
	$str_daveAreas = $str_daveAreas."<option ".$selected." value=\"".$daveArea['value']."\">".$daveArea['name']."</option>\n";
}

include "header.php";
 
?>

<div style="padding:15px;">
	<?php 
		// PLCs menu
		echo "<a href='plc_new_do.php' class='btn btn-primary'>Add new PLC</a>&nbsp;&bull;&nbsp;";
		for ($i = 0; $i < $num_plcs; $i++) {
			$btn_class = "btn-default";
			if ($plcs[$i]['id'] == $id) $btn_class = "btn-success";
			echo "<a href='plcs.php?id=".$plcs[$i]['id']."' class='btn ".$btn_class."'>".$plcs[$i]['name']."</a>&nbsp;";
		}
		
	?>
	
		<form action="plc_do.php" method="POST">
			<table class="table">
				<thead>
				<tr>
					<th>active</th>
					<th>PLC name</th>
					<th>description</th>
					<th>IP address</th>
					<th>daveProto</th>
					<th>daveSpeed</th>
					<th>daveTimeout, &micro;s</th>
					<th>MPI</th>
					<th>rack</th>
					<th>slot</th>
					<th></th>
					<th></th>
				</tr>
				</thead>
				<tbody>
					<td><input id="active" type="checkbox" class="form-control" name="active" <?php if ($plc['active']) echo " checked"; ?>></td>
					<td><input id="name" type="text" class="form-control" name="name" value="<?php echo $plc['name'] ?>"></td>
					<td><textarea id="description" class="form-control" name="description" style="height:35px;"><?php echo $plc['description'] ?></textarea></td>
					<td><input id="ip" type="text" class="form-control" name="ip" placeholder="000.000.000.000" value="<?php echo $plc['ip'] ?>"></td>
					<td>
						<select id="daveProto" class="form-control" name="daveProto">
							<?php echo $str_daveProtos; ?>
						</select>
					</td>
					<td>
						<select id="daveSpeed" class="form-control" name="daveSpeed">
							<?php echo $str_daveSpeeds; ?>
						</select>
					</td>
					<td><input id="daveTimeout" type="number" min="0" class="form-control" name="daveTimeout" value="<?php echo $plc['daveTimeout'] ?>"></td>
					<td><input id="MPI" type="number" min="0" class="form-control" name="MPI" value="<?php echo $plc['MPI'] ?>"></td>
					<td><input id="rack" type="number" min="0" class="form-control" name="rack" value="<?php echo $plc['rack'] ?>"></td>
					<td><input id="slot" type="number" min="0" class="form-control" name="slot" value="<?php echo $plc['slot'] ?>"></td>
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
										<p>Do you want to delete PLC '<?php echo $plc['name'] ?>'?</p>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
										<a href="plc_delete_do.php?id=<?php echo $id; ?>" class="btn btn-danger">Delete</a>
									</div>
								</div>

							</div>
						</div>
					</td>
				</tbody>
			</table>
			<input type="hidden" name="id" value="<?php echo $plc['id'] ?>">
		</form>
	<a href="item_new_do.php?plc_id=<?php echo $id; ?>" class="btn btn-primary">Add new item</a>&nbsp;&bull;&nbsp;
	<hr>
	<b>Items:</b>
		<table class="table">
		<thead>
			<tr>
				<th>active</th>
				<th>name</th>
				<th>description</th>
				<th>area</th>
				<th>DB</th>
				<th>start</th>
				<th>length</th>
				<th>mode</th>
				<th>timer, &micro;s</th>
				<!--<th>write</th>-->
				<th></th>
				<th></th>
			</tr>
			</thead>
			<tbody>
<?php
				for($i = 0; $i < count($items); $i++) {
					
					echo "
					<form action='item_do.php' method='POST'>
					<tr>
						<td><input id='active' type='checkbox' class='form-control' name='active' "; if ($items[$i]['active']) echo " checked"; echo "></td>
						<td><input id='name' type='text' class='form-control' name='name' value='".$items[$i]['name']."'></td>
						<td><textarea id='description' class='form-control' name='description' style='height:35px;'>". $items[$i]['description']."</textarea></td>
						<td>
							<select id='area' class='form-control' name='area'>";
								$str_daveAreas = "";
								for($j = 0; $j < count($daveAreas); $j++) {
									$selected = "";
									if ($items[$i]['area'] == $daveAreas[$j]['value']) $selected = " selected";
									$str_daveAreas = $str_daveAreas."<option ".$selected." value='".$daveAreas[$j]['value']."'>".$daveAreas[$j]['name']."</option>\n";
								}
								echo $str_daveAreas;
					echo "	</select>
						</td>
						<td><input id='DB' type='number' min='0' class='form-control' name='DB' value='".$items[$i]['DB']."'></td>
						<td><input id='start' type='number'  min='0' class='form-control' name='start' value='".$items[$i]['start']."'></td>
						<!--<td><input id='len' type='number' min='8' max='16' step='8' class='form-control' name='len' value='".$items[$i]['len']."'></td>-->
						
						<td>
							<select id='len' class='form-control' name='len'>
								<option value='8' "; if ($items[$i]['len'] == 8) echo "selected"; echo">8 bits</option>
								<option value='16' "; if ($items[$i]['len'] == 16) echo "selected"; echo">16 bits</option>
								<option value='32' "; if ($items[$i]['len'] == 32) echo "selected"; echo">32 bits</option>
							</select>
						</td>
						<td>
							<select id='mode' class='form-control' name='mode'>
								<option value='0' "; if ($items[$i]['mode'] == 0) echo "selected"; echo">by timer</option>
								<option value='1' "; if ($items[$i]['mode'] == 1) echo "selected"; echo">by value change</option>
							</select>
						</td>
						<td><input id='timer' type='number' min='100' class='form-control' name='timer' value='".$items[$i]['timer']."'></td>
						<!--<td><input id='write_mode' type='checkbox' class='form-control' name='write_mode' "; if ($items[$i]['write_mode']) echo " checked"; echo "></td>-->
						<td><input type='submit' class='btn btn-success' value='Save'></th>
						<td>
							<button type='button' class='btn btn-danger' data-toggle='modal' data-target='#askDeleteItem".$items[$i]['id']."'>Delete</button>
							<!-- Modal -->
							<div id='askDeleteItem".$items[$i]['id']."' class='modal fade' role='dialog'>
								<div class='modal-dialog'>

								<!-- Modal content-->
									<div class='modal-content'>
										<div class='modal-header'>
											<button type='button' class='close' data-dismiss='modal'>&times;</button>
											<h4 class='modal-title'>Deleting</h4>
										</div>
										<div class='modal-body'>
											<p>Do you want to delete item '".$items[$i]['name']."' ?</p>
										</div>
										<div class='modal-footer'>
											<button type='button' class='btn btn-default' data-dismiss='modal'>No</button>
											<a href='item_delete_do.php?id=".$items[$i]['id']."&plc_id=".$id."' class='btn btn-danger'>Delete</a>
										</div>
									</div>

								</div>
							</div>
						</td>
					</tr>
					<input type='hidden' name='id' value='".$items[$i]['id']."'>
					<input type='hidden' name='plc_id' value='".$items[$i]['plc_id']."'>
					</form>";

				}
?>

				
			</tbody>
			</table>
</div>

<?php
require_once "footer.php";
?>
