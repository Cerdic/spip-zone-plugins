<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/cextras_gerer');
include_spip('base/multidomaines_pipelines');

function multidomaines_install($action){
	switch ($action){
		case 'test':
			return sql_getfetsel("valeur", "spip_meta", "nom ='multidomaines_install'")=="oui";
		break;
		case 'install':
			$champs = multidomaines_declarer_champs_extras();
			creer_champs_extras($champs);
			sql_replace('spip_meta',array('nom'=>'multidomaines_install','valeur'=>'oui'));
		break;
		case 'uninstall':
			$champs = multidomaines_declarer_champs_extras();
			vider_champs_extras($champs);
			sql_replace('spip_meta',array('nom'=>'multidomaines_install','valeur'=>''));
		break;
	}
}

?>