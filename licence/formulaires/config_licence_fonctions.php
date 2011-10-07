<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function cfg_config_licence_charger(&$cfg){
	include_spip('inc/licence');
	$cfg->val['_licences'] = $GLOBALS['licence_licences'];
}

?>