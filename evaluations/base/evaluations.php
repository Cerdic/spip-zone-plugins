<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Évaluations
 * @copyright  2013
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Evaluations\Pipelines
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
function evaluations_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['evaluations'] = 'evaluations';
	$interfaces['table_des_tables']['evaluations_criteres'] = 'evaluations_criteres';
	$interfaces['table_des_tables']['evaluations_critiques'] = 'evaluations_critiques';
	$interfaces['table_des_tables']['evaluations_syntheses'] = 'evaluations_syntheses';

	$interfaces['table_des_traitements']['SYNTHESE']['evaluations_syntheses']= _TRAITEMENT_RACCOURCIS;

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
function evaluations_declarer_tables_objets_sql($tables) {

	$tables['spip_evaluations'] = array(
		'type' => 'evaluation',
		'principale' => "oui",
		'field'=> array(
			"id_evaluation"      => "bigint(21) NOT NULL",
			"identifiant"        => "varchar(30) NOT NULL DEFAULT ''",
			"titre"              => "tinytext NOT NULL DEFAULT ''",
			"texte"              => "mediumtext NOT NULL DEFAULT ''",
			"date_debut"         => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"date_fin"           => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_evaluation",
			"KEY statut"         => "statut", 
		),
		'titre' => "titre AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array('identifiant', 'titre', 'texte', 'date_debut', 'date_fin'),
		'champs_versionnes' => array('identifiant', 'titre', 'texte', 'date_debut', 'date_fin'),
		'rechercher_champs' => array("titre" => 7, "texte" => 1),
		'tables_jointures'  => array('spip_evaluations_liens'),
		'statut_textes_instituer' => array(
			'prepa'    => 'texte_statut_en_cours_redaction',
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
		'texte_changer_statut' => 'evaluation:texte_changer_statut_evaluation',

	);

	$tables['spip_evaluations_criteres'] = array(
		'type' => 'evaluations_critere',
		'principale' => "oui", 
		'table_objet_surnoms' => array('evaluationscritere'), // table_objet('evaluations_critere') => 'evaluations_criteres' 
		'field'=> array(
			"id_evaluations_critere" => "bigint(21) NOT NULL",
			"id_evaluation"      => "bigint(21) NOT NULL DEFAULT 0",
			"titre"              => "tinytext NOT NULL DEFAULT ''",
			"texte"              => "mediumtext NOT NULL DEFAULT ''",

			"noter"              => "varchar(3) NOT NULL DEFAULT ''",
			"aide_noter"         => "tinytext NOT NULL DEFAULT ''",
			"note_mini"          => "int(4) NOT NULL DEFAULT 0",
			"note_maxi"          => "int(4) NOT NULL DEFAULT 0",
			"ponderation"        => "int(3) NOT NULL DEFAULT 1",

			"commenter"          => "varchar(3) NOT NULL DEFAULT ''",
			"aide_commenter"     => "tinytext NOT NULL DEFAULT ''",
			"evaluer"            => "varchar(3) NOT NULL DEFAULT ''",
			"aide_evaluer"       => "tinytext NOT NULL DEFAULT ''",

			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_evaluations_critere",
		),
		'titre' => "titre AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array('id_evaluation', 'titre', 'texte', 'noter', 'commenter', 'evaluer', 'note_mini', 'note_maxi', 'ponderation', 'aide_noter', 'aide_commenter', 'aide_evaluer'),
		'champs_versionnes' => array('id_evaluation', 'titre', 'texte', 'noter', 'commenter', 'evaluer', 'note_mini', 'note_maxi', 'ponderation', 'aide_noter', 'aide_commenter', 'aide_evaluer'),
		'rechercher_champs' => array(),
		'tables_jointures'  => array(),

	);

	$tables['spip_evaluations_critiques'] = array(
		'type' => 'evaluations_critique',
		'principale' => "oui", 
		'table_objet_surnoms' => array('evaluationscritique'), // table_objet('evaluations_critique') => 'evaluations_critiques' 
		'field'=> array(
			"id_evaluations_critique" => "bigint(21) NOT NULL",
			"id_evaluation"      => "bigint(21) NOT NULL DEFAULT 0",
			"id_evaluations_critere" => "bigint(21) NOT NULL DEFAULT 0",
			"objet"              => "varchar(30) NOT NULL DEFAULT ''",
			"id_objet"           => "int(11) NOT NULL DEFAULT 0",
			"id_auteur"          => "int(11) NOT NULL DEFAULT 0",
			"note"               => "int(4) NOT NULL DEFAULT 0",
			"commentaire"        => "mediumtext NOT NULL DEFAULT ''",
			"forces"             => "tinytext NOT NULL DEFAULT ''",
			"faiblesses"         => "tinytext NOT NULL DEFAULT ''",
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_evaluations_critique",
		),
		'titre' => "commentaire AS titre, '' AS lang",
		'date' => "date",
		'champs_editables'  => array('id_evaluation', 'id_evaluations_critere', 'objet', 'id_objet', 'id_auteur', 'note', 'commentaire', 'forces', 'faiblesses'),
		'champs_versionnes' => array('id_evaluation', 'id_evaluations_critere', 'objet', 'id_objet', 'id_auteur', 'note', 'commentaire', 'forces', 'faiblesses'),
		'rechercher_champs' => array(),
		'tables_jointures'  => array(),

	);

	$tables['spip_evaluations_syntheses'] = array(
		'type' => 'evaluations_synthese',
		'principale' => "oui", 
		'table_objet_surnoms' => array('evaluationssynthese'), // table_objet('evaluations_synthese') => 'evaluations_syntheses' 
		'field'=> array(
			"id_evaluations_synthese" => "bigint(21) NOT NULL",
			"id_evaluation"      => "int(11) NOT NULL DEFAULT 0",
			"objet"              => "varchar(25) NOT NULL DEFAULT ''",
			"id_objet"           => "int(11) NOT NULL DEFAULT 0",
			"note"               => "int(3) NOT NULL DEFAULT 0",
			"synthese"           => "text NOT NULL DEFAULT ''",
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_evaluations_synthese",
		),
		'titre' => "synthese AS titre, '' AS lang",
		'date' => "date",
		'champs_editables'  => array('id_evaluation', 'objet', 'id_objet', 'note', 'synthese'),
		'champs_versionnes' => array('id_evaluation', 'objet', 'id_objet', 'note', 'synthese'),
		'rechercher_champs' => array("synthese" => 1),
		'tables_jointures'  => array(),

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
function evaluations_declarer_tables_auxiliaires($tables) {

	$tables['spip_evaluations_liens'] = array(
		'field' => array(
			"id_evaluation"      => "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"           => "bigint(21) DEFAULT '0' NOT NULL",
			"objet"              => "VARCHAR(25) DEFAULT '' NOT NULL",
			"vu"                 => "VARCHAR(6) DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_evaluation,id_objet,objet",
			"KEY id_evaluation"  => "id_evaluation"
		)
	);

	return $tables;
}


?>
