<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin encarts
 *
 * @plugin     encarts
 * @copyright  2013-2016
 * @author     Cyril
 * @licence    GNU/GPL
 * @package    SPIP\Encarts\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin encarts.
 *
 * Vous pouvez :
 *
 * - créer la structure SQL,
 * - insérer du pre-contenu,
 * - installer des valeurs de configuration,
 * - mettre à jour la structure SQL
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
 **/
function encarts_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_encarts', 'spip_encarts_liens')));

	// Ajout du meta casier "objets" avec les articles activés par défaut
	$maj['1.1.0'] = array(
		array('ecrire_config', 'encarts/objets', 'spip_articles'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin encarts.
 *
 * Vous devez :
 *
 * - nettoyer toutes les données ajoutées par le plugin et son utilisation
 * - supprimer les tables et les champs créés par le plugin.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
 **/
function encarts_vider_tables($nom_meta_base_version) {
	# quelques exemples
	# (que vous pouvez supprimer !)
	# sql_drop_table("spip_xx");
	# sql_drop_table("spip_xx_liens");

	sql_drop_table("spip_encarts");
	sql_drop_table("spip_encarts_liens");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions", sql_in("objet", array('encart')));
	sql_delete("spip_versions_fragments", sql_in("objet", array('encart')));
	sql_delete("spip_forum", sql_in("objet", array('encart')));

	effacer_meta($nom_meta_base_version);
}

