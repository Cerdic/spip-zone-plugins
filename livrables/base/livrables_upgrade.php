<?php

/*
 * Plugin Livrables
 * Licence GPL (c) 2011 Cyril Marion
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function livrables_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/livrables');
		if (version_compare($current_version,'0.0','<=')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			ecrire_meta($nom_meta_base_version,$current_version='0.1','non');
		}
		// ajout d'un index sur l'url
		if (version_compare($current_version,"0.2","<")){
			maj_tables('spip_livrables');
			ecrire_meta($nom_meta_base_version,$current_version="0.2");
		}
		// ajout d'un champ "id_projet"
		if (version_compare($current_version,"0.3","<")){
			maj_tables('spip_livrables');
			ecrire_meta($nom_meta_base_version,$current_version="0.3");
		}
		// ajout d'un champ "id_livrable"
		if (version_compare($current_version,"0.4","<")){
			sql_alter("TABLE spip_tickets ADD id_livrable bigint(21) NOT NULL DEFAULT '0' AFTER exemple");
			sql_alter("TABLE spip_tickets ADD INDEX (id_livrable)");
			ecrire_meta($nom_meta_base_version,$current_version="0.4");
		}
		// ajout des champs "statut_client" et "statut_atelier"
		if (version_compare($current_version,"0.5","<")){
			sql_alter("TABLE spip_livrables ADD statut_client VARCHAR(10) NOT NULL AFTER id_projet");
			sql_alter("TABLE spip_livrables ADD statut_atelier VARCHAR(10) NOT NULL AFTER statut_client");
			sql_alter("TABLE spip_livrables ADD INDEX (statut_client)");
			sql_alter("TABLE spip_livrables ADD INDEX (statut_atelier)");
			ecrire_meta($nom_meta_base_version,$current_version="0.5");
		}
		// ajout des champs "objet", "type" et "composition"
		if (version_compare($current_version,"0.6","<")){
			sql_alter("TABLE spip_livrables ADD objet VARCHAR(50) NOT NULL AFTER url");
			sql_alter("TABLE spip_livrables ADD type VARCHAR(50) NOT NULL AFTER objet");
			sql_alter("TABLE spip_livrables ADD composition VARCHAR(50) NOT NULL AFTER type");
			sql_alter("TABLE spip_livrables ADD INDEX (objet)");
			sql_alter("TABLE spip_livrables ADD INDEX (type)");
			sql_alter("TABLE spip_livrables ADD INDEX (composition)");
			ecrire_meta($nom_meta_base_version,$current_version="0.6");
		}
	}
}

/**
 * Fonction de desinstallation
 *
 * @param unknown_type $nom_meta_base_version
 */
function livrables_vider_tables($nom_meta_base_version) {
	sql_alter('TABLE spip_tickets DROP COLUMN id_livrable');
	sql_drop_table("spip_livrables");
	sql_drop_table("spip_livrables_liens");
	effacer_meta('livrables');
	effacer_meta($nom_meta_base_version);

	// en attendant... 
	sql_drop_table("spip_composants");
	sql_drop_table("spip_composants_projets");
	effacer_meta('composants');
	effacer_meta($nom_meta_base_version);
}

?>