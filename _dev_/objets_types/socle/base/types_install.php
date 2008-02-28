<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/meta');

function types_upgrade($nom_meta, $version_cible) {
	global $tables_principales;
	$current_version = 0.0;
	
	if (   (!isset($GLOBALS['meta'][$nom_meta]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta]) != $version_cible)){
		include_spip('base/types_serial');
		if ($current_version == 0.0){
			//include_spip('base/create');
			include_spip('base/abstract_sql');
			//on ajoute le champ
			spip_query("ALTER TABLE spip_articles ADD `"._TYPE."` VARCHAR(10) DEFAULT 'article' NOT NULL");
			spip_query("ALTER TABLE spip_rubriques ADD `"._TYPE."` VARCHAR(10) DEFAULT 'rubrique' NOT NULL");
			//spip_query("ALTER TABLE spip_groupes_mots ADD `"._TYPE."` VARCHAR(10) DEFAULT 'normal' NOT NULL");
			spip_query("ALTER TABLE `spip_articles` ADD INDEX (`"._TYPE."`)") ;
			spip_query("ALTER TABLE `spip_rubriques` ADD INDEX (`"._TYPE."`)") ;
			//spip_query("ALTER TABLE `spip_groupes_mots` ADD INDEX (`"._TYPE."`)") ;
			//parce que creer_base ne sait que creer des tables
			//creer_base();
			ecrire_meta($nom_meta, $current_version = 0.1,'non');
			ecrire_metas();
		}
	}
}

function types_vider_tables($nom_meta) {
	include_spip('base/types_serial');
	include_spip('base/abstract_sql');
	spip_query("ALTER TABLE spip_articles DROP "._TYPE);
	spip_query("ALTER TABLE spip_rubriques DROP "._TYPE);
	//spip_query("ALTER TABLE spip_groupes_mots DROP "._TYPE);
	spip_query("ALTER TABLE `spip_articles` DROP INDEX (`"._TYPE."`)") ;
	spip_query("ALTER TABLE `spip_rubriques` DROP INDEX (`"._TYPE."`)") ;
	//spip_query("ALTER TABLE `spip_groupes_mots` DROP INDEX (`"._TYPE."`)") ;
	effacer_meta($nom_meta);
	ecrire_metas();
}

?>