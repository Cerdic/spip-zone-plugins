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
		array(
			'maj_tables',
			array(
				'spip_options',
				'spip_options_liens',
				'spip_optionsgroupes',
			),
		),

		// ajouter le champ options dans les tables des plugins Panier et Commande
		array('optionsproduits_alter_paniers_commandes'),
		
		// ajouter les options et groupes à la config des objets géré par Rang
		array('optionsproduits_configure_rang'),
	);

	$maj['1.0.1'] = array(
		array('optionsproduits_configure_rang'),
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

	// Nettoyer les versionnages et forums
	sql_delete('spip_versions', sql_in('objet', array('option')));
	sql_delete('spip_versions_fragments', sql_in('objet', array('option')));

	effacer_meta($nom_meta_base_version);

	// Retirer les options et groupes d'options dans la liste des objets géré par le plugin Rang
	$tables = lire_config('rang/rang_objets');
	$tables = explode(',', $tables);
	unset($tables['spip_options']);
	unset($tables['spip_optionsgroupes']);
	ecrire_config('rang/rang_objets', implode(',', $tables));
}

/**
 * Ajouter les options et groupes d'options dans la liste des objets géré par le plugin Rang
 *
 * @return void
 **/
function optionsproduits_configure_rang() {
	$tables = lire_config('rang/rang_objets');
	$tables = explode(',', $tables);
	$tables_options = array(
		'spip_options',
		'spip_optionsgroupes',
	);
	$tables = array_unique(array_merge($tables, $tables_options));
	ecrire_config('rang/rang_objets', implode(',', $tables));
	// créer les champs 'rang' dans les tables
	rang_creer_champs($tables_options);
}

/**
 * Ajouter le champ options dans les tables des plugins Panier et Commande
 *
 * @return void
 **/
function optionsproduits_alter_paniers_commandes() {

	// TODO : problème si on installe les plugins Panier ou Commande après ce plugin,
	// les champs ne seront pas créés ni la clé réécrite...
	// faudrait pouvoir checker ça avec un pipeline post installation de plugin
	// je sais pas comment faire pour l'instant sans faire le gros bourrin
	
	if(test_plugin_actif('paniers')) {
		// ajouter un champ pour les options dans les lignes du panier
		sql_alter('TABLE spip_paniers_liens ADD options VARCHAR(100) NOT NULL DEFAULT ""');
		// recréer la clé du panier avec les options
		sql_alter('TABLE `spip_paniers_liens` DROP PRIMARY KEY');
		sql_alter('TABLE `spip_paniers_liens` ADD PRIMARY KEY (`id_panier`, `id_objet`, `objet`, `options`)');		
   }
   
	if(test_plugin_actif('commandes')) {
		// ajouter un champ pour les options dans les lignes des commandes
		sql_alter('TABLE spip_commandes_details ADD options VARCHAR(100) NOT NULL DEFAULT ""');
	}

} 