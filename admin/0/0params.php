<?php
require_once "../settings.php";
require_once "../functions.php";
require_once "header.php";

//read defines from <nodave.h>

$handle = fopen($nodave_path, "r");
				
$sql_clear = "DELETE FROM param";
$res_clear = $dbh->query($sql_clear);
$sql_clear = "ALTER TABLE param AUTO_INCREMENT=0";
$res_clear = $dbh->query($sql_clear);
			
$sql_param = "INSERT INTO param (type,name,value,description) VALUES (:type,:name,:value,:description)";
$res_param = $dbh->prepare($sql_param);
$res_param->bindParam(":type", $type, PDO::PARAM_STR);
$res_param->bindParam(":name", $name, PDO::PARAM_STR);
$res_param->bindParam(":value", $valuei, PDO::PARAM_INT);
$res_param->bindParam(":description", $description, PDO::PARAM_STR);

echo "<div style=\"padding:15px;\">";
echo "<table class=\"table\">";

if ($handle) {
    while (($buffer = fgets($handle)) !== false) {
        $param = preg_split("/[\s]+/",$buffer);
        $pos = strpos($buffer,$param[2]) + strlen($param[2]);
        $name = trim($param[1]);
        $value = trim($param[2]);
        $h = "";
        $valuei = 0;
        if (stripos($value,"0x") === FALSE) {
			$valuei = intval($value);
		} else {
			$valuei = intval($value,16);
		}
        $description = trim(substr($buffer, $pos));
        //daveProto-s import
        if (stripos($name,"aveproto")) {
			$type = "proto";
			echo "<tr class=\"success\"><td>".$type."</td><td>".$name."</td><td>".$value."</td><td>".$comment."</td></tr>";
			$res_param->execute();
		}
		//daveSpeed-s import
        if (stripos($name,"avespeed")) {
			$type = "speed";
			echo "<tr class=\"warning\"><td>".$type."</td><td>".$name."</td><td>".$value."</td><td>".$comment."</td></tr>";
			$res_param->execute();
		}
		//dave areas import
		$areas = "areas daveSysInfo daveSysFlags daveAnaIn daveAnaOut daveP daveInputs daveOutputs daveFlags daveDB daveDI daveLocal daveV daveCounter daveTimer daveCounter200 daveTimer200 daveSysDataS5 daveRawMemoryS5 ";
		$name1 = " ".$name." ";
		if (stripos($areas,$name1)) {
			$type = "area";
			echo "<tr class=\"info\"><td>".$type."</td><td>".$name."</td><td>".$value."</td><td>".$comment."</td></tr>";
			$res_param->execute();
		}
    }
} else {
	echo "Please, check path to <nodave.h> in <settings.php> file";
	die();
}

fclose($handle);
?>

</div>
</table>

<?php
require_once "footer.php";
?>
