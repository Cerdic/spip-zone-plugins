<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Bouquinerie
 * @copyright  2017
 * @author     Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\Bouquinerie\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function bouq_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['livres'] = 'livres';
	$interfaces['table_des_tables']['livres_auteurs'] = 'livres_auteurs';

	/* Livres */
	$interfaces['table_des_traitements']['TRADUCTION'][] = _TRAITEMENT_RACCOURCIS;
	$interfaces['table_des_traitements']['EXTRAIT'][] = _TRAITEMENT_RACCOURCIS;

	/* Auteurs de livre */
	$interfaces['table_des_traitements']['BIOGRAPHIE'][] = _TRAITEMENT_RACCOURCIS;

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
function bouq_declarer_tables_objets_sql($tables) {

	$tables['spip_livres'] = array(
		'type' => 'livre',
		'principale' => 'oui',
		'field'=> array(
			'id_livre'           => 'bigint(21) NOT NULL',
			'id_rubrique'        => 'bigint(21) NOT NULL DEFAULT 0',
			'id_secteur'         => 'bigint(21) NOT NULL DEFAULT 0',
			'titre'              => 'text NOT NULL DEFAULT ""',
			'soustitre'          => 'text NOT NULL DEFAULT ""',
			'volume'             => 'text NOT NULL DEFAULT ""',
			'edition'            => 'text NOT NULL DEFAULT ""',
			'traduction'         => 'text NOT NULL DEFAULT ""',
			'texte'              => 'text NOT NULL DEFAULT ""',
			'extrait'            => 'text NOT NULL DEFAULT ""',
			'infos_sup'          => 'text NOT NULL DEFAULT ""',
			'isbn'               => 'varchar(13) NOT NULL DEFAULT ""',
			'pages'              => 'smallint(6)',
			'reliure'            => 'varchar(100) NOT NULL DEFAULT ""',
			'largeur'            => 'int(6) NOT NULL DEFAULT 0',
			'hauteur'            => 'int(6) NOT NULL DEFAULT 0',
			'poids'              => 'int(6) NOT NULL DEFAULT 0',
			'prix'               => 'int(6) NOT NULL DEFAULT 0',
			'date_parution'      => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'date_nouvelle_edition' => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'statut'             => 'varchar(20)  DEFAULT "0" NOT NULL',
			'lang'               => 'VARCHAR(10) NOT NULL DEFAULT ""',
			'langue_choisie'     => 'VARCHAR(3) DEFAULT "non"',
			'id_trad'            => 'bigint(21) NOT NULL DEFAULT 0',
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_livre',
			'KEY id_rubrique'    => 'id_rubrique',
			'KEY id_secteur'     => 'id_secteur',
			'KEY lang'           => 'lang',
			'KEY id_trad'        => 'id_trad',
			'KEY statut'         => 'statut',
		),
		'titre' => 'titre AS titre, lang AS lang',
		'date' => 'date_parution',
		'champs_editables'  => array('titre', 'soustitre', 'volume', 'edition', 'traduction', 'texte', 'extrait', 'infos_sup', 'isbn', 'pages', 'reliure', 'largeur', 'hauteur', 'poids', 'prix', 'date_parution', 'date_nouvelle_edition', 'id_rubrique', 'id_secteur'),
		'champs_versionnes' => array('isbn', 'id_rubrique', 'id_secteur'),
		'rechercher_champs' => array("titre" => 10, "soustitre" => 8),
		'tables_jointures'  => array(),
		// stand by 'tables_jointures'  => array('spip_livres_liens'),
		'statut_textes_instituer' => array(
			'prepa'    => 'texte_statut_en_cours_redaction',
			'prop'     => 'livre:texte_statut_aparaitre',
			'publie'   => 'livre:texte_statut_paru',
			'refuse'   => 'livre:texte_statut_epuise',
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
		'texte_changer_statut' => 'livre:texte_changer_statut_livre',
		'join' => array('id_livre'=>'id_livre'),
		'roles_colonne' => 'role',
		'roles_titres' => array(
			'grand_format'=>'livre:role_grand_format',
			'poche'=>'livre:role_poche',
		),
		'roles_objets' => array(
			'livres' => array(
				'choix' => array('grand_format', 'poche'),
				'defaut' => 'grand_format'),
		),

	);

	$tables['spip_livres_auteurs'] = array(
		'type' => 'livres_auteur',
		'principale' => 'oui',
		'table_objet_surnoms' => array('livresauteur'), // table_objet('livres_auteur') => 'livres_auteurs' 
		'field'=> array(
			'id_livres_auteur'   => 'bigint(21) NOT NULL',
			'nom'                => 'text NOT NULL DEFAULT ""',
			'prenom'             => 'text NOT NULL DEFAULT ""',
			'biographie'         => 'text NOT NULL DEFAULT ""',
			'lien_titre'         => 'text NOT NULL DEFAULT ""',
			'lien_url'           => 'text NOT NULL DEFAULT ""',
			'statut'             => 'varchar(20)  DEFAULT "0" NOT NULL',
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_livres_auteur',
			'KEY statut'         => 'statut',
		),
		'titre' => "CONCAT(prenom,' ',nom) AS titre, '' AS lang",
		 #'date' => '',
		'champs_editables'  => array('nom', 'prenom', 'biographie', 'lien_titre', 'lien_url'),
		'champs_versionnes' => array(),
		'rechercher_champs' => array("nom" => 8, "prenom" => 6, "biographie" => 2),
		'tables_jointures'  => array('spip_livres_auteurs_liens'),
		'statut_textes_instituer' => array(
			'prepa'    => 'texte_statut_en_cours_redaction',
			'publie'   => 'texte_statut_publie',
			'poubelle' => 'texte_statut_poubelle',
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'publie',
				'previsu'   => 'publie,prepa',
				'post_date' => 'date',
				'exception' => array('statut','tout')
			)
		),
		'texte_changer_statut' => 'livres_auteur:texte_changer_statut_livres_auteur',

		'roles_colonne' => 'role',
		'roles_titres' => array(
			'ecrivain'=>'livres_auteur:role_ecrivain',
			'traducteur'=>'livres_auteur:role_traducteur',
			'illustrateur'=>'livres_auteur:role_illustrateur',
			'prefacier'=>'livres_auteur:role_prefacier',
			'postfacier'=>'livres_auteur:role_postfacier',
		),
		'roles_objets' => array(
			'livres' => array(
				'choix' => array('ecrivain', 'traducteur', 'illustrateur', 'prefacier', 'postfacier'),
				'defaut' => 'ecrivain'),
		),

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
function bouq_declarer_tables_auxiliaires($tables) {

	$tables['spip_livres_liens'] = array(
		'field' => array(
			'id_livre'           => 'bigint(21) DEFAULT "0" NOT NULL',
			'id_objet'           => 'bigint(21) DEFAULT "0" NOT NULL',
			'objet'              => 'VARCHAR(25) DEFAULT "" NOT NULL',
			'role'               => 'VARCHAR(25) DEFAULT ""',
			'vu'                 => 'VARCHAR(6) DEFAULT "non" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_livre,id_objet,objet,role',
		)
	);
	$tables['spip_livres_auteurs_liens'] = array(
		'field' => array(
			'id_livres_auteur'   => 'bigint(21) DEFAULT "0" NOT NULL',
			'id_objet'           => 'bigint(21) DEFAULT "0" NOT NULL',
			'objet'              => 'VARCHAR(25) DEFAULT "" NOT NULL',
			'role'               => 'VARCHAR(25) DEFAULT ""',
			'vu'                 => 'VARCHAR(6) DEFAULT "non" NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_livres_auteur,id_objet,objet,role',
		)
	);

	return $tables;
}
