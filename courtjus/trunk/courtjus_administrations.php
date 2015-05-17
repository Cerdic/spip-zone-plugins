<?php
/**
 * Fonction d'upgrade/installation du plugin
 *
 * @plugin     Court-Jus
 * @copyright  2013
 * @author     Phenix
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) return;

function courtjus_upgrade($nom_meta_base_version, $version_cible) {

    // Création du tableau des mises à jour.
    $maj = array();

     $config_default = array(
         'objet_exclu' => array(),
         'squelette_par_rubrique' => '',
    );

    // Tableau de la configuration par défaut
    $maj['create'] = array(
        array('ecrire_meta', 'courtjus', serialize($config_default))
    );
    $maj['1.0.1'] =array(
        array('ecrire_meta', 'courtjus', serialize($config_default))
    );

    // Maj du plugin.
    include_spip('base/upgrade');
    maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/*
 *   Désintaller courtjus.
 */
function courtjus_vider_tables($nom_meta_base_version) {
    // Supprimer les méta, ou oublie pas celle de la base.
    effacer_meta('courtjus_base_version');
    effacer_meta('courtjus');
}