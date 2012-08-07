<?php
/**
 * Plugin Albums
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function albums_upgrade($nom_meta_base_version, $version_cible){
	$maj = array();

	include_spip('inc/config');
	include_spip('base/abstract_sql');

	# Premiere installation  creation des tables
	$maj['create'] = array(
		array('maj_tables', array('spip_albums', 'spip_albums_liens')),
	);

	# Version 2.0.2 : meta + suppression colonne categorie
	$maj['2.0.2'] = array(
		# On supprime la colonne 'categorie'
		array('sql_alter','TABLE spip_albums DROP COLUMN categorie'),
		# Configuration : valeurs par defaut
		array(ecrire_config('albums/afficher_champ_descriptif', 'on'),
			ecrire_config('albums/objets', array('spip_articles')),
			ecrire_config('albums/afficher_champ_descriptif', 'on'),
			ecrire_config('albums/vue_icones', array('titre')),
			ecrire_config('albums/vue_liste', array('icone', 'mimetype', 'poids', 'dimensions'))),
	);

	# Version 2.0.4 : on utilise le statut prepa au lieu de refuse
	$maj['2.0.4'] = array(
		sql_updateq('spip_albums', array('statut' => 'prepa'), 'statut = '.sql_quote('refuse')),
	);

	# On active l'ajout de documents aux albums
	if (!in_array('spip_albums', explode(',',$GLOBALS['meta']['documents_objets'])))
		ecrire_meta('documents_objets',$GLOBALS['meta']['documents_objets'] . 'spip_albums,');

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
**/
function albums_vider_tables($nom_meta_base_version) {

	# supression des tables
	sql_drop_table("spip_albums");
	sql_drop_table("spip_albums_liens");

	# suppression meta & config
	effacer_meta($nom_meta_base_version);
	effacer_config('albums');

	# a faire : retirer les albums de la liste des objets pour les documents

	# Suppression des liens des documents lies aux albums
	# -> utiliser optimiser_base_disparus a la place ?
	if (sql_countsel("spip_documents_liens", "objet='album'")){
		sql_delete("spip_documents_liens", "objet='album'");
	}

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",		sql_in("objet", array('album')));
	sql_delete("spip_versions_fragments",	sql_in("objet", array('album')));
}

?>
