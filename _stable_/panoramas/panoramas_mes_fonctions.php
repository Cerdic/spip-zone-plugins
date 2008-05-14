<?php

function inclusdans ($texte, $param) {
	
	$param_array = explode(",", $param);
	foreach($param_array as $value) {
		if (intval(trim($value)) == intval(trim($texte)))
			return true;
	}

	
	return false;
}

?>