<?php

function metas_ieconfig_metas($table){
	$table['metas']['titre'] = _T('metas:configuration_metas');
	$table['metas']['icone'] = 'images/metas-24.png';
	$table['metas']['metas_brutes'] = 'spip_metas_title,spip_metas_description,spip_metas_keywords,spip_metas_mots_importants';
	return $table;
}

?>