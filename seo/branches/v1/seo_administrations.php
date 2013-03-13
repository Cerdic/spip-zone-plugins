<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');
include_spip('base/abstract_sql');

// Installation et mise à jour
function seo_upgrade($nom_meta_version_base, $version_cible){

	$version_actuelle = '0.0';
	if (
		(!isset($GLOBALS['meta'][$nom_meta_version_base]))
		|| (($version_actuelle = $GLOBALS['meta'][$nom_meta_version_base])!=$version_cible)
	){
		/* Installation normale */
		if (version_compare($version_actuelle, '0.0', '=')){
			// Création des tables
			include_spip('base/create');
			creer_base();

			/**
			 * La config de base active insert_head
			 * (les utilisateurs n'ont pas à modifier leurs squelettes et à penser à l'activer)
			 */
			$config_base = array();
			$config_base['insert_head']['activate'] = 'yes';
			ecrire_meta('seo', serialize($config_base), 'non');

			ecrire_meta($nom_meta_version_base, $version_actuelle = $version_cible, 'non');
		}
		/* Gestion des anciennes tables, la numérotation base était de 1.0 */
		if (version_compare($version_actuelle, '1.0', '<=')){
			include_spip('base/create');

			// On change le nom de la table initialement mal choisi
			$prefixe = $GLOBALS['table_prefix'];
			sql_query("RENAME TABLE seo_meta_tags TO $prefixe" . "_seo");
			ecrire_meta($nom_meta_version_base, $version_actuelle = $version_cible, 'non');
		}
		if (version_compare($version_actuelle, '1.1.0', '<')){
			sql_alter('TABLE spip_seo DROP PRIMARY KEY');
			sql_alter('TABLE spip_seo CHANGE type_object objet varchar(10) NOT NULL');
			sql_alter('TABLE spip_seo CHANGE id_object id_objet int(11) NOT NULL');
			sql_alter('TABLE spip_seo ADD PRIMARY KEY ( `id_objet` , `objet` , `meta_name` )');
			ecrire_meta($nom_meta_version_base, $version_actuelle = $version_cible, 'non');
		}
		if (version_compare($version_actuelle, '1.1.1', '<')){
			if (defined('_SEO_FORCER_SQUELETTE'))
				ecrire_config('seo/forcer_squelette', 'yes');
			ecrire_meta($nom_meta_version_base, $version_actuelle = $version_cible, 'non');
		}
	}
}

// Désinstallation
function seo_vider_tables($nom_meta_version_base){

	// On efface la table du plugin
	sql_drop_table('spip_seo');

	// On efface la méta de configuration
	effacer_meta('seo');

	// On efface la version entregistrée
	effacer_meta($nom_meta_version_base);
}
