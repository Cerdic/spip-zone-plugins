<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Rang
 *
 * @plugin     Rang
 * @copyright  2016
 * @author     Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\Rang\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Rang.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function rang_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Rang.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function rang_vider_tables($nom_meta_base_version) {

	include_spip('inc/rang_api');

	// supprimer les champs 'rang'
	// note : ici que faire si un objet a ete selectionne, puis deselectionne dans la config ?
	$objets_selectionnes = lire_config('rang/rang_objets');
	$objets = explode(',', $objets_selectionnes);
	foreach ($objets as $value) {
		$champs_table = sql_showtable($value);
		if (isset($champs_table['field']['rang'])) {
			sql_alter("TABLE $value DROP rang");
		}
	}

	// Effacer les metas
	effacer_meta('rang');
	effacer_meta($nom_meta_base_version);
}