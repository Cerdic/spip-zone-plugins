<?php
/**
 * Utilisations de pipelines par Formulaire upload html5
 *
 * @plugin     Formulaire upload html5
 * @copyright  2014
 * @author     Phenix
 * @licence    GNU/GPL
 * @package    SPIP\Uploadhtml5\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function uploadhtml5_jquery_plugins($scripts) {

    $scripts[] = 'lib/dropzone/dropzone.js';

    return $scripts;
}

function uploadhtml5_insert_head_css($flux) {

    $flux .= '<link rel="stylesheet" href="'.find_in_path('lib/dropzone/dropzone.css').'" type="text/css" media="screen" />';

    return $flux;
}

function uploadhtml5_header_prive($flux) {
    $flux .= '<link rel="stylesheet" href="'.find_in_path('lib/dropzone/dropzone.css').'" type="text/css" media="screen" />';

    return $flux;
}

function uploadhtml5_afficher_complement_objet($flux) {

    $flux['data'] .= recuperer_fond('prive/squelettes/inclure/uploadhtml5', $flux['args']);

    return $flux;
}