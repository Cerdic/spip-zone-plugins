<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Optionsproduits
 *
 * @plugin     Optionsproduits
 * @copyright  2017
 * @author     nicod_
 * @licence    GNU/GPL
 * @package    SPIP\Optionsproduits\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'installation et de mise à jour du plugin Optionsproduits.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 *
 * @return void
 **/
function optionsproduits_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(

		// créer les tables des options
		array('maj_tables', array('spip_options', 'spip_options_liens', 'spip_optionsgroupes')),

		// ajouter un champ pour les options dans les lignes du panier et des commandes
		array('sql_alter', 'TABLE spip_paniers_liens ADD options VARCHAR(100) NOT NULL DEFAULT ""'),
		array('sql_alter', 'TABLE spip_commandes_details ADD options VARCHAR(100) NOT NULL DEFAULT ""'),

		// recréer la clé du panier avec les options
		array('sql_alter', 'TABLE `spip_paniers_liens` DROP PRIMARY KEY'),
		array('sql_alter', 'TABLE `spip_paniers_liens` ADD PRIMARY KEY (`id_panier`, `id_objet`, `objet`, `options`)',),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation du plugin Optionsproduits.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 *
 * @return void
 **/
function optionsproduits_vider_tables($nom_meta_base_version) {

	sql_drop_table('spip_options');
	sql_drop_table('spip_options_liens');
	sql_drop_table('spip_optionsgroupes');

	# Nettoyer les versionnages et forums
	sql_delete('spip_versions', sql_in('objet', array('option')));
	sql_delete('spip_versions_fragments', sql_in('objet', array('option')));

	effacer_meta($nom_meta_base_version);
}
