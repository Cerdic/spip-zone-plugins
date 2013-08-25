<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function gis_declarer_tables_interfaces($interface){
	$interface['tables_jointures']['spip_gis'][] = 'gis_liens';
	$interface['tables_jointures']['spip_gis_liens'][] = 'gis';
	$interface['tables_jointures']['spip_articles'][] = 'gis_liens';
	$interface['tables_jointures']['spip_auteurs'][] = 'gis_liens';
	$interface['tables_jointures']['spip_breves'][] = 'gis_liens';
	$interface['tables_jointures']['spip_documents'][] = 'gis_liens';
	$interface['tables_jointures']['spip_groupes_mots'][] = 'gis_liens';
	$interface['tables_jointures']['spip_mots'][] = 'gis_liens';
	$interface['tables_jointures']['spip_rubriques'][] = 'gis_liens';
	$interface['tables_jointures']['spip_syndic'][] = 'gis_liens';

	$interface['table_des_tables']['gis'] = 'gis';
	$interface['table_des_tables']['gis_liens'] = 'gis_liens';

	// Traitements typo et raccourcis
	$interface['table_des_traitements']['TITRE_GIS'][] = 'typo(extraire_multi(%s))';
	$interface['table_des_traitements']['DESCRIPTIF_GIS'][] = _TRAITEMENT_RACCOURCIS;
	$interface['table_des_traitements']['VILLE_GIS'][] = 'typo(extraire_multi(%s))';
	$interface['table_des_traitements']['PAYS_GIS'][] = 'typo(extraire_multi(%s))';
	$interface['table_des_traitements']['REGION_GIS'][] = 'typo(extraire_multi(%s))';
	$interface['table_des_traitements']['VILLE'][] = 'typo(extraire_multi(%s))';
	$interface['table_des_traitements']['PAYS'][] = 'typo(extraire_multi(%s))';
	$interface['table_des_traitements']['REGION'][] = 'typo(extraire_multi(%s))';

	return $interface;
}

function gis_declarer_tables_objets_sql($tables){
	/* Declaration de la table de points gis */
	$tables['spip_gis'] = array(
		/* Declarations principales */
		'table_objet' => 'gis',
		'table_objet_surnoms' => array('gis'),
		'type' => 'gis',
		'type_surnoms' => array('gi'),

		/* La table */
		'field' => array(
			"id_gis" => "bigint(21) NOT NULL",
			"titre" => "varchar(255) NOT NULL DEFAULT ''",
			"descriptif" => "text NOT NULL DEFAULT ''",
			"lat" => "double NULL NULL",
			"lon" => "double NULL NULL",
			"zoom" => "tinyint(4) NULL NULL",
			"adresse" => "text NOT NULL DEFAULT ''",
			"pays" => "text NOT NULL DEFAULT ''",
			"code_pays" => "varchar(255) NOT NULL DEFAULT ''",
			"region" => "text NOT NULL DEFAULT ''",
			"ville" => "text NOT NULL DEFAULT ''",
			"code_postal" => "varchar(255) NOT NULL DEFAULT ''"
		),
		'key' => array(
			"PRIMARY KEY" => "id_gis",
		),
		'principale' => 'oui',
		'modeles' => array('carte_gis', 'carte_gis_preview'),

		/* Le titre, la date et la gestion du statut */
		'titre' => "titre, '' AS lang",

		/* L'édition, l'affichage et la recherche */
		'page' => 'gis',
		'url_voir' => 'gis',
		'url_edit' => 'gis_edit',
		'editable' => 'oui',
		'champs_editables' => array('lat', 'lon', 'zoom', 'titre', 'descriptif', 'adresse', 'code_postal', 'ville', 'region', 'pays'),
		/*'champs_editables' => array(), */
		'icone_objet' => 'gis',
		'rechercher_champs' => array(
			'titre' => 8,
			'descriptif' => 5,
			'pays' => 3,
			'region' => 3,
			'ville' => 3,
			'code_postal' => 3,
		),

		/* Les textes standard */
		'texte_ajouter' => 'gis:texte_ajouter_gis',
		'texte_retour' => 'icone_retour',
		'texte_modifier' => 'gis:texte_modifier_gis',
		'texte_creer' => 'gis:texte_creer_gis',
		'texte_creer_associer' => 'gis:texte_creer_associer_gis',
		'texte_objet' => 'gis:gis_singulier',
		'texte_objets' => 'gis:gis_pluriel',
		'info_aucun_objet' => 'gis:info_aucun_gis',
		'info_1_objet' => 'gis:info_1_gis',
		'info_nb_objets' => 'gis:info_nb_gis',
		'texte_logo_objet' => 'gis:libelle_logo_gis',
	);

	$spip_gis_liens = array(
		"id_gis" => "bigint(21) NOT NULL",
		"objet" => "VARCHAR (25) DEFAULT '' NOT NULL",
		"id_objet" => "bigint(21) NOT NULL");

	$spip_gis_liens_key = array(
		"PRIMARY KEY" => "id_gis,id_objet,objet",
		"KEY id_objet" => "id_gis");

	$tables_auxiliaires['spip_gis_liens'] = array(
		'field' => &$spip_gis_liens,
		'key' => &$spip_gis_liens_key);

	return $tables;
}

function gis_declarer_tables_auxiliaires($tables_auxiliaires){
	$spip_gis_liens = array(
		"id_gis" => "bigint(21) NOT NULL",
		"objet" => "VARCHAR (25) DEFAULT '' NOT NULL",
		"id_objet" => "bigint(21) NOT NULL");

	$spip_gis_liens_key = array(
		"PRIMARY KEY" => "id_gis,id_objet,objet",
		"KEY id_objet" => "id_gis");

	$tables_auxiliaires['spip_gis_liens'] = array(
		'field' => &$spip_gis_liens,
		'key' => &$spip_gis_liens_key);

	return $tables_auxiliaires;
}

?>