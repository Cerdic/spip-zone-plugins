<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_configurer_comments_charger_dist(){
	foreach(array(
		"comments_fil", "permalink", "forum_longueur_mini", "forum_longueur_maxi", "style"
		) as $m)
		$valeurs[$m] = $GLOBALS['meta'][$m];

	return $valeurs;
}


function formulaires_configurer_comments_traiter_dist(){
	$res = array('editable'=>true);
	foreach(array(
		"comments_fil", "permalink", "forum_longueur_mini", "forum_longueur_maxi", "style"
		) as $m)
			ecrire_meta($m, _request($m));

	$res['message_ok'] = _T('config_info_enregistree');
	return $res;
}

?>