<?php
/*
 * Plugin xxx
 * (c) 2009 xxx
 * Distribue sous licence GPL
 *
 */

function action_editer_squelette_dist($file_name = null){
	if (is_null($file_name)){
		$securiser_action = charger_fonction('securiser_action','inc');
		$file_name = $securiser_action();
	}

	$redirect = _request('redirect');
	$erreur = "";
	if (autoriser('modifier','squelette',$file_name)){
	}
}


?>