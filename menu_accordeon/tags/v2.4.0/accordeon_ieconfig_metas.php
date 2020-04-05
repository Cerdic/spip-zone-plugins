<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function accordeon_ieconfig_metas($table){
	$table['accordeon']['titre'] = _T('accordeon:titre_menu');
	$table['accordeon']['icone'] = 'prive/themes/spip/images/accordeon-16.png';
	$table['accordeon']['metas_serialize'] = 'accordeon';
	return $table;
}

?>