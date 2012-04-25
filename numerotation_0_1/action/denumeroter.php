<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_denumeroter_dist() {
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$arg = explode('-',$arg);
	$type = 'rubrique';
	if (preg_match(',^\w*$,',$arg[0]))
		$type = $arg[0];
	
	include_spip('inc/numeroter');
	numero_numeroter_rubrique(intval($arg[1]),$type,false);

}

?>