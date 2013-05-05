<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_configurer_spip_suggest_charger_dist(){
	foreach(array(
		"db_name", "db_keys", "suggest_selecteur", "suggest_form", "suggest_selecteur_affichage", "suggest_classement"
		) as $m)
		$valeurs[$m] = $GLOBALS['meta'][$m];

	return $valeurs;
}


function formulaires_configurer_spip_suggest_traiter_dist(){
	$res = array('editable'=>true);
	foreach(array(
		"db_name", "db_keys", "suggest_selecteur", "suggest_form", "suggest_selecteur_affichage", "suggest_classement"
		) as $m)
			ecrire_meta($m, _request($m));

	$res['message_ok'] = _T('config_info_enregistree');
	return $res;
}

?>