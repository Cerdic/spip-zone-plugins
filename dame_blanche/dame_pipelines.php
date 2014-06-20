<?php

function dame_pre_propre($letexte) {
	$GLOBALS['spip_wheels']['dame'] = array(
		'dame.yaml'
	);
	static $wheel = null;
	if (!isset($wheel)) {
		$wheel = new TextWheel(
			SPIPTextWheelRuleset::loader($GLOBALS['spip_wheels']['dame'])
		);
	}
	return $wheel->text($letexte);
}


function dame_inserer_entete($flux) {
	return $flux . "<link rel='stylesheet' href='".find_in_path("dame.css")."' type='text/css' />";
}