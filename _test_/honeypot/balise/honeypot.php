<?php

if (!defined("_ECRIRE_INC_VERSION")) return;  

function balise_HONEYPOT ($p) 
{
	return calculer_balise_dynamique($p, 'HONEYPOT', array());
}

function balise_HONEYPOT_stat($args, $filtres) {
   return array();
}
 
function balise_HONEYPOT_dyn() {
	return array('formulaires/honeypot', 7*24*3600, 
		array(
			  'hp' => lire_config('honeypot/hpfile')
		));
}

?>
