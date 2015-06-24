<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin panier options
 *
 * @plugin     Panier Options
 * @copyright  2015
 * @author     Anne-lise Martenot Elastick.net
 * @licence    GPL v3
 * @package    SPIP\Panier_options\Installation
 */
 
// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin panier options.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function panier_options_upgrade($nom_meta_base_version, $version_cible){
	$maj = array();

	include_spip('base/abstract_sql');
	include_spip('inc/config');
	$config_paniers=lire_config('paniers');

	// Première installation création des tables + options de configuration
	$maj['create'] = array(
		array('sql_alter',"table spip_paniers ADD options varchar(55) NOT NULL DEFAULT ''"),
		array('ecrire_config', 'paniers/panier_options', array(
			'code_avantage' => '',
			'pourcentage_avantage' => '',
			)
		)
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation du plugin paniers.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function panier_options_vider_tables($nom_meta_base_version){

	include_spip('base/abstract_sql');
	include_spip('inc/config');

	// On efface le champ du plugin
	sql_alter('table spip_paniers DROP options');

	// On efface la version entregistrée
	effacer_meta($nom_meta_base_version);
	effacer_config('paniers/panier_options');

}

?>
