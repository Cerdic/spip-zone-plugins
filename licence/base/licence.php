<?php
/*
 * Plugin Licence
 * (c) 2007-2009 fanouch
 * Distribue sous licence GPL
 *
 */


function licence_declarer_tables_principales($tables_principales){

	$tables_principales['spip_articles']['field']['id_licence'] = "bigint(21) NOT NULL";
	return $tables_principales;
}


function licence_upgrade($nom_meta_base_version,$version_cible){
	include_spip('inc/meta');
	sql_alter("TABLE spip_articles ADD id_licence bigint(21) DEFAULT '0' NOT NULL AFTER id_article");
	ecrire_meta($nom_meta_base_version,$version_cible,'non');
	ecrire_metas();
}


function licence_vider_tables($nom_meta_base_version) {
	include_spip('inc/meta');
	sql_alter("TABLE spip_articles DROP id_licence");
	effacer_meta($nom_meta_base_version);
}
?>