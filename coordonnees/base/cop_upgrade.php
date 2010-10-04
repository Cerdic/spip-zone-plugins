<?php
/**
 * Plugin Coordonnees pour Spip 2.1
 * Licence GPL (c) 2010 - Marcimat / Ateliers CYM
 */

function cop_upgrade($nom_meta_base_version, $version_cible){
	include_spip('inc/meta');
	
	
	/**
	 *
	 *  11/01/2009 : ajout table spip_emails, version 1.0.1
	 *
	 */
	 
	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	
	if ($current_version=="0.0") {
		include_spip('base/create');
		creer_base();
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}

	if (version_compare($current_version,"1.2","<")){
		// on passe de "voie" à "adresse"
		sql_alter("TABLE spip_adresses CHANGE voie adresse TINYTEXT NOT NULL DEFAULT ''");
		// on ajoute le contenu du champ "numero" au champ "adresse"
		sql_update("TABLE spip_adresses SET `adresse` = CONCAT(`numero`, ' ', `adresse`) WHERE `numero` IS NOT NULL or `numero` <> ''");
		// on supprime le champ "numero"
		sql_alter("TABLE spip_adresses DROP COLUMN `numero`");
		spip_log('Tables coordonnées correctement passsées en version 1.1','cop');
		ecrire_meta($nom_meta_base_version, $current_version="1.1");
	}

}

function cop_vider_tables($nom_meta_base_version) {
	
	sql_drop_table("spip_adresses");
	sql_drop_table("spip_adresses_liens");
	sql_drop_table("spip_numeros");
	sql_drop_table("spip_numeros_liens");
	sql_drop_table("spip_emails");
	sql_drop_table("spip_emails_liens");

	effacer_meta($nom_meta_base_version);
}

?>
