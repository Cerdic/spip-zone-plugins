<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function slogan_declarer_tables_interfaces($interfaces){
	$interfaces['table_des_traitements']['SLOGAN_SITE_SPIP'][] = 'typo(%s)';
	return $interfaces;
}

?>
