<?php
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin
 */
function img_max_upgrade($nom_meta_base_version,$version_cible) {
    $maj = array();

    // Déclaration des valeurs par défaut de chaque variable de config
    $defaut = img_max_declarer_config();

    $maj['create'] = array(
        array('ecrire_config','img_max',$defaut)
    );

    include_spip('base/upgrade');
    maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function img_max_declarer_config() {
    $config =array(
        'img_max' => '1024',
    );

    return $config;
}


/**
 * Fonction de désinstallation
 * On supprime les trois metas du plugin :
 */
function img_max_vider_tables($nom_meta_base_version) {
    effacer_meta('img_max');
    effacer_meta($nom_meta_base_version);
}


?>
