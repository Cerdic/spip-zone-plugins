<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function cfg_lister_licences(){
	include_spip('inc/licence');
	return $GLOBALS['licence_licences'];
}

?>