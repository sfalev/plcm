<?php
require_once "../settings.php";
require_once "../functions.php";

if (array_key_exists("id",$_GET)) {
	$id = $_GET['id'];
} else {
	header("location:index.php");
}

$sql = "SELECT * FROM plc WHERE id=?";
$res = $dbh->prepare($sql);
$res->bindParam(1, $id, PDO::PARAM_INT);
$res->execute();
$plc = $res->fetch(PDO::FETCH_ASSOC);

// get daveProto

$sql_daveProto = "SELECT * FROM param WHERE name like '%daveProto%'";
$res_daveProto = $dbh->query($sql_daveProto);
$daveProtos = $res_daveProto->fetchAll();
$str_daveProtos = "";

foreach ($daveProtos as $daveProto) {
	$selected = "";
	if ($daveProto['value'] == $plc['daveProto']) $selected = "selected";
	$str_daveProtos = $str_daveProtos."<option ".$selected." value=\"".$daveProto['value']."\">".$daveProto['name']."</option>\n";
}

// get daveSpeed

$sql_daveSpeed = "SELECT * FROM param WHERE name like '%daveSpeed%'";
$res_daveSpeed = $dbh->query($sql_daveSpeed);
$daveSpeeds = $res_daveSpeed->fetchAll();
$str_daveSpeeds = "";

foreach ($daveSpeeds as $daveSpeed) {
	$selected = "";
	if ($daveSpeed['value'] == $plc['daveSpeed']) $selected = "selected";
	$str_daveSpeeds = $str_daveSpeeds."<option ".$selected." value=\"".$daveSpeed['value']."\">".$daveSpeed['name']."</option>\n";
}

include "header.php";

?>

<ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#home">PLC edit</a></li>
  <li><a data-toggle="tab" href="#menu1">plc 1</a></li>
  <li><a data-toggle="tab" href="#menu2">plc 2</a></li>
</ul>

<style>
	.input-group-addon {
	width: 300px;
}
	.form-control {
		width: 300px !important;
		}
</style>

<div class="tab-content" style="padding:15px;">
  <div id="home" class="tab-pane fade in active">
    <form action="plc_do.php" method="POST">
		<div class="input-group">
			<span class="input-group-addon">PLC name</span>
			<input id="name" type="text" class="form-control" name="name" value="<?php echo $plc['name'] ?>">
		</div>
		<div class="input-group">
			<span class="input-group-addon">description</span>
			<textarea id="description" class="form-control" name="description"><?php echo $plc['description'] ?></textarea>
		</div>
		<div class="input-group">
			<span class="input-group-addon">IP address</span>
			<input id="ip" type="text" class="form-control" name="ip" placeholder="000.000.000.000" value="<?php echo $plc['ip'] ?>">
		</div>
		<div class="input-group">
			<span class="input-group-addon">daveProto</span>
			<select id="daveProto" class="form-control" name="daveProto">
				<?php echo $str_daveProtos; ?>
			</select>
		</div>
		<div class="input-group">
			<span class="input-group-addon">daveSpeed</span>
			<select id="daveSpeed" class="form-control" name="daveSpeed">
				<?php echo $str_daveSpeeds; ?>
			</select>
		</div>
		<div class="input-group">
			<span class="input-group-addon">daveTimeout</span>
			<input id="daveTimeout" type="number" class="form-control" name="daveTimeout" value="<?php echo $plc['daveTimeout'] ?>">
		</div>
		<div class="input-group">
			<span class="input-group-addon">MPI</span>
			<input id="MPI" type="number" class="form-control" name="MPI" value="<?php echo $plc['MPI'] ?>">
		</div>
		<div class="input-group">
			<span class="input-group-addon">rack</span>
			<input id="rack" type="number" class="form-control" name="rack" value="<?php echo $plc['rack'] ?>">
		</div>
		<div class="input-group">
			<span class="input-group-addon">slot</span>
			<input id="slot" type="number" class="form-control" name="slot" value="<?php echo $plc['slot'] ?>">
		</div>
		<br>
		<input type="hidden" name="id" value="<?php echo $plc['id'] ?>">
		<input type="submit" class="btn" value="Save">
	</form>
  </div>
  <div id="menu1" class="tab-pane fade">
    <h3>plc 1</h3>
    <p>Some content in menu 1.</p>
  </div>
  <div id="menu2" class="tab-pane fade">
    <h3>plc 2</h3>
    <p>Some content in menu 2.</p>
  </div>
</div>

<?php
require_once "footer.php";
?>
