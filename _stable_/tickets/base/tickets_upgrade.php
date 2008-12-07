<?php
include_spip('inc/meta');
include_spip('base/create');
include_spip('inc/vieilles_defs');

function tickets_upgrade($nom_meta_base_version,$version_cible){
	$current_version = "0.0";
	// On traite le cas de la premiere version de Tickets sans version_base
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version])) && tickets_existe())
		$current_version = "0.1";
		
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
		
	if ($current_version=="0.0") {
		include_spip('base/tickets_install');
		creer_base();
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
	}
	if (version_compare($current_version,"0.2","<")){
		//modifications de la table spip_tickets
		sql_alter("TABLE spip_tickets ADD COLUMN jalon varchar(10) DEFAULT '' NOT NULL AFTER exemple");
		sql_alter("TABLE spip_tickets ADD COLUMN version varchar(10) DEFAULT '' NOT NULL AFTER exemple");
		sql_alter("TABLE spip_tickets ADD COLUMN composant varchar(40) DEFAULT '' NOT NULL AFTER exemple");
		sql_alter("TABLE spip_tickets ADD COLUMN projet varchar(60) DEFAULT '' NOT NULL AFTER exemple");
		
		ecrire_meta($nom_meta_base_version,$current_version="0.2");
	}
		
		ecrire_metas();
}

function tickets_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_tickets");
	sql_drop_table("spip_tickets_forum");
	effacer_meta($nom_meta_base_version);
	ecrire_metas();
}

function tickets_existe() {
	$desc = sql_showtable('spip_tickets', true);
	if (!$desc['field']) 
		return false;
	else
		return true;
}

?>