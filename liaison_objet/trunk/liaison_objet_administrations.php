<?php
/**
 * Plugin Signaler des abus
 * (c) 2012 My Chacra
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Fonction d'installation du plugin et de mise à jour.
 * Vous pouvez :
 * - créer la structure SQL,
 * - insérer du pre-contenu,
 * - installer des valeurs de configuration,
 * - mettre à jour la structure SQL
 **/
function liaison_objet_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array(
			'maj_tables',
			array('spip_liaison_objets')
		),
		array('selection2liaison')
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation du plugin.
 * Vous devez :
 * - nettoyer toutes les données ajoutées par le plugin et selection_objetn utilisation
 * - supprimer les tables et les champs créés par le plugin.
 **/
function liaison_objet_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_liaison_objets");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions", sql_in("objet", array('abuselection_objetbjet')));
	sql_delete("spip_versions_fragments", sql_in("objet", array('abuselection_objetbjet')));
	sql_delete("spip_forum", sql_in("objet", array('abuselection_objetbjet')));

	effacer_meta($nom_meta_base_version);
}

/**
	* Permet de migrer du plugin "Sélection d'objet" vers "Liaison d'objet"
	*
 **/
function selection2liaison() {
	// D'abord, tester si on avait apparavent le plugin selection d'objet, et si oui, vérifier que c'était la version la plus récente. 
	$base_selection = lire_config("selection_objet_base_version");
	if (!$base_selection){
		return;
	}
	
	if ($base_selection != "0.5.1") {
		$message = "La migration depuis selection d'objet n'a pas fonctionné (base version != 0.5.1)";
		spip_log ($message, "selection_objet"._LOG_ERREUR);
		return $message;
	}

	// Idéalement il faudrait pouvoir désactiver l'ancien plugin automatiquement, mais je ne sais pas comment faire. Je n'arrive pas à comprendre s'il est possible de se servir de l'actionnaire de svp 
	// Du coup pour le moment il faut un plugin désactivé, et puis ensuite on supprime le meta de l'ancien
	effacer_config("selection_objet_base_version");

	// On déplacer l'ancienne config
	$cfg = lire_config('selection_objet');
	ecrire_config("liaison_objet",$spip);
	effacer_config("selection_objet");

	// Supprimer la table qui vient tout juste d'être créer pour ce nouveau plugin
	sql_drop_table('spip_liaison_objets');
	// Renommer la table de l'ancien plugin
	sql_alter("TABLE `spip_selection_objets` RENAME  `spip_liaison_objets`");
	//Modifier l'ancienne table
	sql_alter("TABLE `spip_liaison_objets` CHANGE COLUMN `id_selection_objet` `id_liaison_objet` bigint(21)");
}	
