<?php
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function aeres_ieconfig_metas($table){
	$table['aeres']['titre'] = 'Bibliographie AERES';
	$table['aeres']['icone'] = 'images/aeres-16.png';
	$table['aeres']['metas_serialize'] = 'aeres';
	return $table;
}

