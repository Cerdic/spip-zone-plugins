<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_didaspip_import_charger_dist(){
	foreach(array(
		"didaspip_url",
		"didaspip_box",
		"didawidth",
		"didaheight",
		"accesdida",
		"accessuppr"
		) as $m)
		$valeurs[$m] = $GLOBALS['meta'][$m];
	return $valeurs;
}


function formulaires_didaspip_import_traiter_dist(){
	$res = array('editable'=>true);
		$res['message_ok'] = _T('config_info_enregistree');
	return $res;
}

