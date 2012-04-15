<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function forumsectorise_ieconfig_metas($table){
	$table['forumsectorise']['titre'] = _T('forumsectorise:titre_menu');
	$table['forumsectorise']['icone'] = 'prive/themes/spip/images/forumsectorise-16.png';
	$table['forumsectorise']['metas_serialize'] = 'forumsectorise';
	return $table;
}

?>