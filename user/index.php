<?php
require_once "../settings.php";
require_once "../functions.php";
require_once "../auth.php";

include "header.php";
echo "<div style='padding:15px;'>";
$admins = false;

if (array_search(2,$group_id) !== false) {
	echo "<a href='../admin'>Admin area</a><br><br>";
	$admins = true;
}

echo "Available dashboards:<br>
	<ul>";
		$sql = "SELECT * FROM dashboard WHERE (user_id=:user_id OR user_id=:group_id OR user_id=:group_id1 OR user_id=:group_id2) AND active=1";
		if ($admins == true) {
			$sql = "SELECT * FROM dashboard WHERE active=1";
		}
		$res = $dbh->prepare($sql);
		$res->bindParam(":user_id",		$user_id,		PDO::PARAM_INT);
		$res->bindParam(":group_id",	$group_id[0],	PDO::PARAM_INT);
		$res->bindParam(":group_id1",	$group_id[1],	PDO::PARAM_INT);
		$res->bindParam(":group_id2",	$group_id[2],	PDO::PARAM_INT);
		$res->execute();
		$num = $res->rowCount();
		
		if ($num) {
			$dashboards = $res->fetchAll();
			foreach($dashboards as $dashboard) {
				echo "<li><a href=dashboard.php?id=".$dashboard['id'].">".$dashboard['name']."</a>";
			}
		}

echo "	</ul>
</div>";

require_once "footer.php";
?>
