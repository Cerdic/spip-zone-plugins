<?php
/**
 * @name 		Tables
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 *
 * - 0cree	=> cas exceptionnel de création via ADDS
 * - 1inactif
 * - 2actif
 * - 3obsolete
 * - 4rompu
 * - 5poubelle
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function pubban_declarer_tables_interfaces($interface){

	// Tables principales
	$interface['table_des_tables']['publicites'] = 'publicites';
	$interface['table_des_tables']['bannieres'] = 'bannieres';
	$interface['table_des_tables']['bannieres_publicites'] = 'bannieres_publicites';

	// Table de jointure
	$interface['tables_jointures']['spip_bannieres_publicites'][] = 'publicites';
	$interface['tables_jointures']['spip_bannieres_publicites'][] = 'bannieres';

	// Table des dates
	$interface['table_date']['publicites'] = 'date_debut';
	$interface['table_date']['publicites'] = 'date_fin';
	$interface['table_date']['publicites'] = 'date_add';

	return $interface;
}

function pubban_declarer_tables_objets_sql($tables){

	$tables['spip_publicites'] = array(
		'type'=>'publicite',
	  'type_surnoms' => array('pub'),
	  // objet
	  'icone_objet'=>'publicite', // a faire en 16, 24 et 32
	  'editable'=>'oui',
	  'champs_editables'=>array(
			"url", "blank", "titre", "objet", "type", "illimite", "affichages", "clics", "date_debut", "date_fin"
	  ),
	  'champs_versionnes'=>array(
			"url", "titre", "objet", "date_debut", "date_fin"
	  ),
	  // pages
	  'modele'=>array('pub'),
	  'url_voir'=>'publicite',
	  'url_edit'=>'publicite_edit',
	  // textes
		'texte_retour' => 'icone_retour',
		'texte_objets' => 'pubban:icone_publicites',
		'texte_objet' => 'pubban:icone_publicite',
		'texte_modifier' => 'pubban:icone_modifier_publicite',
		'texte_ajouter' => 'pubban:icone_nouvelle_publicite',
		'texte_creer' => 'pubban:icone_nouvelle_publicite',
		'info_aucun_objet'=> 'pubban:info_aucune_publicite',
		'info_1_objet' => 'pubban:info_1_publicite',
		'info_nb_objets' => 'pubban:info_nb_publicites',
	  // table
		'principale' => 'oui',
		'field'=> array(
			"id_publicite" => "bigint(21) NOT NULL",
			"statut" => "varchar(100) NOT NULL DEFAULT '1inactif'",
			"url" => "varchar(200) NOT NULL DEFAULT ''",
			"blank" => "enum('non','oui') NOT NULL DEFAULT 'oui'",
			"titre" => "varchar(200) NOT NULL DEFAULT ''",
			"objet" => "text NOT NULL",
			"type" => "varchar(60) NOT NULL DEFAULT 'img'",
			"illimite" => "enum('non','oui') NOT NULL DEFAULT 'non'",
			"affichages" => "bigint(20) NOT NULL DEFAULT '0'",
			"affichages_restant" => "bigint(20) NOT NULL DEFAULT '0'",
			"clics" => "bigint(20) NOT NULL DEFAULT '0'",
			"clics_restant" => "bigint(20) NOT NULL DEFAULT '0'",
			"date_debut" => "varchar(10) NOT NULL DEFAULT ''",
			"date_fin" => "varchar(10) NOT NULL DEFAULT ''",
			"date_add" => "datetime NOT NULL default '0000-00-00 00:00:00'",
			"maj" => "TIMESTAMP",
		),
		'key' => array(
			"PRIMARY KEY" => "id_publicite",
			"KEY titre" => "titre",
			"KEY statut" => "statut"
		),
		'join' => array(
			"id_publicite"=>"id_publicite",
		),
		'titre' => "titre AS titre, '' AS lang",
		'date' => 'date_add',
 		// statut
		'texte_changer_statut' => 'pubban:info_statut_publicite_1',
    'statut_titres' => array(
			'0cree' => 'pubban:info_titre_publicite_creee',
			'1inactif' => 'pubban:info_titre_publicite_inactive',
			'2actif' => 'pubban:info_titre_publicite_active',
			'3obsolete' => 'pubban:info_titre_publicite_obsolete',
			'4rompu' => 'pubban:info_titre_publicite_rompue',
			'5poubelle' => 'pubban:info_titre_publicite_poubelle',
    ),
    'statut_textes_instituer' => array(
			'0cree' => 'pubban:info_publicite_creee',
			'1inactif' => 'pubban:info_publicite_inactive',
			'2actif' => 'pubban:info_publicite_active',
			'3obsolete' => 'pubban:info_publicite_obsolete',
			'4rompu' => 'pubban:info_publicite_rompue',
			'5poubelle' => 'pubban:info_publicite_poubelle',
    ),
		'statut_images' => array(
			'0cree' => 'puce-poubelle.gif',
			'1inactif' => 'puce-blanche.gif',
			'2actif' => 'puce-verte.gif',
			'3obsolete' => 'clock_stop-8.png',
			'4rompu' => 'puce-orange.gif',
			'5poubelle' => 'puce-poubelle.gif',
		),
    'statut'=> array(
			array(
				'champ' => 'statut',
				'publie' => '2actif',
				'previsu' => '0cree,1inactif,3obsolete,5poubelle',
				'post_date' => 'date', 
				'exception' => array('statut','tout')
			)
    ),
	);

	$tables['spip_bannieres'] = array(
		'type'=>'banniere',
	  // objet
	  'icone_objet'=>'banniere', // a faire en 16, 24 et 32
	  'editable'=>'oui',
	  'champs_editables'=>array(
			"titre", "titre_id", "width", "height"
	  ),
	  'champs_versionnes'=>array(
			"titre", "titre_id"
	  ),
	  // pages
	  'modele'=>'',
	  'page'=>'banniere_preview',
	  'url_voir'=>'banniere',
	  'url_edit'=>'banniere_edit',
	  // textes
		'texte_retour' => 'icone_retour',
		'texte_objets' => 'pubban:icone_bannieres',
		'texte_objet' => 'pubban:icone_banniere',
		'texte_modifier' => 'pubban:icone_modifier_banniere',
		'texte_ajouter' => 'pubban:icone_nouvelle_banniere',
		'texte_creer' => 'pubban:icone_nouvelle_banniere',
		'info_aucun_objet'=> 'pubban:info_aucune_banniere',
		'info_1_objet' => 'pubban:info_1_banniere',
		'info_nb_objets' => 'pubban:info_nb_bannieres',
	  // table
		'principale' => 'oui',
		'field'=> array(
			"id_banniere"	=> "bigint(21) NOT NULL",
			"statut" => "varchar(100) NOT NULL default '1inactif'",
			"titre" => "varchar(30) NOT NULL default ''",
			"titre_id" => "varchar(30) NOT NULL default ''",
			"width" => "bigint(5) NOT NULL default '0'",
			"height" => "bigint(5) NOT NULL default '0'",
			"ratio_pages" => "int(3) NOT NULL default '0'",
			"refresh" => "bigint(5) NOT NULL default '0'",
			"maj" => "TIMESTAMP",
		),
		'key' => array(
			"PRIMARY KEY" 	=> "id_banniere",
			"KEY titre_id" 	=> "titre_id",
			"KEY statut" 	=> "statut"
		),
		'join' => array(
			"id_banniere"=>"id_banniere",
		),
		'titre' => "titre AS titre, '' AS lang",
 		// statut
		'texte_changer_statut' => 'pubban:info_statut_banniere_1',
    'statut_titres' => array(
			'1inactif' => 'pubban:info_titre_banniere_inactive',
			'2actif' => 'pubban:info_titre_banniere_active',
			'3poubelle' => 'pubban:info_titre_banniere_poubelle',
    ),
    'statut_textes_instituer' => array(
			'1inactif' => 'pubban:info_banniere_inactive',
			'2actif' => 'pubban:info_banniere_active',
			'3poubelle' => 'pubban:info_banniere_poubelle',
    ),
		'statut_images' => array(
			'1inactif' => 'puce-blanche.gif',
			'2actif' => 'puce-verte.gif',
			'3poubelle' => 'puce-poubelle.gif',
		),
    'statut'=> array(
			array(
				'champ' => 'statut',
				'publie' => '2actif',
				'previsu' => '1inactif,3poubelle',
				'post_date' => 'date', 
				'exception' => array('statut','tout')
			)
    ),
	);

	$tables['spip_pubban_stats'] = array(
	  // objet
	  'editable'=>'non',
	  // pages
	  'modele'=>'',
	  'page'=>'',
	  // table
		'principale' => 'oui',
		'field'=> array(
			"id_banniere"	=> "bigint(21) NOT NULL",
			"id_publicite" => "bigint(21) NOT NULL",
			"date" => "date NOT NULL default '0000-00-00'",
			"jour" => "int(3) NOT NULL",
			"clics" => "bigint(20) NOT NULL default '0'",
			"affichages" => "bigint(20) NOT NULL default '0'",
			"page" => "varchar(255) NOT NULL",
		),
		'key' => array(
			"KEY id_banniere" 	=> "id_banniere",
			"KEY id_publicite" 	=> "id_publicite",
		),
	);

	$tables['spip_bannieres_publicites'] = array(
	  'editable'=>'non',
		'principale' => 'non',
		'field'=> array(
			"id_publicite" => "bigint(21) NOT NULL",
			"id_banniere" => "bigint(21) NOT NULL"
		),
		'key' => array(
			"KEY id_banniere" 	=> "id_banniere",
			"KEY id_publicite" 	=> "id_publicite",
		),
	);

	return $tables;
}

?>
