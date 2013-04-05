<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin DayFill - Factures
 *
 * @plugin     DayFill - factures
 * @copyright  2013
 * @author     Cyril Marion
 * @licence    GNU/GPL
 * @package    SPIP\Dayfill\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/cextras');
include_spip('base/dayfill_factures');

/**
 * Fonction d'installation et de mise à jour du plugin DayFill - Factures.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function dayfill_factures_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	cextras_api_upgrade(dayfill_factures_declarer_champs_extras(), $maj['create']);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin DayFill - factures.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function dayfill_factures_vider_tables($nom_meta_base_version) {

	cextras_api_vider_tables(dayfill_factures_declarer_champs_extras());

	effacer_meta($nom_meta_base_version);
}

?>
