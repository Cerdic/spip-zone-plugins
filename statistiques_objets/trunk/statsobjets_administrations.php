<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Statistiques des objets éditoriaux.
 *
 * @plugin    Statistiques des objets éditoriaux
 * @copyright 2016
 * @author    tcharlss
 * @licence   GNU/GPL
 * @package   SPIP\Statistiques_objets\Administrations
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Statistiques des objets éditoriaux.
 *
 * @param string $nom_meta_base_version
 *		 Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *		 Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function statsobjets_upgrade($nom_meta_base_version, $version_cible) {

	include_spip('inc/config');

	$maj = array();

	// Ajout de 2 tables pour prendre en compte les statistiques des objets éditoriaux.
	// Ajout des colonnes popularite, visites et referers sur tous les objets.
	$maj['create'] = array(
		array('maj_tables', array('spip_visites_objets', 'spip_referers_objets')),
		array('statsobjets_maj_create'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);

}


/**
 * Fonction de désinstallation du plugin Statistiques des objets éditoriaux.
 *
 * @param string $nom_meta_base_version
 *		 Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function statsobjets_vider_tables($nom_meta_base_version) {

	include_spip('base/objets');

	// tables
	sql_drop_table('spip_visites_objets');
	sql_drop_table('spip_referers_objets');

	// virer les colonnes, sauf pour les articles
	$tables_objets = array_keys(lister_tables_objets_sql());
	//$trouver_table = charger_fonction('trouver_table','base');
	foreach($tables_objets as $table){
		//$desc = $trouver_table($table);
		if (objet_type($table) != 'article'){
			sql_alter("TABLE $table DROP popularite");
			sql_alter("TABLE $table DROP visites");
			sql_alter("TABLE $table DROP referers");
		}
	}

	effacer_meta('activer_statistiques_objets');
	effacer_meta($nom_meta_base_version);
}


/**
 * Fonction privée pour l'installation
 * @return void
 */
function statsobjets_maj_create() {
	// Si les stats sont activées, on prend en compte les articles
	include_spip('inc/config');
	if (lire_config('activer_statistiques') == 'oui') {
		ecrire_config('activer_statistiques_objets', array('spip_articles'));
	}
	// vérifier la présence des champs sur toutes les tables
	statsobjets_check_upgrade();
}


/**
 * Vérifier que les champs nécessaires sont présents sur tous les objets : popularite, visites, referers
 * Fonction appelée également lorsqu'on enregistre la configuration
 * (cas d'un nouvel objet ajouté apres l'install du plugin)
 *
 * @return void
 */
function statsobjets_check_upgrade() {

	include_spip('base/objets');

	$tables_objets = array_keys(lister_tables_objets_sql());
	$trouver_table = charger_fonction('trouver_table','base');
	foreach($tables_objets as $table){
		$desc = $trouver_table($table);
		if (!isset($desc['field']['popularite'])){
			sql_alter("TABLE $table ADD popularite DOUBLE DEFAULT '0' NOT NULL");
		}
		if (!isset($desc['field']['visites'])) {
			sql_alter("TABLE $table ADD visites integer DEFAULT '0' NOT NULL");
		}
		if (!isset($desc['field']['referers'])) {
			sql_alter("TABLE $table ADD referers integer DEFAULT '0' NOT NULL");
		}
	}

}