<?php
/**
 * Plugin Diogene
 *
 * Auteurs :
 * b_b
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * Distribue sous licence GNU/GPL
 *
 * Déclaration des tables pour Diogene
 *
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

function diogene_declarer_tables_interfaces($interfaces){
	$interfaces['table_des_tables']['diogenes']='diogenes';
	$interfaces['table_des_traitements']['DESCRIPTION'][]= _TRAITEMENT_RACCOURCIS;
	return $interfaces;
}

/**
 * Insertion dans le pipeline declarer_tables_auxiliaires (SPIP)
 * 
 * Declaration de la table auxiliaire spip_diogene_liens
 *
 * @param array $tables_auxiliaires
 * 	Un tableau de description des tables auxiliaires
 * @return array $tables_auxiliaires
 * 	Le tableau des tables auxiliaires complété
 */
function diogene_declarer_tables_auxiliaires($tables_auxiliaires){
	$spip_diogenes_liens = array(
		"id_diogene"	=> "bigint(21) NOT NULL",
		"id_objet"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"objet"	=> "VARCHAR (25) DEFAULT '' NOT NULL");

	$spip_diogenes_liens_key = array(
		"PRIMARY KEY"		=> "id_diogene,id_objet,objet");

	$tables_auxiliaires['spip_diogenes_liens'] = array(
		'field' => &$spip_diogenes_liens,
		'key' => &$spip_diogenes_liens_key);

	return $tables_auxiliaires;
}

/**
 * Insertion dans le pipeline declarer_tables_objets_sql (SPIP)
 * 
 * Modification de la description des tables SQL :
 * - On ajoute les champs "lang" et "langue_choisie" dans les champs éditables de la table spip_articles
 * - On ajoute notre nouvel objet "diogene"
 * 
 * @param array $tables
 * 	La description complète des objets SPIP
 * @return array $tables
 * 	La description des objets SPIP modifiée
 */
function diogene_declarer_tables_objets_sql($tables){
	$tables['spip_articles']['champs_editables'][] = 'lang';
	$tables['spip_articles']['champs_editables'][] = 'langue_choisie';
	$tables['spip_diogenes'] = array(
		'texte_retour' => 'icone_retour',
		'texte_objets' => 'diogene:diogenes',
		'texte_objet' => 'diogene:diogene',
		'texte_modifier' => 'diogene:icone_modifier_diogene',
		'texte_creer' => 'diogene:icone_nouveau_diogene',
		'info_aucun_objet'=> 'diogene:info_aucun_diogene',
		'info_1_objet' => 'diogene:info_1_diogene',
		'info_nb_objets' => 'diogene:info_nb_diogenes',
		'texte_logo_objet' => 'diogene:libelle_logo_diogene',
		'titre' => 'titre',
		'date' => 'maj',
		'principale' => 'oui',
		'field'=> array(
			"id_diogene"	=> "bigint(21) NOT NULL",
			"titre"	=> "text DEFAULT '' NOT NULL",
			"objet" => "varchar(25) DEFAULT '' NOT NULL",
			"id_secteur"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"id_rubrique_defaut"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"champs_caches"	=> "text DEFAULT '' NOT NULL",
			"champs_ajoutes"	=> "text DEFAULT '' NOT NULL",
			"type" => "varchar(25) DEFAULT '' NOT NULL",
			"description"	=> "mediumtext DEFAULT '' NOT NULL",
			"statut_auteur"	=> "text DEFAULT '' NOT NULL",
			"statut_auteur_publier" => "text DEFAULT '' NOT NULL",
			"options_complements"	=> "text DEFAULT '' NOT NULL",
			"nombre_attente" => "int DEFAULT '0' NOT NULL",
			"menu" => "varchar(3) DEFAULT '' NOT NULL",
			"id_auteur"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"maj"	=> "TIMESTAMP"
		),
		'champs_editables' => array('titre', 'objet', 'id_secteur', 'id_rubrique_defaut', 'champs_caches','champs_ajoutes', 'type', 'description', 'statut_auteur', 'statut_auteur_publier','options_complements','menu','nombre_attente','id_auteur'),
		'key' => array(
			"PRIMARY KEY"	=> "id_diogene",
			"KEY id_auteur"	=> "id_auteur",
			"KEY id_secteur" => "id_secteur",
			"KEY id_secteur" => "id_rubrique_defaut",
			"KEY objet"	=> "objet",
			"KEY type"	=> "type"
		),
		'join' => array(
			"id_diogene"=>"id_diogene",
			"id_auteur"=>"id_auteur"
		),
		'rechercher_champs' => array(
		  'titre' => 8, 'description' => 2, 'objet' => 1, 'type' => 1
		),
		'champs_versionnes' => array('id_secteur','id_rubrique_defaut','champs_caches','champs_ajoutes','statut_auteur','statut_auteur_publier','options_complements','menu','id_auteur', 'titre', 'description', 'nombre_attente','objet', 'type'),
	);
	
	// jointures sur les diogenes pour tous les objets
	$tables[]['tables_jointures'][]= 'diogenes_liens';
	$tables[]['tables_jointures'][]= 'diogenes';
	
	// recherche jointe sur les diogenes pour tous les objets
	$tables[]['rechercher_jointures']['diogenes'] = array('titre' => 3);
	
	// versionner les jointures pour tous les objets
	$tables[]['champs_versionnes'][] = 'jointure_diogenes';
	
	return $tables;
}
?>
