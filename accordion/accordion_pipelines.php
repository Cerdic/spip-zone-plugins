<?php

if (!defined('_ECRIRE_INC_VERSION')){
 	 return;
}

function accordion_pre_propre($letexte) {
	$GLOBALS['spip_wheels']['accordeon'] = array(
		'accordeon.yaml'
	);
	static $wheel = null;
	if (!isset($wheel)) {
		$wheel = new TextWheel(
			SPIPTextWheelRuleset::loader($GLOBALS['spip_wheels']['accordeon'])
		);
	}
	return $wheel->text($letexte);
}

function accordion_jqueryui_plugins($plugins){
        $plugins[] = "jquery.ui.accordion";
        return $plugins;
}

function accordion_insert_head($flux) {
	$flux .='<link rel="stylesheet" href="'.find_in_path('css/spip_accordion.css').'">';
	$flux .='<script src="'.find_in_path('javascript/spip_accordion.js').'"></script>';
	return $flux;
}
