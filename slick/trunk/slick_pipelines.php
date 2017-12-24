<?php
/**
 * Utilisations de pipelines par Slick
 *
 * @plugin     Slick
 * @copyright  2014
 * @author     Vertige (Didier)
 * @licence    GNU/GPL
 * @package    SPIP\Slick\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// Ajouter Slick à SPIP
function slick_insert_head($flux) {
    include_spip('inc/config');
    $config = lire_config('slick');

    $flux .= '<script type="text/javascript" src="'.find_in_path('lib/slick/slick.min.js').'"></script>';

    if ($config['charger']) {
        $flux .= '<script type="text/javascript" src="'.produire_fond_statique('javascript/slick.spip.js').'"></script>';
    }

    return $flux;
}

// Ajouter le css de slick
function slick_insert_head_css($flux) {
    $flux .= '<link rel="stylesheet" href="'.find_in_path('lib/slick/slick.css').'" type="text/css" />';
    $flux .= '<link rel="stylesheet" href="'.find_in_path('lib/slick/slick-theme.css').'" type="text/css" />';
    $flux .= '<link rel="stylesheet" href="'.find_in_path('css/slick-spip.css').'" type="text/css" />';

    return $flux;
}