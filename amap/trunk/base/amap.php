<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Amap
 * @copyright  2013
 * @author     Pierre KUHN
 * @licence    GNU/GPL
 * @package    SPIP\Amap\Pipelines
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
function amap_declarer_tables_interfaces($interfaces) {
	//-- Alias
	$interfaces['table_des_tables']['amap_paniers'] = 'amap_paniers';
	$interfaces['table_des_tables']['amap_responsables'] = 'amap_responsables';
	$interfaces['table_des_tables']['amap_livraisons'] = 'amap_livraisons';
	//-- filtre date
	$interfaces['table_date']['amap_paniers'] = 'date_distribution';
	$interfaces['table_date']['amap_responsables'] = 'date_distribution';
	//-- Savoir traiter "_ " en <br />
	$interfaces['table_des_traitements']['CONTENU_PANIER']['amap_livraisons'] = _TRAITEMENT_RACCOURCIS;
	$interfaces['table_des_traitements']['INFO_SUPPLEMENTAIRE']['amap_disponibles'] = _TRAITEMENT_RACCOURCIS;
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
function amap_declarer_tables_objets_sql($tables) {

	$tables['spip_amap_paniers'] = array(
		'type' => 'amap_panier',
		'principale' => "oui", 
		'table_objet_surnoms' => array('amappanier'), // table_objet('amap_panier') => 'amap_paniers' 
		'field'=> array(
			"id_amap_panier"     => "bigint(21) NOT NULL",
			"id_auteur"          => "bigint(21) NOT NULL",
			"id_producteur"      => "bigint(21) NOT NULL",
			"date_distribution"  => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"dispo"              => "varchar(3) DEFAULT 'non'",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_amap_panier",
		),
		'titre' => "'' AS titre, '' AS lang",
		'date' => "",
		'champs_editables'  => array(),
		'champs_versionnes' => array(),
		'rechercher_champs' => array(),
		'tables_jointures'  => array(),
		

	);

	$tables['spip_amap_responsables'] = array(
		'type' => 'amap_responsable',
		'principale' => "oui", 
		'table_objet_surnoms' => array('amapresponsable'), // table_objet('amap_responsable') => 'amap_responsables' 
		'field'=> array(
			"id_amap_responsable" => "bigint(21) NOT NULL",
			"id_auteur"          => "bigint(21) NOT NULL",
			"date_distribution"  => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_amap_responsable",
		),
		'titre' => "'' AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array(),
		'champs_versionnes' => array(),
		'rechercher_champs' => array(),
		'tables_jointures'  => array(),
		

	);

	$tables['spip_amap_livraisons'] = array(
		'type' => 'amap_livraison',
		'principale' => "oui", 
		'table_objet_surnoms' => array('amaplivraison'), // table_objet('amap_livraison') => 'amap_livraisons' 
		'field'=> array(
			"id_amap_livraison"  => "bigint(21) NOT NULL",
			"date_livraison"     => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"contenu_panier"     => "text",
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_amap_livraison",
		),
		'titre' => "'' AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array(),
		'champs_versionnes' => array(),
		'rechercher_champs' => array(),
		'tables_jointures'  => array(),
		

	);

	return $tables;
}

//creation de champs extra
function amap_declarer_champs_extras($champs = array()){
	// type d'adhérent
	$champs['spip_auteurs']['type_adherent'] = array(
		'saisie' => 'radio', // Type du champ (voir plugin Saisies)
		'options' => array(
				'nom' => 'type_adherent',
				'label' => _T('amap:type_adherent_auteur'),
				'sql' => "varchar(15) DEFAULT ''",
				'defaut' => '', // Valeur par défaut
				'restrictions'=>array(
						'voir' => array('auteur' => ''), // Tout le monde peut voir
						'modifier' => array('auteur' => ''), // Seuls les auteurs peuvent modifier
				),
				'datas' => array(
						'adherent' => _T('amap:adherent'),
						'producteur' => _T('amap:producteur'),
				),
		),
	);

	// l'adhésion
	$champs['spip_auteurs']['adhesion'] = array(
		'saisie' => 'input',//Type du champ (voir plugin Saisies)
		'options' => array(
				'nom' => 'adhesion',
				'label' => _T('amap:adhesion_auteur'),
				'sql' => "bigint(21) NULL", // declaration sql
				'defaut' => '',// Valeur par défaut
				'restrictions'=>array(
						'voir' => array('auteur' => ''), // Tout le monde peut voir
						'modifier' => array('auteur' => ''), // Seuls les auteurs peuvent modifier
				),
		),
	);

	// type de panier
	$champs['spip_auteurs']['type_panier'] = array(
		'saisie' => 'radio',//Type du champ (voir plugin Saisies)
		'options' => array(
				'nom' => 'type_panier',
				'label' => _T('amap:type_panier_auteur'),
				'sql' => "varchar(10) DEFAULT ''", // declaration sql
				'defaut' => '',// Valeur par défaut
				'restrictions'=>array(
						'voir' => array('auteur' => ''), // Tout le monde peut voir
						'modifier' => array('auteur' => ''), // Seuls les auteurs peuvent modifier
				),
				'datas' => array(
						'petit' => _T('amap:petit'),
						'grand' => _T('amap:grand'),
				)
		)
	);

	return $champs;
}

?>
