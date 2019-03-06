<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Rang sur les auteurs
 *
 * @plugin     Rang sur les auteurs
 * @copyright  2019
 * @author     Cedric
 * @licence    GNU/GPL
 * @package    SPIP\Rang_auteurs\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Rang sur les auteurs.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function rang_auteurs_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('sql_alter', 'TABLE spip_auteurs_liens ADD rang_lien int(11) DEFAULT 0 NOT NULL'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Rang sur les auteurs.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function rang_auteurs_vider_tables($nom_meta_base_version) {
	sql_alter('TABLE spip_auteurs_liens DROP rang_lien');
	effacer_meta($nom_meta_base_version);
}
