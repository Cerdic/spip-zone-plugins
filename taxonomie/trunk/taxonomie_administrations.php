<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Taxonomie
 *
 * @plugin     Taxonomie
 * @copyright  2014
 * @author     Eric
 * @licence    GNU/GPL
 * @package    SPIP\Taxonomie\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin Taxonomie.
 * Le schéma du plugin est composé d'une table spip_taxons et d'une
 * configuration.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
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
 * Fonction de désinstallation du plugin Taxonomie.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function taxonomie_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_taxons");

	// Nettoyer les versionnages
	sql_delete("spip_versions",              sql_in("objet", array('taxon')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('taxon')));

	// Effacer la meta de configuration du plugin
	effacer_meta('taxonomie');

	effacer_meta($nom_meta_base_version);
}

function configurer_taxonomie() {
	$config = array(
		'services' => array('cinfo'),
	);

	return $config;
}
?>