<?php
/**
 * Plugin commandes pour Spip 2.1
 * Licence GPL
 * Cyril MARION - Ateliers CYM http://www.cym.fr
 *
 */

include_spip('inc/meta');
include_spip('base/create');

function commandes_upgrade($nom_meta_base_version, $version_cible){
	$current_version = "0.0";
	if ( (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){

		$config = lire_config('commandes');
		if (!is_array($config)) {
			$config = array();
		}

		include_spip('commandes_fonctions');
		$id_webmestre = commandes_id_premier_webmestre();
		$config = array_merge(array(
				'duree_vie' => '1',
				'activer' => '',
				'quand' => array_keys(commandes_lister_statuts()),
				'expediteur' => 'webmaster',
				'expediteur_webmaster' => $id_webmestre,
				'expediteur_administrateur' => '',
				'expediteur_email' => '',
				'vendeur' => 'webmaster',
				'vendeur_webmaster' => $id_webmestre,
				'vendeur_administrateur' => '',
				'vendeur_email' => '',
				'client' => 'on'
		), $config);
		if ($current_version=="0.0") {
			creer_base();
			ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
		}
		// ajout de objet/id_objet sur les details de commande
		if (version_compare($current_version,"0.2","<")){
			maj_tables('spip_commandes_details');
			ecrire_meta($nom_meta_base_version, $current_version="0.2");
		}
		// La duree de vie des commandes passent de secondes en heures
		if (version_compare($current_version,"0.3","<")){
			$config['duree_vie'] = intval($config['duree_vie'] / 3600) ;
			ecrire_meta($nom_meta_base_version, $current_version="0.3");
		}
		ecrire_meta('commandes', serialize($config));
	}
}


function commandes_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_commandes");
	sql_drop_table("spip_commandes_details");
	effacer_meta('commandes');
	effacer_meta($nom_meta_base_version);
}



?>
