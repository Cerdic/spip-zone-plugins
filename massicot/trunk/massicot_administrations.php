<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Massicot
 *
 * @plugin     Massicot
 * @copyright  2015
 * @author     Michel @ Vertige ASBL
 * @licence    GNU/GPL
 * @package    SPIP\Massicot\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin Massicot.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function massicot_upgrade($nom_meta_base_version, $version_cible) {
    $maj = array();

    $maj['create'] = array(array('maj_tables', array('spip_massicotages', 'spip_massicotages_liens')));

    include_spip('base/upgrade');
    maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Massicot.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function massicot_vider_tables($nom_meta_base_version) {

    sql_drop_table("spip_massicotages");
    sql_drop_table("spip_massicotages_liens");

    effacer_meta($nom_meta_base_version);
}