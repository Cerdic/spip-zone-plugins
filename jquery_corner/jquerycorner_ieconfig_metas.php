<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function jquerycorner_ieconfig_metas($table){
	$table['jquerycorner']['titre'] = _T('jquerycorner:titre_menu');
	$table['jquerycorner']['icone'] = 'prive/themes/spip/images/jquerycorner-16.png';
	$table['jquerycorner']['metas_serialize'] = 'jquerycorner';
	return $table;
}

?>