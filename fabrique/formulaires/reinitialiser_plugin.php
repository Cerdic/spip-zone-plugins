<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_reinitialiser_plugin_charger_dist(){
	return array();
}

function formulaires_reinitialiser_plugin_traiter_dist(){

	// reinit
	session_set(FABRIQUE_ID, null);
	session_set(FABRIQUE_ID_IMAGES, null);

	$res = array(
		'editable'=>'oui',
		'message_ok' => _T('fabrique:reinitialisation_effectuee'),
	);
	return $res;
}



?>
