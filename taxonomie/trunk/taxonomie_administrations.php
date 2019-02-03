<?php
/**
 * Fichier gérant l'installation et la désinstallation du plugin Taxonomie
 *
 * @package    SPIP\TAXONOMIE\CONFIGURATION
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin.
 * Le schéma du plugin est composé d'une table `spip_taxons` et d'une configuration.
 *
 * @param string	$nom_meta_base_version
 * 		Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string	$version_cible
 * 		Version du schéma de données (déclaré dans paquet.xml)
 *
 * @return void
**/
function taxonomie_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$config_defaut = configurer_taxonomie();

	$maj['create'] = array(
		array('maj_tables', array('spip_taxons')),
		array('ecrire_config', 'taxonomie', $config_defaut)
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
 *
 * @param string	$nom_meta_base_version
 * 		Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP.
 *
 * @return void
**/
function taxonomie_vider_tables($nom_meta_base_version) {

	// Supprimer la table des taxons créées par le plugin
	sql_drop_table("spip_taxons");

	// Nettoyer les versionnages
	sql_delete("spip_versions",              sql_in("objet", array('taxon')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('taxon')));

	// Effacer la meta de chaque règne chargé. On boucle sur tous les règnes
	include_spip('inc/taxonomie');
	foreach (regne_lister() as $_regne) {
		effacer_meta("taxonomie_${_regne}");
	}

	// Effacer la meta de configuration du plugin
	effacer_meta('taxonomie');

	// Effacer la meta du schéma de la base
	effacer_meta($nom_meta_base_version);
}

/**
 * Initialise la configuration du plugin.
 *
 * @return array
 * 		Le tableau de la configuration par défaut qui servira à initialiser la meta `taxonomie`.
 */
function configurer_taxonomie() {

	$config = array(
		'langues_utilisees' => array('fr'),
		'services_utilises' => array('wikipedia'),
		'iucn_token'        => ''
	);

	return $config;
}
