<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'installation et de mise à jour du plugin Collection de favoris.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function mesfavoris_collections_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	
	// Création de la nouvelle table de collections et ajouter de l'id dans les favoris
	$maj['create'] = array(
		array('maj_tables', array('spip_favoris', 'spip_favoris_collections')),
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Itinéraires.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function mesfavoris_collections_vider_tables($nom_meta_base_version) {
	sql_drop_table('spip_favoris_collections');
	sql_alter('table spip_favoris drop column id_favoris_colection');
	
	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('favoris_collection')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('favoris_collection')));
	sql_delete("spip_forum",                 sql_in("objet", array('favoris_collection')));

	effacer_meta($nom_meta_base_version);
}
