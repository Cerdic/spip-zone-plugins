<?php
/**
 * Plugin Autocomplétion
 * (c) 2012 Dimitri EXBRAYAT
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
//error_reporting(E_ALL);
//ini_set('display_errors','On');
/**
 * Fonction d'installation du plugin et de mise.
**/
function autocompletion_upgrade($nom_meta_base_version, $version_cible) {
    
    $filename = include_spip("base/arrayCommunesDepRegFr");
    include($filename);
 
    $maj = array();
    $maj['create'] = array(
        array('maj_tables', array('spip_communes')),
        array('maj_tables', array('spip_departements')),
        array('maj_tables', array('spip_regions')),
        array('sql_insertq_multi', 'spip_communes', $spip_communesValeurs),
        array('sql_insertq_multi', 'spip_departements', $spip_departementsValeurs),
        array('sql_insertq_multi', 'spip_regions', $spip_regionsValeurs)
    ); 
    
    include_spip('base/upgrade');
    
    maj_plugin($nom_meta_base_version, $version_cible, $maj);

}

/**
 * Fonction de d‚àö¬©sinstallation du plugin.
**/
function autocompletion_vider_tables($nom_meta_base_version) {
    sql_drop_table("spip_communes");
    sql_drop_table("spip_departements");
    sql_drop_table("spip_regions");
    effacer_meta($nom_meta_base_version);        
}

?>