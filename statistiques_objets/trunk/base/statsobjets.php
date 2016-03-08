<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin    Statistiques des objets éditoriaux
 * @copyright 2016
 * @author    tcharlss
 * @licence   GNU/GPL
 * @package   SPIP\Statistiques_objets\Base
 */

if (!defined('_ECRIRE_INC_VERSION')) {
		return;
}


/**
 * Déclarer les tables supplémentaires de statistiques
 *
 * Par défaut, SPIP n'enregistre que les visites des articles dans spip_visites_articles.
 * On prend en compte tous les objets éditoriaux avec une table générique spip_visites_objets.
 *
 * Déclare les tables :
 * - spip_visites_objets
 * - spip_referers_objets
 *
 * @pipeline declarer_tables_auxiliaires
 * @param array $tables_auxiliaires
 *     Description des tables auxiliaires
 * @return array
 *     Description complétée des tables auxiliaires
 */
function statsobjets_declarer_tables_auxiliaires($tables_auxiliaires) {

	$spip_visites_objets = array(
		'date'     => "DATE NOT NULL",
		'objet'    => "VARCHAR (25) DEFAULT '' NOT NULL",
		'id_objet' => "bigint(21) DEFAULT '0' NOT NULL",
		'visites'  => "int UNSIGNED DEFAULT '0' NOT NULL",
		'maj'      => "TIMESTAMP"
	);

	$spip_visites_objets_key = array(
		'PRIMARY KEY'  => "objet, id_objet, date",
		"KEY id_objet" => "id_objet",
		"KEY objet"    => "objet",
	);

	$spip_referers_objets = array(
		'objet'       => "VARCHAR (25) DEFAULT '' NOT NULL",
		'id_objet'    => "bigint(21) DEFAULT '0' NOT NULL",
		'referer_md5' => "bigint UNSIGNED NOT NULL",
		'referer'     => "VARCHAR (255) DEFAULT '' NOT NULL",
		'visites'     => "int UNSIGNED NOT NULL",
		'maj'         => "TIMESTAMP"
	);

	$spip_referers_objets_key = array(
		'PRIMARY KEY'     => "objet, id_objet, referer_md5",
		'KEY referer_md5' => "referer_md5",
		"KEY id_objet"    => "id_objet",
		"KEY objet"       => "objet",
	);

	$tables_auxiliaires['spip_visites_objets'] = array(
		'field' => &$spip_visites_objets,
		'key'   => &$spip_visites_objets_key
	);

	$tables_auxiliaires['spip_referers_objets'] = array(
		'field' => &$spip_referers_objets,
		'key'   => &$spip_referers_objets_key
	);

	return $tables_auxiliaires;
}
