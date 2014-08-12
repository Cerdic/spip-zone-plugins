<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Pages Uniques
 *
 * @plugin     Pages
 * @copyright  2013
 * @author     RastaPopoulos 
 * @licence    GNU/GPL
 * @package    SPIP\Pages\Installation
 * @link       http://contrib.spip.net/Pages-uniques
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Fonction d'installation et de mise à jour du plugin
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function pages_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', 'spip_articles')
	);
	$maj['1.0.1'] = array(
		array('sql_alter', "TABLE spip_articles CHANGE page page VARCHAR(255) DEFAULT '' NOT NULL"),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin
 * Supprimer la colonne 'page' du plugin
 *
 * TODO : que deviennent les article avec un id_rubrique=-1 ? Ne faut-il pas les traiter ?
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function pages_vider_tables($nom_meta_base_version) {
	sql_alter("TABLE spip_articles DROP page");
	effacer_meta($nom_meta_base_version);
}

?>
