<?php
/**
 * Plugn SPIP-Projet
 * Licence GPL
 *
 * Installation et upgrade des tables
 *
 */
include_spip('inc/meta');

function projets_tickets_upgrade($nom_meta_base_version,$version_cible){
	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];

	if ($current_version=="0.0") {
		/**
		 * On insère la ligature avec tickets automatiquement à l'installation
		 */
		$conf_projets = lire_config('projet',array());
		$conf_projets['ligatures']['objets'][] = 'ticket';
		$conf_projets = serialize($conf_projets);
		ecrire_meta('projet',$conf_projets);
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
	}
}

function projets_tickets_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}
?>