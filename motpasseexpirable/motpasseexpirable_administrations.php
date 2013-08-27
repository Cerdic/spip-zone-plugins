<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Mots de passe expirables
 *
 * @plugin     Mots de passe expirables
 * @copyright  2013
 * @author     erational
 * @licence    GNU/GPL
 * @package    SPIP\Motpasseexpirable\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin Mots de passe expirables.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function motpasseexpirable_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	include_spip('inc/config');   
	$maj['create'] = array(
		array('sql_alter',"TABLE spip_auteurs ADD pass_maj datetime DEFAULT '0000-00-00 00:00:00' NOT NULL")
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Mots de passe expirables.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function motpasseexpirable_vider_tables($nom_meta_base_version) {

  spip_query("ALTER TABLE spip_auteurs DROP `pass_maj`");
	effacer_meta($nom_meta_base_version);
}

?>