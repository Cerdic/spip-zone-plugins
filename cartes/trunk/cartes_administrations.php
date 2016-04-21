<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Création de cartes
 *
 * @plugin     Création de cartes
 * @copyright  2016
 * @author     kent1
 * @licence    GNU/GPL
 * @package    SPIP\Cartes\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin Création de cartes.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function cartes_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_cartes')));
	$maj['1.0.6'] = array(array('maj_tables', array('spip_cartes')));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Création de cartes.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function cartes_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_cartes");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('carte')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('carte')));
	sql_delete("spip_forum",                 sql_in("objet", array('carte')));

	effacer_meta($nom_meta_base_version);
}