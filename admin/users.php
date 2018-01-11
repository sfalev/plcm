<?php
require_once "../settings.php";
require_once "../functions.php";
require_once "../auth.php";

// select groups

$sql = "SELECT * FROM user WHERE isgroup>0";
$res = $dbh->query($sql);
$num_groups = $res->rowCount();
$groups = $res->fetchAll();

// select group

if (array_key_exists("id",$_GET)) {
	$id = $_GET['id'];
} else {
	$id = 1;
}

$sql = "SELECT * FROM user WHERE id=:id AND isgroup>0";
$res = $dbh->prepare($sql);
$res->bindParam(":id", $id, PDO::PARAM_INT);
$res->execute();
$group = $res->fetch(PDO::FETCH_ASSOC);
if ($res->rowCount() < 1) $id = 1;

// select users for this group

$sql = "SELECT * FROM user WHERE group_id=:id OR group_id1=:id OR group_id2=:id";
if ($id == 1) $sql = "SELECT * FROM user WHERE isgroup IS NULL OR isgroup<1";
$res = $dbh->prepare($sql);
$res->bindParam(":id", $id, PDO::PARAM_INT);
$res->execute();
$num_users = $res->rowCount();
$users = $res->fetchAll();

include "header.php";
 
?>

<div style="padding:15px;">
	<?php 
		// groups menu
		echo "<a href='group_new_do.php' class='btn btn-primary'>Add new group</a>&nbsp;&bull;&nbsp;";
		for ($i = 0; $i < $num_groups; $i++) {
			$btn_class = "btn-default";
			if ($groups[$i]['id'] == $id) $btn_class = "btn-success";
			echo "<a href='users.php?id=".$groups[$i]['id']."' class='btn ".$btn_class."'>".$groups[$i]['name']."</a>&nbsp;";
		}
	?>
		<form action="group_do.php" method="POST">
			<table class="table" style="width:500px;">
				<thead>
				<tr>
					<th>active</th>
					<th>name</th>
					<th></th>
					<th></th>
				</tr>
				</thead>
				<tbody>
					<td><input id="active" type="checkbox" class="form-control" name="active" <?php if ($group['active']) echo " checked"; ?>></td>
					<td><input id="name" type="text" class="form-control" name="name" value="<?php echo $group['name'] ?>"></td>
					<td><input type="submit" class="btn btn-success" value="Save" 
						<?php 
							// disable changing groups "all" and "admins"
							if($id == 1or $id == 2) echo "disabled"; 
						?>>
					</td>
					<td>
						<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#askDeleteGroup" 
						<?php 
							// disable deleting groups "all" and "admins"
							if($id == 1 or $id == 2) echo "disabled"; 
						?>>Delete</button>
						<!-- Modal -->
						<div id="askDeleteGroup" class="modal fade" role="dialog">
							<div class="modal-dialog">

							<!-- Modal content-->
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title">Deleting</h4>
									</div>
									<div class="modal-body">
										<p>Do you want to delete group '<?php echo $group['name'] ?>'?</p>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
										<a href="group_delete_do.php?id=<?php echo $id; ?>" class="btn btn-danger">Delete</a>
									</div>
								</div>

							</div>
						</div>
					</td>
				</tbody>
			</table>
			<input type="hidden" name="id" value="<?php echo $group['id'] ?>">
		</form>
	<a href="user_new_do.php?group_id=<?php echo $id; ?>" class="btn btn-primary">Add new user</a>&nbsp;&bull;&nbsp;
	<hr>
	<b>Users:</b>
		<table class="table">
		<thead>
			<tr>
				<th>active</th>
				<th>name</th>
				<th>password</th>
				<th>group 1</th>
				<th>group 2</th>
				<th></th>
				<th></th>
			</tr>
			</thead>
			<tbody>
<?php
				for($i = 0; $i < $num_users; $i++) {
					
					echo "
					<form action='users_do.php' method='POST'>
					<tr>
						<td><input id='active' type='checkbox' class='form-control' name='active' "; if ($users[$i]['active']) echo " checked"; echo "></td>
						<td><input id='name' type='text' class='form-control' name='name' value='".$users[$i]['name']."'></td>
						<td><input id='password' type='password' class='form-control' name='password'></td>
						<td>
							<select id='group_id' class='form-control' name='group_id'>";
								$str_groups = "";
								for($j = 0; $j < $num_groups; $j++) {
									$selected = "";
									if ($users[$i]['group_id'] == $groups[$j]['id']) $selected = " selected";
									$str_groups = $str_groups."<option ".$selected." value='".$groups[$j]['id']."'>".$groups[$j]['name']."</option>\n";
								}
								echo $str_groups;
					echo "	</select>
						</td>
						<td>
							<select id='group_id1' class='form-control' name='group_id1'>";
								$str_groups = "";
								for($j = 0; $j < $num_groups; $j++) {
									$selected = "";
									if ($users[$i]['group_id1'] == $groups[$j]['id']) $selected = " selected";
									$str_groups = $str_groups."<option ".$selected." value='".$groups[$j]['id']."'>".$groups[$j]['name']."</option>\n";
								}
								echo $str_groups;
					echo "	</select>	
						</td>
						<td><input type='submit' class='btn btn-success' value='Save'></th>
						<td>
							<button type='button' class='btn btn-danger' data-toggle='modal' data-target='#askDeleteUser".$users[$i]['id']."'>Delete</button>
							<!-- Modal -->
							<div id='askDeleteUser".$users[$i]['id']."' class='modal fade' role='dialog'>
								<div class='modal-dialog'>

								<!-- Modal content-->
									<div class='modal-content'>
										<div class='modal-header'>
											<button type='button' class='close' data-dismiss='modal'>&times;</button>
											<h4 class='modal-title'>Deleting</h4>
										</div>
										<div class='modal-body'>
											<p>Do you want to delete item '".$users[$i]['name']."' ?</p>
										</div>
										<div class='modal-footer'>
											<button type='button' class='btn btn-default' data-dismiss='modal'>No</button>
											<a href='user_delete_do.php?id=".$users[$i]['id']."' class='btn btn-danger'>Delete</a>
										</div>
									</div>

								</div>
							</div>
						</td>
					</tr>
					<input type='hidden' name='id' value='".$users[$i]['id']."'>
					</form>";
				}
?>
			</tbody>
			</table>
</div>

<?php
require_once "footer.php";
?>
