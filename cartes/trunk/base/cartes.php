<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Création de cartes
 * @copyright  2016
 * @author     kent1
 * @licence    GNU/GPL
 * @package    SPIP\Cartes\Pipelines
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
function cartes_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_tables']['cartes'] = 'cartes';
	$interfaces['table_des_traitements']['TEXTE_FOOTER']['cartes'] = _TRAITEMENT_RACCOURCIS;
	$interfaces['table_des_traitements']['CONTROLES']['cartes'] = 'unserialize(%s)';
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
function cartes_declarer_tables_objets_sql($tables) {

	$tables['spip_cartes'] = array(
		'type' => 'carte',
		'principale' => 'oui',
		'field'=> array(
			'id_carte'           => 'bigint(21) NOT NULL',
			'titre'              => 'text NOT NULL DEFAULT ""',
			'texte'              => 'text NOT NULL DEFAULT ""',
			'texte_footer'       => 'text NOT NULL DEFAULT ""',
			'layer_defaut'       => 'text NOT NULL DEFAULT ""',
			'layer_topojson'     => 'text NOT NULL DEFAULT ""',
			'zoom_defaut'		 => 'int(6) NOT NULL DEFAULT 0',
			'zoom_min'           => 'int(6) NOT NULL DEFAULT 0',
			'zoom_max'           => 'int(6) NOT NULL DEFAULT 0',
			'style_carte'        => 'text NOT NULL DEFAULT ""',
			'footer_carte'       => 'text NOT NULL DEFAULT ""',
			'lat' 				 => 'double NULL NULL',
			'lon'				 => 'double NULL NULL',
			'center_points'      => 'VARCHAR(3) DEFAULT "non"',
			'popup'              => 'text NOT NULL DEFAULT ""',
			'label'              => 'text NOT NULL DEFAULT ""',
			'bounds'             => 'GEOMETRY DEFAULT "" NOT NULL',
			'controles'              => 'text NOT NULL DEFAULT ""',
			'type'               => 'text NOT NULL DEFAULT ""',
			'date'               => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'statut'             => 'varchar(20)  DEFAULT "0" NOT NULL',
			'lang'               => 'VARCHAR(10) NOT NULL DEFAULT ""',
			'langue_choisie'     => 'VARCHAR(3) DEFAULT "non"',
			'id_trad'            => 'bigint(21) NOT NULL DEFAULT 0',
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_carte',
			'KEY lang'           => 'lang',
			'KEY id_trad'        => 'id_trad',
			'KEY statut'         => 'statut',
		),
		'titre' => 'titre AS titre, lang AS lang',
		'date' => 'date',
		'champs_editables'  => array('titre', 'texte', 'layer_defaut', 'layer_topojson', 'zoom_defaut', 'zoom_min', 'zoom_max', 'style_carte', 'footer_carte', 'texte_footer', 'bounds', 'controles', 'type', 'lat', 'lon', 'center_points', 'popup', 'label'),
		'champs_versionnes' => array('titre', 'texte', 'layer_defaut', 'layer_topojson', 'zoom_defaut', 'zoom_min', 'zoom_max', 'style_carte', 'footer_carte', 'texte_footer', 'bounds', 'controles', 'type', 'lat', 'lon', 'center_points', 'popup', 'label'),
		'rechercher_champs' => array('titre' => 5, 'texte' => 7, 'texte_footer' => 4),
		'tables_jointures'  => array(),
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
		'texte_changer_statut' => 'carte:texte_changer_statut_carte',
	);

	$tables['spip_gis']['roles_titres']['label_nohide'] = 'carte:role_label_nohide';
	$tables['spip_gis']['roles_objets']['cartes']['choix'][] = 'label_nohide';
	return $tables;
}
