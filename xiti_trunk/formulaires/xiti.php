<?php

function formulaires_xiti_charger_dist(){
	lire_meta('xiti_config');
	$GLOBALS['xiti_config']=unserialize($GLOBALS['meta']['xiti_config']);
	$id_xiti=$GLOBALS['xiti_config']['id_xiti'];
	$logo_xiti=$GLOBALS['xiti_config']['logo_xiti'];
	$width=$GLOBALS['xiti_config']['width'];
	$height=$GLOBALS['xiti_config']['height'];

	$valeurs = array('id_xiti' => $id_xiti, 'logo' => $logo_xiti);
	if (!autoriser("webmestre")) {
		echo"NON AUTORISER";
        return false;
    }
	return $valeurs;
}

function formulaires_xiti_verifier_dist(){
	$erreurs = array();
	return $erreurs;
}

function formulaires_xiti_traiter_dist(){
	if (_request(logo)!="hit") {
		$width="80";
		$height="15";
	}
	else {
		$width="39";
		$height="25";
	}
	ecrire_meta('xiti_config',serialize(array(
		'id_xiti' => _request(id_xiti),
		'logo_xiti' => _request(logo),
		'width' => $width,
		'height' => $height
	)));

	return array('message_ok'=>'Les modifications ont &eacute;t&eacute; apport&eacute;e dans la base de donn&eacute;e.');
}

?>