<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

// TABLES INTERFACES
function albums_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_tables']['albums'] = 'albums';
	$interfaces['table_des_tables']['albums_liens'] = 'albums_liens';
	return $interfaces;
}

// TABLES SQL
function albums_declarer_tables_objets_sql($tables){

	// ALBUMS
	$tables['spip_albums'] = array(
	
			/* Infos base de donnee */
	
		// Nom raccourci de la table
		'table_objet' => 'albums',
		// Synonymes de table_objet
		'table_objet_surnom' => array('albums'),
		// 
		'type' => 'album',
		// Synonymes de type
		'type_surnoms' => array('album'),
	
		// Champs SQL de l objet
		'field'=> array(
			"id_album"	=> "bigint(21) NOT NULL",
			"titre"	=> "tinytext DEFAULT '' NOT NULL",
			"categorie"	=> "varchar(255)  DEFAULT '' NOT NULL",
			"descriptif"	=> "text DEFAULT '' NOT NULL",
			"lang"  => "VARCHAR(10) DEFAULT '' NOT NULL",
			"langue_choisie"	=> "VARCHAR(3) DEFAULT 'non'",
			"id_trad" => "bigint(21) DEFAULT '0' NOT NULL",
			"statut" => "varchar(255)  DEFAULT '' NOT NULL",
			"date" => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"maj"	=> "TIMESTAMP"
		),
		
		// Cle SQL de l objet
		'key' => array(
			"PRIMARY KEY"	=> "id_album",
		),

		// Champs declares explicitement pour jointure
		'join' => array(
			"id_album" => "id_album",
		),
		// Declaration table de liens et de son champ de jointure
		'tables_jointures' => array(
			'albums_liens'
		),
		
		// Table Principale ou auxiliaire ?
		'principale' => "oui",
		
		// Modeles associes a l objet
		'modeles' => array(
			'album'
		),
		
		
			/* Titre, date et gestion des status */
		
		// Titre et date
		'titre' => "titre, '' AS lang",
		'date' => "date",
		
		// Statuts de l objet
		'statut'=> array(
			array(
				'champ' => 'statut',
				'publie' => 'publie',
				'previsu' => 'publie,prepa',
				'post_date' => 'date',
				'exception' => array('statut','tout')
			)
		),
		// Puces associees a chaque statut
		# statut_images
		// Chaines de langues pour statuts
		'statut_textes_instituer' => 	array(
			'prepa' => 'texte_statut_en_cours_redaction',
			'publie' => 'texte_statut_publie',
			'poubelle' => 'texte_statut_poubelle',
		),
		
		
			/* Edition, affichage et recherche */
		
		// acces edition dans espace prive
		'editable' => "oui",
		
		// Champs modifiables dans le formulaire d edition
		'champs_editables' => array(
			"titre", "categorie", "descriptif"
		),
		
		// Champs soumis au processus de revision
		'champs_versionnes' => array(
			"titre", "categorie", "descriptif"
		),
		
		// Champs utilises dans la recherche et leur score respectif (1 a 10)
		'rechercher_champs' => array(
			'titre' => 8,
			'categorie' => 4,
			'descriptif' => 1
		),
		
		// Champs utilises pour la jointure et leur score respectif
		# rechercher_jointure
			
		
			/* Textes standards */
			
		'texte_creer_associer' => 'album:creer_et_associer_un_album',
		'texte_creer' => 'album:texte_creer_album',
		'texte_ajouter' => 'album:texte_ajouter_album',
		'texte_changer_statut' => 'album:texte_changer_statut',
	);
	
	return $tables;
}

// TABLES AUXILIAIRES
function albums_declarer_tables_auxiliaires($tables_auxiliaires) {

	$tables_auxiliaires['spip_albums_liens'] = array(
		'field' => array(
			"id_album"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"objet"	=> "VARCHAR (25) DEFAULT '' NOT NULL",
			"vu"	=> "ENUM('non', 'oui') DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"	=> "id_album,id_objet,objet",
			"KEY id_album"	=> "id_album",
			"KEY id_objet"	=> "id_objet",
			"KEY objet"	=> "objet",
		)
	);
	
	return $tables_auxiliaires;
}

?>
