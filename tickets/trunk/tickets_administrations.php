<?php
/**
 * Plugin Tickets
 * Licence GPL (c) 2008-2012
 *
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function tickets_upgrade($nom_meta_base_version,$version_cible){
	$current_version = "0.0";
	
	// On traite le cas de la premiere version de Tickets sans version_base
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version])) && tickets_existe())
		$current_version = "0.1";
	
	$maj = array();
	$maj['create'] = array(
		array('maj_tables',array('spip_tickets'))
	);
	
	$maj['0.2'] = array('maj_tables',array('spip_tickets'));
	$maj['0.6'] = array(
		array('sql_alter',"TABLE spip_tickets MODIFY jalon varchar(30) DEFAULT '' NOT NULL"),
		array('sql_alter',"TABLE spip_tickets MODIFY version varchar(30) DEFAULT '' NOT NULL")
	);
	$maj['0.7'] = array('maj_tables',array('spip_tickets'));
	$maj['1.1'] = array(
		array('maj_tables',array('spip_tickets')),
		array('migrer_commentaires_tickets_vers_forums',''),
		array('sql_drop_table',"spip_tickets_forum")
	);
	$maj['1.2'] = array(
		array('maj_tables',array('spip_tickets'))
	);
	$maj['1.3'] = array(
		array('sql_alter',"TABLE spip_tickets DROP tracker"),
		array('sql_alter',"TABLE spip_tickets CHANGE type tracker integer DEFAULT '0' NOT NULL")
	);
	$maj['1.4'] = array(
		array('maj_tables',array('spip_tickets'))
	);
	$maj['1.4.1'] = array(
		array('sql_alter',"TABLE spip_tickets CHANGE version version varchar(255) DEFAULT '' NOT NULL")
	);
	/**
	 * On ne prend plus en compte le statut "redac"
	 */
	$maj['1.5.0'] = array(
		array('tickets_supprimer_redac','')
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function tickets_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_tickets");
	effacer_meta($nom_meta_base_version);
}

function tickets_existe() {
	$desc = sql_showtable('spip_tickets', true);
	if (!$desc['field']) 
		return false;
	else
		return true;
}

function tickets_supprimer_redac(){
	sql_updateq('spip_tickets',array('statut' => 'ouvert'),array('statut'=>'redac'));
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
