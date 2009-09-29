<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_GFC($p) {
    return calculer_balise_dynamique($p, 'GFC', array());
}

function balise_GFC_dyn($param) {
	switch($param){
		case "consumer_id":
			echo $GLOBALS["gfc"]["consumer_id"];
			break;
			
		case "cookie_name":
			echo $GLOBALS["gfc"]["cookie_name"];
			break;
	}
}
 	



?>
