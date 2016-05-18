<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function gis_declarer_tables_interfaces($interface) {
	$interface['table_des_tables']['gis'] = 'gis';
	$interface['table_des_tables']['gis_liens'] = 'gis_liens';

	// Traitements typo et raccourcis
	$interface['table_des_traitements']['TITRE_GIS'][] = 'typo(extraire_multi(%s))';
	$interface['table_des_traitements']['DESCRIPTIF_GIS'][] = _TRAITEMENT_RACCOURCIS;
	$interface['table_des_traitements']['VILLE_GIS'][] = 'typo(extraire_multi(%s))';
	$interface['table_des_traitements']['PAYS_GIS'][] = 'typo(extraire_multi(%s))';
	$interface['table_des_traitements']['REGION_GIS'][] = 'typo(extraire_multi(%s))';
	$interface['table_des_traitements']['DEPARTEMENT_GIS'][] = 'typo(extraire_multi(%s))';
	$interface['table_des_traitements']['VILLE'][] = 'typo(extraire_multi(%s))';
	$interface['table_des_traitements']['PAYS'][] = 'typo(extraire_multi(%s))';
	$interface['table_des_traitements']['REGION'][] = 'typo(extraire_multi(%s))';
	$interface['table_des_traitements']['DEPARTEMENT'][] = 'typo(extraire_multi(%s))';

	return $interface;
}

function gis_declarer_tables_objets_sql($tables) {
	/* Declaration de la table de points gis */
	$tables['spip_gis'] = array(
		/* Declarations principales */
		'table_objet' => 'gis',
		'table_objet_surnoms' => array('gis'),
		'type' => 'gis',
		'type_surnoms' => array('gi'),

		/* La table */
		'field' => array(
			'id_gis' => 'bigint(21) NOT NULL',
			'titre' => "text NOT NULL DEFAULT ''",
			'descriptif' => "text NOT NULL DEFAULT ''",
			'lat' => 'double NULL NULL',
			'lon' => 'double NULL NULL',
			'zoom' => 'tinyint(4) NULL NULL',
			'adresse' => "text NOT NULL DEFAULT ''",
			'pays' => "text NOT NULL DEFAULT ''",
			'code_pays' => "varchar(255) NOT NULL DEFAULT ''",
			'region' => "text NOT NULL DEFAULT ''",
			'departement' => "text NOT NULL DEFAULT ''",
			'ville' => "text NOT NULL DEFAULT ''",
			'code_postal' => "varchar(255) NOT NULL DEFAULT ''",
			'color' => "varchar(25) NOT NULL DEFAULT ''",
			'weight' => "varchar(4) NOT NULL DEFAULT ''",
			'opacity' => "varchar(4) NOT NULL DEFAULT ''",
			'fillcolor' => "varchar(25) NOT NULL DEFAULT ''",
			'fillopacity' => "varchar(4) NOT NULL DEFAULT ''"
		),
		'key' => array(
			'PRIMARY KEY' => 'id_gis',
			'KEY lat' => 'lat',
			'KEY lon' => 'lon',
			'KEY pays' => 'pays(500)',
			'KEY code_pays' => 'code_pays',
			'KEY region' => 'region(500)',
			'KEY departement' => 'departement(500)',
			'KEY ville' => 'ville(500)',
			'KEY code_postal' => 'code_postal',
		),
		'join' => array(
				'id_gis' => 'id_gis'
		),
		'principale' => 'oui',
		'modeles' => array('carte_gis', 'carte_gis_preview'),

		/* Le titre, la date et la gestion du statut */
		'titre' => "titre, '' AS lang",

		/* L'édition, l'affichage et la recherche */
		'page' => false,
		'url_voir' => 'gis',
		'url_edit' => 'gis_edit',
		'editable' => 'oui',
		'champs_editables' => array('lat', 'lon', 'zoom', 'titre', 'descriptif', 'adresse', 'code_postal', 'ville', 'region', 'departement', 'pays', 'code_pays', 'color', 'weight', 'opacity', 'fillcolor', 'fillopacity'),
		'champs_versionnes' => array('lat', 'lon', 'zoom', 'titre', 'descriptif', 'adresse', 'code_postal', 'ville', 'region', 'departement', 'pays', 'code_pays', 'color', 'weight', 'opacity', 'fillcolor', 'fillopacity'),
		'champs_critere_gis' => array('gis.titre AS titre_gis', 'gis.descriptif AS descriptif_gis', 'gis.adresse AS adresse_gis', 'gis.pays AS pays_gis', 'gis.code_pays AS code_pays_gis', 'gis.region AS region_gis', 'gis.departement AS departement_gis', 'gis.ville AS ville_gis', 'gis.code_postal AS code_postal_gis'),
		'icone_objet' => 'gis',
		'rechercher_champs' => array(
			'titre' => 8,
			'descriptif' => 5,
			'pays' => 3,
			'region' => 3,
			'departement' => 3,
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

	$tables[]['tables_jointures'][]= 'gis_liens';
	$tables[]['champs_versionnes'][] = 'jointure_gis';

	// recherche jointe sur les points gis pour tous les objets
	$tables[]['rechercher_jointures']['gis'] = array(
			'titre' => 3,
			'descriptif' => 2,
			'pays' => 4,
			'region' => 1,
			'departement' => 1,
			'ville' => 1,
			'code_postal' => 1
		);

	return $tables;
}

function gis_declarer_tables_auxiliaires($tables_auxiliaires) {
	$spip_gis_liens = array(
		'id_gis' => 'bigint(21) NOT NULL',
		'objet' => "VARCHAR (25) DEFAULT '' NOT NULL",
		'id_objet' => 'bigint(21) NOT NULL');

	$spip_gis_liens_key = array(
		'PRIMARY KEY' => 'id_gis,id_objet,objet',
		'KEY id_gis' => 'id_gis',
		'KEY id_objet' => 'id_objet',
		'KEY objet' => 'objet'
	);

	$tables_auxiliaires['spip_gis_liens'] = array(
		'field' => &$spip_gis_liens,
		'key' => &$spip_gis_liens_key);

	return $tables_auxiliaires;
}
