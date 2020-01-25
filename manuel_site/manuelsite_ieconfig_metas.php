<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function manuelsite_ieconfig_metas($table){
	$table['manuelsite']['titre'] = _T('manuelsite:titre_menu');
	$table['manuelsite']['icone'] = 'prive/themes/spip/images/manuelsite-16.png';
	$table['manuelsite']['metas_serialize'] = 'manuelsite';
	return $table;
}

?>