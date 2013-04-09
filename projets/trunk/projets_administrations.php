<?php
/**
 * Plugin projets
 * (c) 2012 Cyril Marion
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
 * Vous pouvez :
 * - créer la structure SQL,
 * - insérer du pre-contenu,
 * - installer des valeurs de configuration,
 * - mettre à jour la structure SQL 
**/
function projets_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_projets', 'spip_projets_liens', 'spip_projets_cadres')));

	// on ne gère plus les categories de projets (voir avec les groupes de mots si les gens en veulent).
	$maj['1.1.0']  = array(
		array('sql_drop_table', 'spip_projets_categories'),
		array('sql_alter', 'TABLE spip_projets DROP id_projets_categorie'),
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
 * Vous devez :
 * - nettoyer toutes les données ajoutées par le plugin et son utilisation
 * - supprimer les tables et les champs créés par le plugin. 
**/
function projets_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_projets");
	sql_drop_table("spip_projets_liens");
	sql_drop_table("spip_projets_cadres");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('projet', 'projets_cadre')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('projet', 'projets_cadre')));
	sql_delete("spip_forum",                 sql_in("objet", array('projet', 'projets_cadre')));

	effacer_meta($nom_meta_base_version);
}

?>
