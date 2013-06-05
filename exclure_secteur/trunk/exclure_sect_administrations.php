<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Exclure secteur
 *
 * @plugin     Exclure secteur
 * @copyright  2013
 * @author     Maïeul Rouquette
 * @licence    GPL 3
 * @package    SPIP\exclure_sect\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction d'installation et de mise à jour du plugin
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function exclure_sect_upgrade($nom_meta_base_version, $version_cible){
	$maj = array();
	$maj['create'] = array(
		array('exclure_sect_conf')
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction d'installation : écriture config
**/
function exclure_sect_conf(){
	include_spip('inc/config');
	if (!lire_config('secteur/exclure_sect')){
		ecrire_config('secteur/exclure_sect',array());
	}
}

/**
 * Fonction de désinstallation du plugin
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function exclure_sect_vider_tables($nom_meta_base_version){
	if (lire_config('secteur')){
		effacer_config('secteur');
	}
	effacer_meta($nom_meta_base_version);
}

?>
