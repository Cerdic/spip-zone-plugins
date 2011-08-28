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
		// modifications de la table spip_tickets,
		// ajout des champs jalon, version, composant, projet
		maj_tables('spip_tickets');
		ecrire_meta($nom_meta_base_version,$current_version="0.2");
	}
	if (version_compare($current_version,"0.6","<")){
		// modifications de la table spip_tickets
		sql_alter("TABLE spip_tickets MODIFY jalon varchar(30) DEFAULT '' NOT NULL");
		sql_alter("TABLE spip_tickets MODIFY version varchar(30) DEFAULT '' NOT NULL");
		
		ecrire_meta($nom_meta_base_version,$current_version="0.6");
	}
	if (version_compare($current_version,"0.7","<")){
		// ajout des champs ip
		maj_tables(array('spip_tickets', 'spip_tickets_forum'));
		ecrire_meta($nom_meta_base_version,$current_version="0.7");
	}

	// au dessus de 1.0, c'est specifique SPIP >= 2.1
	if (version_compare($current_version,"1.0","<")) {
		// migrer sur la table forums pour la version 2.1...
		migrer_commentaires_tickets_vers_forums();
		sql_drop_table("spip_tickets_forum");
		ecrire_meta($nom_meta_base_version,$current_version="1.0");
	}
	if (version_compare($current_version,"1.1","<")){
		// modifications de la table spip_tickets,
		// ajout du champ navigateur
		maj_tables('spip_tickets');
		ecrire_meta($nom_meta_base_version,$current_version="1.1");
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


function migrer_commentaires_tickets_vers_forums() {
	$res = sql_select('*', 'spip_tickets_forum');
	if ($res) {
		$correspondances = array();
		while ($r = sql_fetch($res)) {
			$titre = sql_getfetsel('titre', 'spip_tickets', 'id_ticket='. sql_quote($r['id_ticket']));
			$auteur = sql_fetsel(array('nom','email'), 'spip_auteurs', 'id_auteur='. sql_quote($r['id_auteur']));
			$correspondances[] = array(
				"id_objet"	=> $r['id_ticket'],
				"objet"		=> "ticket",
				"id_parent"	=> 0,
				"id_thread"	=> 0, // prendra id_forum cree
				"date_heure"	=> $r['date'],
				"titre"	=> $titre,
				"texte"	=> $r['texte'],
				"auteur"	=> ($auteur ? $auteur['nom'] : ''),
				"email_auteur"	=> ($auteur ? $auteur['email'] : ''),
				"statut"	=> "publie", // publie = public, prive = prive... dilemme ?
				"ip"	=> $r['ip'],
				"id_auteur"	=> $r['id_auteur'],
			);
		}
		
		if (count($correspondances)) {
			sql_insertq_multi('spip_forum', $correspondances);
			sql_update('spip_forum',
				array('id_thread'=>'id_forum'),
				array('id_thread=0', 'objet='.sql_quote('ticket')));
		}
	}
}

?>
