<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function socialtags_ieconfig_metas($table){
	$table['socialtags']['titre'] = _T('socialtags:titre_menu');
	$table['socialtags']['icone'] = 'prive/themes/spip/images/socialtags-16.png';
	$table['socialtags']['metas_serialize'] = 'socialtags';
	return $table;
}

?>