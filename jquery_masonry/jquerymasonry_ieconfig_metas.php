<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function jquerymasonry_ieconfig_metas($table){
	$table['jquerymasonry']['titre'] = _T('jquerymasonry:titre_menu');
	$table['jquerymasonry']['icone'] = 'prive/themes/spip/images/jquerymasonry-16.png';
	$table['jquerymasonry']['metas_serialize'] = 'jquerymasonry';
	return $table;
}

?>