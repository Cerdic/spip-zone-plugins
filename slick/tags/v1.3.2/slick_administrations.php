<?php
/**
 * Fonction d'upgrade/installation du plugin foundation-4-spip
 *
 * @plugin     foundation-4-spip
 * @copyright  2013
 * @author     Phenix
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) return;

function slick_upgrade($nom_meta_base_version, $version_cible) {

    // Création du tableau des mises à jour.
    $maj = array();

    $config_default = array(
        'charger' => 'on',
        'selecteur' => '#slick',
        'slide' => 'div',
        'slidesToShow' => 1,
        'slidesToScroll' => 1,
        'autoplay' => "true",
        'autoplaySpeed' => 3000,
        'fade' => "false",
        'speed' => 300,
        'vertical' => "false",
        'lazyload' => 'ondemand',
        'centerMode' => "false",
        'centerPadding' => '50px',
        'cssEase' => 'ease',
        'dots' => "false",
        'pauseOnHover' => "true",
        'pauseOnDotsHover' => "false",
        'rtl' => "false"
    );

    // Tableau de la configuration par défaut
    $maj['create'] = array(
        array('ecrire_meta', 'slick', serialize($config_default))
    );

    // Maj du plugin.
    include_spip('base/upgrade');
    maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/*
*   Désintaller slick.
*/
function slick_vider_tables($nom_meta_base_version) {
    // Supprimer les méta, ou oublie pas celle de la base.
    effacer_meta('slick_base_version');
    effacer_meta('slick');
}