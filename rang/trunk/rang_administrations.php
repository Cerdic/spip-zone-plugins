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

if (!defined('_ECRIRE_INC_VERSION')) return;


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
	$tables_a_nettoyer = rang_objets_gere_rubrique('oui');
	foreach ($tables_a_nettoyer as $table) {
		$champs_table = sql_showtable($table);
		if (isset($champs_table['field']['rang'])) {
			sql_alter("TABLE $table DROP rang");
		}
	}

	// Effacer les métas
	effacer_meta('rang_objets');
	effacer_meta($nom_meta_base_version);
}