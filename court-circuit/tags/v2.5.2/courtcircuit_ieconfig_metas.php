<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function courtcircuit_ieconfig_metas($table){
	$table['courtcircuit']['titre'] = _T('courtcircuit:courtcircuit');
	$table['courtcircuit']['icone'] = 'courtcircuit-16.png';
	$table['courtcircuit']['metas_serialize'] = 'courtcircuit';
	return $table;
}

?>