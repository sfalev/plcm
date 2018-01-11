<?php

////////////////////////////////////////////////////////////////////////

// Edit this

////////////////////////////////////////////////////////////////////////

//nodave.h path to get defines

$nodave_path = "/usr/local/include/nodave.h";

//DB connection params

$host = "localhost";
$dbname = "plcm";
$user = "plcm";
$pass = "plcm";

////////////////////////////////////////////////////////////////////////

//DB connection

try {  
  $dbh = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);  
}  
catch(PDOException $Exception) {  
	
    //echo $Exception->getMessage();
    
    if ($Exception->getMessage()) {
		echo "Database connection error";
		die();
	}
    
}
?>
