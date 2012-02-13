<?php

if (!defined('_ECRIRE_INC_VERSION'))
    return;
include_spip('inc/meta');
include_spip('base/abstract_sql');

function autartrole_upgrade($nom_meta_base_version,$version_cible)
{
    $current_version = 20120202; // v0.0
    if ( (!isset($GLOBALS['meta'][$nom_meta_base_version]))
        || (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible) )
    {
        include_spip('base/autartrole_table'); // ou se situe la reference des tables
        if ( $current_version==20120202 )
        { // cas d'une installation
            include_spip('base/serial');
            include_spip('base/auxiliaires');
            include_spip('base/create');
            creer_base();  // creer la base si elle ne l'est pas
            maj_tables('spip_auteurs_articles'); //va normalement faire : sql_alter("TABLE spip_auteurs_articles ADD role VARCHAR(200) DEFAULT '' NOT NULL ");
            ecrire_meta($nom_meta_base_version, $current_version = $version_cible, 'non'); // maj ver. dans metas
            spip_log(' Installation initiale du plugin','autartrole');
        }
    }

    return $tables_auxiliaires;
}

function autartrole_vider_tables($nom_meta_base_version)
{
    sql_alter('TABLE spip_auteurs_articles DROP role');
    effacer_meta($nom_meta_base_version);
    spip_log(' Desinstallation du plugin','autartrole');
}

?>