<?php
/**
 * Fonctions d'installation et de désinstallation du plugin Albums
 *
 * @plugin     Albums
 * @copyright  2013
 * @author     Romy Tetue, Charles Razack
 * @licence    GNU/GPL
 * @package    SPIP\Albums\Administrations
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
 */
function albums_upgrade($nom_meta_base_version, $version_cible){
	$maj = array();

	include_spip('inc/config');
	include_spip('base/abstract_sql');

	// Création des tables + options de configuration
	$maj['create'] = array(
		array('maj_tables', array('spip_albums','spip_albums_liens')),
		array('ecrire_config','albums/afficher_champ_descriptif', 'on'),
		array('ecrire_config','albums/vue_icones', array('titre')),
		array('ecrire_config','albums/vue_liste', array('icone', 'mimetype', 'poids', 'dimensions')),
	);

	// Suppression de la colonne «categorie»
	$maj['0.0.2'] = array(
		array('sql_alter','TABLE spip_albums DROP COLUMN categorie'),
	);

	// Statut «prepa» au lieu de «refuse»
	$maj['0.0.3'] = array(
		array('sql_updateq', 'spip_albums', array('statut'=>'prepa'), 'statut='.sql_quote('refuse'))
	);

	// passer le titre en «text» au lieu de «varchar» pour la recherche fulltext
	// passer le titre en «text» au lieu de «mediumtext»
	// passer le satut en «varchar(10)» au lieu 255
	// nettoyer les options de configuration obsolètes
	$maj['1.0.0'] = array(
		array('sql_alter', "TABLE spip_albums CHANGE titre titre text DEFAULT '' NOT NULL"),
		array('sql_alter', "TABLE spip_albums CHANGE descriptif descriptif text DEFAULT '' NOT NULL"),
		array('sql_alter', "TABLE spip_albums CHANGE statut statut varchar(10) DEFAULT '' NOT NULL"),
		array('effacer_config','albums/afficher_champ_descriptif'),
		array('effacer_config','albums/vue_icones'),
		array('effacer_config','albums/vue_liste'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
 */
function albums_vider_tables($nom_meta_base_version) {

	# Supression des tables
	sql_drop_table("spip_albums");
	sql_drop_table("spip_albums_liens");

	# Suppression des liens des documents liés aux albums
	sql_delete("spip_documents_liens",    sql_in("objet", array('album')));

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",           sql_in("objet", array('album')));
	sql_delete("spip_versions_fragments", sql_in("objet", array('album')));
	sql_delete("spip_forum",              sql_in("objet", array('album')));

	# Suppression meta
	effacer_meta($nom_meta_base_version);

	# Retirer les albums de la liste des objets où téléverser des documents
	if (in_array('spip_albums', $objets=@array_filter(explode(',',$GLOBALS['meta']['documents_objets'])))){
		$objets = array_diff($objets,array('spip_albums'));
		ecrire_meta('documents_objets',implode(',',$objets));
	}

}


?>
