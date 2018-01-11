<?php
function pre_print($var) {
	echo "<pre>";
	print_r ($var);
	echo "</pre>";
}

function myhash($len) {
	$sym = "1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
	$symlen = strlen($sym);
	$str = "";
	for ($i = 0; $i < $len; $i++) {
		$ii = rand(0,$symlen-1);
		$str = $str.$sym[$ii];
	}
	return $str;
}

function is_cookie() {
	//echo $_COOKIE['minobr'];
	if ($_COOKIE['minobr'] <> 'minobr') {
		$message = "We use cookies, turn them on in your browser, please.";
	}
	return true;	
}
?>
