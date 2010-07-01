<?php
/**
 * Plugin Comptes pour Spip 2.0
 * Licence GPL
 * Auteur Cyril MARION - Ateliers CYM
 *
 */

include_spip('inc/meta');
include_spip('base/create');
include_spip('base/peupler_base');


function comptes_upgrade($nom_meta_base_version, $version_cible){
	/**
	 *
	 *  11/01/2009 : ajout table spips_emails, version 1.0.1
	 *
	 */
	 
	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	
	if ($current_version=="0.0") {
		creer_base();
		peupler_base_comptes();
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}
	if (version_compare($current_version,"1.0.1","<")){
		maj_tables('spip_comptes');
		maj_tables('spip_contacts');
		maj_tables('spip_comptes_contacts');
		maj_tables('spip_adresses');
		maj_tables('spip_adresses_liens');
		maj_tables('spip_numeros');
		maj_tables('spip_numeros_liens');
		maj_tables('spip_emails');
		maj_tables('spip_emails_liens');
		maj_tables('spip_champs');
		maj_tables('spip_champs_liens');
		
		ecrire_meta($nom_meta_base_version,$current_version="1.0.1");
	}
}

function comptes_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_comptes");
	sql_drop_table("spip_contacts");
	sql_drop_table("spip_comptes_contacts");	
	sql_drop_table("spip_adresses");
	sql_drop_table("spip_adresses_liens");
	sql_drop_table("spip_numeros");
	sql_drop_table("spip_numeros_liens");
	sql_drop_table("spip_emails");
	sql_drop_table("spip_emails_liens");
	sql_drop_table("spip_champs");
	sql_drop_table("spip_champs_liens");

	effacer_meta($nom_meta_base_version);
}

?>