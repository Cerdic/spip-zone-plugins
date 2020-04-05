<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function gestionml_ieconfig_metas($table){
	$table['gestionml']['titre'] = _T('gestionml:titre_menu_configurer');
	$table['gestionml']['icone'] = 'images/gestionml-16.png';
	$table['gestionml']['metas_serialize'] = 'gestionml';
	return $table;
}

?>