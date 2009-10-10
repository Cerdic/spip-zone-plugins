<?php
include_spip('inc/meta');
include_spip('base/create');

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
		//modifications de la table spip_tickets,
		// ajout des champs jalon, version, composant, projet
		maj_tables('spip_tickets');
		ecrire_meta($nom_meta_base_version,$current_version="0.2");
	}
	if (version_compare($current_version,"0.6","<")){
		//modifications de la table spip_tickets
		sql_alter("TABLE spip_tickets MODIFY jalon varchar(30) DEFAULT '' NOT NULL");
		sql_alter("TABLE spip_tickets MODIFY version varchar(30) DEFAULT '' NOT NULL");
		
		ecrire_meta($nom_meta_base_version,$current_version="0.6");
	}
	if (version_compare($current_version,"0.7","<")){
		// ajout des champs ip
		maj_tables(array('spip_tickets', 'spip_tickets_forum'));
		ecrire_meta($nom_meta_base_version,$current_version="0.7");
	}	
		
}

function tickets_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_tickets");
	sql_drop_table("spip_tickets_forum");
	effacer_meta($nom_meta_base_version);
}

function tickets_existe() {
	$desc = sql_showtable('spip_tickets', true);
	if (!$desc['field']) 
		return false;
	else
		return true;
}

?>
