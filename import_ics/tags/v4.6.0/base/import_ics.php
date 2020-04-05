<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Import_ics
 * @copyright  2013
 * @author     Amaury
 * @licence    GNU/GPL
 * @package    SPIP\Import_ics\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function import_ics_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['almanachs'] = 'almanachs';
	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function import_ics_declarer_tables_objets_sql($tables) {

	$tables['spip_almanachs'] = array(
		'type' => 'almanach',
		'principale' => "oui",
		'field'=> array(
			"id_almanach"        => "bigint(21) NOT NULL",
			"titre"              => "text NOT NULL DEFAULT ''",
			"url"                => "text NOT NULL DEFAULT ''",
			"id_article"         => "bigint(21) NOT NULL DEFAULT 0",
			"decalage_ete"       => "tinyint NOT NULL DEFAULT 0",
			"decalage_hiver"     => "tinyint NOT NULL DEFAULT 0",
			"dtend_inclus"       => "varchar(10) DEFAULT '0' NOT NULL",
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"statut"             => "varchar(20) DEFAULT '0' NOT NULL",
			"maj"                => "TIMESTAMP",
			"derniere_synchro"	 => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"derniere_erreur"	 	 => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_almanach",
			"KEY statut"         => "statut",
		),
		'titre' => "titre AS titre, '' AS lang",
		'date' => "date",
		'champs_editables'  => array('titre', 'url', 'id_article',"decalage_ete","decalage_hiver",'dtend_inclus'),
		'champs_versionnes' => array('titre', 'url', 'id_article',"decalage_ete","decalage_hiver",'dtend_inclus'),
		'rechercher_champs' => array(),
		'tables_jointures'  => array('spip_almanachs_liens'),
		'statut_textes_instituer' => array(
			'prop'     => 'texte_statut_propose_evaluation',
			'publie'   => 'texte_statut_publie',
			'refuse'   => 'texte_statut_refuse',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'publie',
				'previsu'   => 'publie,prop,prepa',
				'post_date' => 'date',
				'exception' => array('statut','tout')
			)
		),
		'texte_changer_statut' => 'almanach:texte_changer_statut_almanach',


	);

	// les jointures automatiques
	$tables['spip_evenements']['tables_jointures'][] = 'almanachs_liens';
	$tables['spip_evenements']['tables_jointures'][] = 'almanachs';
	$tables['spip_almanachs']['tables_jointures'][] = 'almanachs_liens';
	$tables['spip_almanachs']['tables_jointures'][] = 'almanachs_liens';


	// ajout du statut archive sur les evenements
	$tables["spip_evenements"]["statut_titres"]["archive"] = "import_ics:archive";
	$tables["spip_evenements"]["statut_textes_instituer"]["archive"] = "import_ics:archiver";
	$tables["spip_evenements"]["statut_images"] = array(
		"archive"=>"puce-refuser-8.png",
		"publie"=>"puce-publier-8.png",
		"prop"=>"puce-proposer-8.png",
		"poubelle"=>"puce-supprimer-8.png"
	);

	return $tables;
}


/**
 * Déclaration des tables secondaires (liaisons)
 *
 * @pipeline declarer_tables_auxiliaires
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function import_ics_declarer_tables_auxiliaires($tables) {

	$tables['spip_almanachs_liens'] = array(
		'field' => array(
			"id_almanach"        => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"           => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"              => "VARCHAR(25) DEFAULT '' NOT NULL",
			"vu"                 => "VARCHAR(6) DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_almanach,id_objet,objet",
			"KEY id_almanach"    => "id_almanach"
		)
	);

	return $tables;
}

/**
 * Déclaration des champs extra
 *
 * @pipeline declarer_tables_auxiliaires
 * @param array $champs
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function import_ics_declarer_champs_extras($champs = array()){
	$champs['spip_evenements']['attendee'] = array(
		'saisie' => 'input',// type de saisie
		'options' => array(
			'nom' => 'attendee',
			'label' => _T('almanach:attendee'),
			'sql' => "varchar(256) NOT NULL DEFAULT ''", // declaration sql
			'rechercher'=>true,
			'defaut' => '',
	));
	$champs['spip_evenements']['origin'] = array(
		'saisie' => 'input',
		'options' => array(
			'nom' => 'origin', // nom sql
			'label' => _T('almanach:origin'),
			'sql' => "varchar(256) NOT NULL DEFAULT ''", // declaration sql
			'rechercher'=>true,
			'defaut' => '',
	));
	$champs['spip_evenements']['notes'] = array(
		'saisie' => 'textarea',
		'options' => array(
			'nom' => 'notes', // nom sql
			'label' => _T('almanach:notes'),
			'sql' => "text NOT NULL DEFAULT ''", // declaration sql
			'rechercher'=>true,
			'defaut' => '',
			'rows' => 4,
			'traitements' => '_TRAITEMENT_RACCOURCIS',
			'class'	=>'inserer_barre_edition',
	));
	$champs['spip_evenements']['uid'] = array(
		'saisie' => 'hidden',
		'options' => array(
			'nom' => 'uid', // nom sql
			'label' => _T('almanach:uid'),
			'sql' => "text NOT NULL", // declaration sql
			'rechercher'=>false,
			'defaut' => '',
			'rows' => 4,
			'versionner' => true,
			'modifier' => array()
	));
	$champs['spip_evenements']['sequence'] = array(
		'saisie' => 'hidden',
		'options' => array(
			'nom' => 'sequence', // nom sql
			'label' => _T('almanach:sequence'),
			'sql' => "bigint(21) DEFAULT '0' NOT NULL", // declaration sql
			'rechercher'=>false,
			'defaut' => '',
			'rows' => 4,
			'versionner' => true,
			'modifier' => array()
	));
	$champs['spip_evenements']['last_modified_distant'] = array(
		'saisie' => 'hidden',
		'options' => array(
			'nom' => 'last_modified_distant', // nom sql
			'label' => _T('almanach:last_modified_distant'),
			'sql' => "last_modified_distant text NOT NULL", // declaration sql
			'rechercher'=>false,
			'defaut' => '',
			'rows' => 4,
			'versionner' => true,
			'modifier' => array(),
			'traitements' => 'affdate_heure(date_ical_to_sql(unserialize(%s),"",True))'
	));
	return $champs;
}

?>
