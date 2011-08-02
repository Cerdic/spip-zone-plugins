<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

// Installation et mise à jour
function seo_upgrade($nom_meta_version_base, $version_cible){

	$version_actuelle = '0.0';
	if (
		(!isset($GLOBALS['meta'][$nom_meta_version_base]))
		|| (($version_actuelle = $GLOBALS['meta'][$nom_meta_version_base]) != $version_cible)
	){
		/* Gestion des anciennes tables, la numérotation base était de 1.0 */
		if (version_compare($version_actuelle,'1.0','=')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			
			// On change le nom de la table initialement mal choisi
			$prefixe = $GLOBALS['table_prefix'];
			sql_query("RENAME TABLE seo_meta_tags TO $prefixe"."_seo");
			
			echo "Mise à jour du plugin SEO vers ses nouvelles tables<br/>";
			ecrire_meta($nom_meta_version_base, $version_actuelle=$version_cible, 'non');
		}
		/* FIN : Gestion des anciennes tables, la numérotation base était de 1.0 */

		/* Installation normale */
		if (version_compare($version_actuelle,'0.0','=')){
			// Création des tables
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			
			ecrire_meta($nom_meta_version_base, $version_actuelle=$version_cible, 'non');
		}
	}
}

// Désinstallation
function seo_vider_tables($nom_meta_version_base){
	include_spip('base/abstract_sql');
	
	// On efface les tables du plugin
	sql_drop_table('spip_seo');
		
	// On efface la version entregistrée
	effacer_meta($nom_meta_version_base);
}
