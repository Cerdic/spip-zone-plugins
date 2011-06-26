<?php

include_spip('inc/cextras_gerer');
include_spip('base/mots_techniques');

function mots_techniques_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (version_compare($current_version,'0.0','<=')){
			$champs = mots_techniques_declarer_champs_extras();
			creer_champs_extras($champs);
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
		if ($current_version<0.2){
			sql_alter("TABLE spip_groupes_mots DROP affiche_formulaire");
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');				
		}
	}
}

function mots_techniques_vider_tables($nom_meta_base_version) {
	$champs = mots_techniques_declarer_champs_extras();
	desinstaller_champs_extras($champs, $nom_meta_base_version);
}

?>
