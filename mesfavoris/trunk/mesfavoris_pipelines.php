<?php
/**
 * Plugin mesfavoris
 * (c) 2009-2013 Olivier Sallou, Cedric Morin, Gilles Vincent
 * Distribue sous licence GPL
 *
 */

/**
 * Utilisation des pipelines
 *
 * @package SPIP\Mesfavoris\Pipelines
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Déclaration de l'index de $tables_principales qui sera utilisé dans les 'spip_'
 *
 * @pipeline declarer_tables_interfaces
 * @param  array $interface Array contenant les infos des tables visibles par recherche sur 'spip_bidule'
 * @return array            Cet Array de description modifié
 */
function mesfavoris_declarer_tables_interfaces($interface) {
	$interface['table_des_tables']['favoris'] = 'favoris';
	
	return $interface;
}

/**
 * Declaration des tables principales
 *
 * @pipeline declarer_tables_principales
 * @param array $tables_principales Un array de description des tables
 * @return array $tables_principales L'Array de description complété
 */
function mesfavoris_declarer_tables_principales($tables_principales) {
	$spip_favoris = array(
		"id_favori" => "BIGINT NOT NULL",
		"id_auteur" => "BIGINT DEFAULT '0' NOT NULL",
		"id_objet"  => "BIGINT DEFAULT '0' NOT NULL",
		"objet"     => "VARCHAR(25) DEFAULT '' NOT NULL",
		"categorie" => "VARCHAR(99) DEFAULT '' NOT NULL",
		"date_ajout" => "DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL", // MySQL>=5.6.5 ; SQLite: not NOW()
		"maj"       => "TIMESTAMP",
	);

	$spip_favoris_key = array(
		"PRIMARY KEY"       => "id_favori",
		"KEY auteur_objet"  => "id_auteur,id_objet,objet",
		"KEY id_auteur"     => "id_auteur", // un peu inutile vu qu'il y a l'index auteur_objet
		"KEY id_objet"      => "id_objet",
		"KEY objet"         => "objet",
		"KEY categorie"     => "categorie",
	);

	$tables_principales['spip_favoris'] = array('field' => &$spip_favoris, 'key' => &$spip_favoris_key);

	return $tables_principales;
}

/**
 * Insertion dans le pipeline insert_head_css
 *
 * @pipeline insert_head_css
 * @param string $flux Le contenu CSS du head
 * @return string Le contenu CSS du head modifié
 */
function mesfavoris_insert_head_css($flux) {
	$css = find_in_path("css/mesfavoris.css");
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='".timestamp(direction_css($css))."' />\n";
	
	return $flux;
}
