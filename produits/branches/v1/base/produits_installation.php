<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/meta');

// Installation et mise à jour
function produits_upgrade($nom_meta_version_base, $version_cible){
	$version_actuelle = '0.0';
	if (
		(!isset($GLOBALS['meta'][$nom_meta_version_base]))
		|| (($version_actuelle = $GLOBALS['meta'][$nom_meta_version_base]) != $version_cible)
	){
		$config = lire_config('produits');
		if (!is_array($config)) {
			$config = array();
		}
		$config = array_merge(array(
				'taxe' => '0',
				'limiter_ajout' => '',
		), $config);
		ecrire_meta('produits', serialize($config));

		if (version_compare($version_actuelle,'0.0','=')){
			// Création des tables
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
		}
		
		ecrire_meta($nom_meta_version_base, $version_actuelle=$version_cible, 'non');	
	}
}

// Désinstallation
function produits_vider_tables($nom_meta_version_base){
	include_spip('base/abstract_sql');
	
	// On efface les tables du plugin
	sql_drop_table('spip_produits');
		
	// On efface la version entregistrée
	effacer_meta($nom_meta_version_base);
}

?>
