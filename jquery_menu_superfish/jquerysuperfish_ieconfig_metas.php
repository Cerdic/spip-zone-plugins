<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function jquerysuperfish_ieconfig_metas($table){
	$table['jquerysuperfish']['titre'] = _T('jquerysuperfish:titre_menu');
	$table['jquerysuperfish']['icone'] = 'prive/themes/spip/images/jquerysuperfish-16.png';
	$table['jquerysuperfish']['metas_serialize'] = 'jquerysuperfish';
	return $table;
}

?>