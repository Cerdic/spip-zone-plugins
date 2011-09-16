<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

// fonction d'installation, mise a jour de la base
function metas_upgrade($nom_meta_base_version, $version_cible){
	include_spip('inc/meta');
	// migration depuis l'ancien systeme de maj
	if (isset($GLOBALS['meta']['spip_metas_version'])
		AND !isset($GLOBALS['meta'][$nom_meta_base_version])){
		ecrire_meta($nom_meta_base_version,$GLOBALS['meta']['spip_metas_version'],'non');
		effacer_meta('spip_metas_version');
	}
	
	$current_version = '0.0';
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/serial');
		include_spip('base/aux');
		if (version_compare($current_version,'0.0','<=')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			// cette fonction cree les tables declarees manquantes
			// ou ajoute des champs declares, manquants
			creer_base();
			echo "Installation du plugin M&eacute;tas effectu&eacute;e correctement !<br/>";
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
	}
}

// fonction de desinstallation
function metas_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_metas");
	sql_drop_table("spip_metas_liens");
	effacer_meta('spip_metas_title');
	effacer_meta('spip_metas_description');
	effacer_meta('spip_metas_mots_importants');
	effacer_meta('spip_metas_mots_keywords');
	effacer_meta($nom_meta_base_version);
}
?>