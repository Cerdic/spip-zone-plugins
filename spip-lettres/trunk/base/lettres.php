<?php


/**
 * SPIP-Lettres
 *
 * Copyright (c) 2006-2009
 * Agence Artégo http://www.artego.fr
 *  
 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package SPIP\Lettres\Pipelines
 **/

global $table_des_abonnes;
$table_des_abonnes['abonne'] = array(
									'table'				=> 'abonnes',
									'url_prive'			=> 'abonnes_edit',
									'url_prive_titre'	=> _T('lettresprive:modifier_abonne'),
									'champ_id'			=> 'id_abonne',
									'champ_email'		=> 'email',
									'champ_nom'			=> 'nom'
									);
$table_des_abonnes['auteur'] = array(
									'table'				=> 'auteurs',
									'url_prive'			=> 'auteur_infos',
									'url_prive_titre'	=> _T('lettresprive:voir_fiche_auteur'),
									'champ_id'			=> 'id_auteur',
									'champ_email'		=> 'email',
									'champ_nom'			=> 'nom'
									);


/**
 * Déclarer les interfaces des tables pour le compilateur
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interface
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function lettres_declarer_tables_interfaces($interface) {
	$interface['table_des_tables']['abonnes'] = 'abonnes';
	$interface['table_des_tables']['abonnes_statistiques'] = 'abonnes_statistiques';
	$interface['table_des_tables']['lettres'] = 'lettres';
	$interface['table_des_tables']['lettres_statistiques'] = 'lettres_statistiques';
	$interface['table_des_tables']['themes'] = 'themes';
	$interface['tables_jointures']['spip_abonnes'][] = 'abonnes_lettres';
	$interface['tables_jointures']['spip_abonnes'][] = 'abonnes_rubriques';
	$interface['tables_jointures']['spip_abonnes'][] = 'abonnes_statistiques';
	$interface['tables_jointures']['spip_abonnes'][] = 'rubriques';
	$interface['tables_jointures']['spip_abonnes'][] = 'abonnes_clics';
	$interface['tables_jointures']['spip_abonnes'][] = 'clics';
	$interface['tables_jointures']['spip_abonnes'][] = 'auteurs';
	$interface['tables_jointures']['spip_articles'][] = 'articles_lettres';
	$interface['tables_jointures']['spip_articles'][] = 'lettres';
	$interface['tables_jointures']['spip_lettres'][] = 'articles_lettres';
	$interface['tables_jointures']['spip_lettres'][] = 'articles';
	$interface['tables_jointures']['spip_lettres'][] = 'lettres_statistiques';
	$interface['tables_jointures']['spip_lettres'][] = 'rubriques';
	$interface['tables_jointures']['spip_lettres'][] = 'abonnes_lettres';
	$interface['tables_jointures']['spip_lettres'][] = 'documents_liens';
	$interface['tables_jointures']['spip_themes'][] = 'rubriques';
	$interface['tables_jointures']['spip_themes']['expediteur_id'] = 'auteurs';
	$interface['tables_jointures']['spip_themes']['retours_id'] = 'auteurs';
	$interface['table_date']['abonnes']	= 'maj';
	$interface['table_date']['lettres']	= 'date';
	$interface['table_des_traitements']['URL_FORMULAIRE_LETTRES'][] = 'quote_amp(%s)';
	$interface['table_des_traitements']['URL_LETTRE'][] = 'quote_amp(%s)';
	return $interface;
}

/**
 * Déclarer les objets éditoriaux des lettres
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function lettres_declarer_tables_objets_sql($tables) {

	//-- Table lettres
	$tables['spip_lettres'] = array(
		'type' => 'lettre',

		'titre' => "titre, lang",
		'date' => 'date',
		'principale' => 'oui',

		'field' => array(
			"id_lettre"				=> "BIGINT(21) NOT NULL",
			"id_rubrique"			=> "BIGINT(21) NOT NULL",
			"id_secteur"			=> "BIGINT(21) NOT NULL",
			"titre"					=> "TEXT NOT NULL DEFAULT ''",
			"descriptif"			=> "TEXT NOT NULL DEFAULT ''",
			"chapo"					=> "MEDIUMTEXT NOT NULL DEFAULT ''",
			"texte"					=> "longtext DEFAULT '' NOT NULL",
			"ps"					=> "TEXT NOT NULL DEFAULT ''",
			"date"					=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"lang"					=> "VARCHAR(10) NOT NULL DEFAULT ''",
			"langue_choisie"		=> "VARCHAR(3) DEFAULT 'non'",
			"maj"					=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"message_html"			=> "longtext DEFAULT '' NOT NULL",
			"message_texte"			=> "longtext DEFAULT '' NOT NULL",
			"date_debut_envoi"		=> "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"date_fin_envoi"		=> "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"statut"				=> "VARCHAR(15) NOT NULL DEFAULT 'brouillon'",
			"extra"					=> "longtext NULL"
		),
		'key' => array(
			"PRIMARY KEY"	=> "id_lettre",
		),
		'rechercher_champs' => array(
			'titre' => 8,'descriptif' => 4, 'chapo' => 3, 'texte' => 2, 'ps' => 1
		),
	);
	return $tables;
}

function lettres_declarer_tables_principales($tables_principales) {
	$spip_abonnes = array(
						"id_abonne"	=> "BIGINT(21) NOT NULL",
						"objet"		=> "VARCHAR(255) NOT NULL DEFAULT 'abonne'",
						"id_objet"	=> "BIGINT(21) NOT NULL",
						"email"		=> "VARCHAR(255) NOT NULL DEFAULT ''",
						"code"		=> "VARCHAR(255) NOT NULL DEFAULT ''",
						"nom"		=> "VARCHAR(255) NOT NULL DEFAULT ''",
						"format"	=> "ENUM('html','texte','mixte') NOT NULL DEFAULT 'mixte'",
						"maj"		=> "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
						"extra"		=> "LONGBLOB NULL"
					);
	$spip_abonnes_key = array(
						"PRIMARY KEY" 	=> "id_abonne",
						"KEY code"	=> "code"
					);
	$spip_clics = array(
						"id_clic"		=> "BIGINT(21) NOT NULL",
						"id_lettre"		=> "BIGINT(21) NOT NULL",
						"url"			=> "VARCHAR(255) NOT NULL"
					);
	$spip_clics_key = array(
						"PRIMARY KEY"	=> "id_clic",
						"KEY lettre"	=> "id_lettre, url"
					);
	$spip_desabonnes = array(
						"id_desabonne"	=> "BIGINT(21) NOT NULL",
						"email"			=> "VARCHAR(255) NOT NULL DEFAULT ''"
					);
	$spip_desabonnes_key = array(
						"PRIMARY KEY" 	=> "id_desabonne",
						"KEY email"	=> "email"
					);
	$spip_rubriques_crontabs = array(
						"id_rubrique"			=> "BIGINT (21) DEFAULT '0' NOT NULL",
						"titre"					=> "TEXT NOT NULL"
					);
	$spip_rubriques_crontabs_key = array(
						"KEY id_rubrique"		=> "id_rubrique"
					);
	$spip_themes = array(
						"id_theme"					=> "BIGINT(21) NOT NULL",
						"id_rubrique"				=> "BIGINT(21) DEFAULT '0' NOT NULL",
						"titre"						=> "TEXT NOT NULL",
						"lang"						=> "VARCHAR(10) NOT NULL DEFAULT ''",
						"expediteur_type"			=> "ENUM('default','webmaster','author','custom') NOT NULL DEFAULT 'default'",
						"expediteur_id"				=> "BIGINT(21) NOT NULL DEFAULT '0'",
						"retours_type"				=> "ENUM('default','webmaster','author','custom') NOT NULL DEFAULT 'default'",
						"retours_id"				=> "BIGINT(21) NOT NULL DEFAULT '0'"
					);
	$spip_themes_key = array(
						"PRIMARY KEY"			=> "id_theme",
						"KEY id_rubrique"	=> "id_rubrique"
					);
	$tables_principales['spip_abonnes'] =
		array('field' => &$spip_abonnes, 'key' => &$spip_abonnes_key);
	$tables_principales['spip_clics'] =
		array('field' => &$spip_clics, 'key' => &$spip_clics_key);
	$tables_principales['spip_desabonnes'] =
		array('field' => &$spip_desabonnes, 'key' => &$spip_desabonnes_key);
	$tables_principales['spip_rubriques_crontabs'] =
		array('field' => &$spip_rubriques_crontabs, 'key' => &$spip_rubriques_crontabs_key);
	$tables_principales['spip_themes'] =
		array('field' => &$spip_themes, 'key' => &$spip_themes_key);
	return $tables_principales;
}


function lettres_declarer_tables_auxiliaires($tables_auxiliaires) {
	$spip_abonnes_clics = array(
						"id_abonne"		=> "BIGINT(21) NOT NULL",
						"id_clic"		=> "BIGINT(21) NOT NULL",
						"id_lettre"		=> "BIGINT(21) NOT NULL"
					);
	$spip_abonnes_clics_key = array();
	$spip_abonnes_lettres = array(
						"id_abonne"		=> "BIGINT(21) NOT NULL DEFAULT '0'",
						"id_lettre" 	=> "BIGINT(21) NOT NULL DEFAULT '0'",
						"statut"		=> "ENUM('a_envoyer','envoye','echec','annule') NOT NULL DEFAULT 'a_envoyer'",
						"format"		=> "ENUM('mixte','html','texte') NOT NULL DEFAULT 'mixte'",
						"verrou"		=> "TINYINT NOT NULL DEFAULT '0'",
						"maj"			=> "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'"
					);
	$spip_abonnes_lettres_key = array(
						"PRIMARY KEY"	=> "id_abonne, id_lettre"
					);
	$spip_abonnes_rubriques = array(
						"id_abonne"			=> "BIGINT(21) NOT NULL DEFAULT '0'",
						"id_rubrique" 		=> "BIGINT(21) NOT NULL DEFAULT '0'",
						"date_abonnement"	=> "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
						"statut"			=> "ENUM('a_valider','valide') NOT NULL DEFAULT 'a_valider'"
					);
	$spip_abonnes_rubriques_key = array(
						"PRIMARY KEY" => "id_abonne, id_rubrique"
					);
	$spip_abonnes_statistiques = array(
						"periode"				=> "VARCHAR(7) NOT NULL",
						"nb_inscriptions"		=> "BIGINT(21) DEFAULT '0' NOT NULL",
						"nb_desinscriptions"	=> "BIGINT(21) DEFAULT '0' NOT NULL"
					);
	$spip_abonnes_statistiques_key = array(
						"PRIMARY KEY"	=> "periode"
					);
	$spip_articles_lettres = array(
						"id_article"	=> "BIGINT(21) NOT NULL",
						"id_lettre"		=> "BIGINT(21) NOT NULL"
					);
	$spip_articles_lettres_key = array(
						"PRIMARY KEY" 		=> "id_article, id_lettre",
						"KEY id_article"	=> "id_article",
						"KEY id_lettre"		=> "id_lettre"
					);
	$spip_lettres_statistiques = array(
						"periode"		=> "VARCHAR(7) NOT NULL",
						"nb_envois"		=> "BIGINT(21) DEFAULT '0' NOT NULL"
					);
	$spip_lettres_statistiques_key = array(
						"PRIMARY KEY"	=> "periode"
					);
	$tables_auxiliaires['spip_abonnes_clics'] = 
		array('field' => &$spip_abonnes_clics, 'key' => &$spip_abonnes_clics_key);
	$tables_auxiliaires['spip_abonnes_lettres'] = 
		array('field' => &$spip_abonnes_lettres, 'key' => &$spip_abonnes_lettres_key);
	$tables_auxiliaires['spip_abonnes_rubriques'] = 
		array('field' => &$spip_abonnes_rubriques, 'key' => &$spip_abonnes_rubriques_key);
	$tables_auxiliaires['spip_abonnes_statistiques'] = 
		array('field' => &$spip_abonnes_statistiques, 'key' => &$spip_abonnes_statistiques_key);
	$tables_auxiliaires['spip_articles_lettres'] = 
		array('field' => &$spip_articles_lettres, 'key' => &$spip_articles_lettres_key);
	$tables_auxiliaires['spip_lettres_statistiques'] = 
		array('field' => &$spip_lettres_statistiques, 'key' => &$spip_lettres_statistiques_key);
	return $tables_auxiliaires;
}


?>
