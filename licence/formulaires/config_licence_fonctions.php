<?php

function cfg_config_licence_charger(&$cfg){
	include_spip('inc/licence');
	$cfg->val['_licences'] = $GLOBALS['licence_licences'];
}

?>