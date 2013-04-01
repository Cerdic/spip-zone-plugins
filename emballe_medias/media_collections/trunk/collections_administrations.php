<?php
/**
 * Plugin Collections (ou albums)
 * (c) 2012-2013 kent1 (http://www.kent1.info - kent1@arscenic.info)
 * Licence GNU/GPL
 * 
 * Installation et désinstallation du plugin collections
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction d'installation et de mise à jour du plugin.
 * 
 * Installe principalement les tables spip_collections et spip_collections_liens
 * Crée le diogène de collection à l'installation
 * 
 * @param string $nom_meta_base_version
 * 		Le nom de la meta d'installation
 * @param float $version_cible
 * 		Le numéro de version vers laquelle mettre à jour
 */
function collections_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_collections', 'spip_collections_liens')),
		array('collections_init')
	);
	$maj['1.0.1'] = array(array('maj_tables', array('spip_collections')));
	$maj['1.0.2'] = array(
		array('sql_alter',"TABLE spip_collections CHANGE type type_collection varchar(25) DEFAULT 'perso' NOT NULL"),
	);
	$maj['1.0.3'] = array(array('maj_tables', array('spip_collections'))); # Ajout du champ genre
	$maj['1.0.4'] = array(
		array('maj_tables', array('spip_collections_liens'))
	); # Ajout du champ rang et id_auteur sur spip_collections_liens
	$maj['1.0.5'] = array(
		array('maj_tables', array('spip_collections'))
	); # Ajout du champ id_admin sur spip_collections
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation du plugin.
 * 
 * Supprime les tables spip_collections et spip_collections_liens
 * Supprime également :
 * - les révisions potentielles de collections (spip_versions et spip_versions_fragments)
 * - les forums attachés aux collections 
 * - les liens des auteurs liés aux collections
 * - la meta d'installation du plugin
 * 
 * @param string $nom_meta_base_version
 * 		Le nom de la meta d'installation
 */
function collections_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_collections");
	sql_drop_table("spip_collections_liens");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('collection')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('collection')));
	sql_delete("spip_forum",                 sql_in("objet", array('collection')));
	sql_delete("spip_auteurs_liens",         sql_in("objet", array('collection')));

	effacer_meta($nom_meta_base_version);
}

/**
 * Fonction de création du diogène de collection si aucun diogène de collection
 * n'existe 
 * 
 * Appelé lors de l'installation du plugin
 */
function collections_init(){
	if(!$id_diogene_collections = sql_getfetsel('id_diogene','spip_diogenes','objet="collection"')){
		include_spip('action/editer_diogene');
		if(!function_exists('filtrer_entites'))
			include_spip('inc/filtres');
		$id_diogene_collections = diogene_inserer();
		$set_collection = array(
			'titre' => filtrer_entites(_T('collection:publier_une_collection')),
			'description' => filtrer_entites(_T('collection:publier_une_collection_desc')),
			'champs_caches' => serialize(array()),
			'champs_ajoutes' => array(),
			'menu'=> 'on',
			'statut_auteur' => '6forum',
			'statut_auteur_publier' => '6forum',
			'objet' => 'collection',
			'type' => 'collection'
		);
		diogene_modifier($id_diogene_collections, $set_collection);
	}
}
?>