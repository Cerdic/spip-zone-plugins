<?php
/**
 * Plugin Catalogue pour Spip 2.0
 * Licence GPL (c) 2009 - 2010- Ateliers CYM
 *
 */

include_spip('inc/meta');
include_spip('base/create');

function catalogue_upgrade($nom_meta_base_version, $version_cible){
	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version])) {
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
		spip_log('12:14 Version actuelle : '.$current_version,'catalogue');
	}
	
	if ($current_version=="0.0") {
		creer_base();
		#include_spip('base/catalogue_peupler_base');
		#catalogue_peupler_base();
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
		spip_log('Base de données Catalogue correctement peuplée','catalogue');
	}
	if (version_compare($current_version,"1.3","<")){
		maj_tables('spip_variantes');
		maj_tables('spip_options');
		maj_tables('spip_options_articles');
		maj_tables('spip_transactions');
		maj_tables('spip_lignes_transactions');
				
		ecrire_meta($nom_meta_base_version,$current_version="1.3");
		spip_log('Tables du plugin Catalogue correctement passsées en version 1.3','catalogue');
	}

	if (version_compare($current_version,"1.3.1","<")){
		if (!sql_alter("TABLE spip_variantes CHANGE descriptif descriptif TEXT DEFAULT '' NOT NULL")) spip_log('Probleme lors de la modif de la table variantes','catalogue'); // on change le type de "descriptif" de TINYTEXT à TEXT
		if (!sql_alter("TABLE spip_options CHANGE descriptif descriptif TEXT DEFAULT '' NOT NULL")) spip_log('Probleme lors de la modif de la table options','catalogue'); // on change le type de "descriptif" de TINYTEXT à TEXT

		ecrire_meta($nom_meta_base_version, $current_version="1.3.1");
		spip_log('Tables du plugin Catalogue correctement passsées en version 1.3.1','catalogue');
	}

	// prefixer les tables
	if (version_compare($current_version,"1.4.0","<")){

		// changements de tables
		sql_alter('TABLE spip_variantes RENAME TO spip_cat_variantes');
		sql_alter('TABLE spip_options RENAME TO spip_cat_options');
		sql_alter('TABLE spip_transactions RENAME TO spip_cat_transactions');
		sql_alter('TABLE spip_lignes_transactions RENAME TO spip_cat_lignes_transactions');
		sql_alter('TABLE spip_options_articles RENAME TO spip_cat_options_articles');

		// alterations des identifiants
		sql_alter('TABLE spip_cat_variantes CHANGE id_variante id_cat_variante bigint(21) NOT NULL auto_increment');
		sql_alter('TABLE spip_cat_options CHANGE id_option id_cat_option bigint(21) NOT NULL auto_increment');
		sql_alter('TABLE spip_cat_transactions CHANGE id_transaction id_cat_transaction bigint(21) NOT NULL auto_increment');
		sql_alter('TABLE spip_cat_lignes_transactions CHANGE id_ligne id_cat_ligne bigint(21) NOT NULL auto_increment');
		sql_alter('TABLE spip_cat_lignes_transactions CHANGE id_transaction id_cat_transaction bigint(21) NOT NULL DEFAULT 0');
		sql_alter('TABLE spip_cat_options_articles CHANGE id_option id_cat_option bigint(21) NOT NULL DEFAULT 0');
		
		ecrire_meta($nom_meta_base_version, $current_version="1.4.0");
		spip_log('Tables du plugin Catalogue correctement passsées en version 1.4.0','catalogue');
	}

	// pas de gestion de transactions dans ce plugin !
	if (version_compare($current_version,"1.5.0","<")){
		sql_drop_table("spip_lignes_transactions");
		sql_drop_table("spip_transactions");
		ecrire_meta($nom_meta_base_version, $current_version="1.5.0");
		spip_log('Tables du plugin Catalogue correctement passsées en version 1.5.0','catalogue');		
	}
}


function catalogue_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_options_articles");
	sql_drop_table("spip_options");
	sql_drop_table("spip_variantes");

	spip_log('Plugin Catalogue correctement désinstallé.','catalogue');
	effacer_meta($nom_meta_base_version);
}

?>
