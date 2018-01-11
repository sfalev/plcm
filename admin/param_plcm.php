<?php
require_once "../settings.php";
require_once "../functions.php";
require_once "../auth.php";

// select PLCM settings

$sql = "SELECT * FROM param_plcm";
$res = $dbh->query($sql);
$num = $res->rowCount();
if (!$num) {
	header("location:plcs.php");
	exit;
}
$params = $res->fetchAll();

include "header.php";
 
?>

<div style="padding:15px; width:800px;">
	<table class="table">
		<thead>
			<tr>
				<th>name</th>
				<th>value</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php 
				foreach ($params as $param) {
					echo "<form action='param_plcm_do.php' method='post'>";
					echo "<td>".$param['name']."</td>
						<td><input type='text' id='varname' class='form-control' name='varvalue' value='".$param['value']."'></td>
						<td>".$param['description']."</td>
						<td><input type='submit' class='btn btn-success' value='Save'></td>";
					echo "<input type='hidden' name='varname' value='".$param['name']."'>";	
					echo "</form>";
				}
			?>
		</tbody>
	</table>
</div>
<?php
require_once "footer.php";
?>
