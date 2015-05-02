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

    $flux .= '<link rel="stylesheet" href="'.find_in_path('prive/css/dropzone_prive.css').'" type="text/css" media="screen" />';


    return $flux;
}

function uploadhtml5_formulaire_fond($flux) {

    if ($flux['args']['form'] == 'joindre_document') {

        // Récupérer le formulaire d'upload en html5 et lui passer une partie du contexte de joindre_document
        $uploadhtml5 = recuperer_fond(
            'prive/squelettes/inclure/uploadhtml5',
            array(
                'type' => $flux['args']['contexte']['objet'],
                'id' => $flux['args']['contexte']['id_objet']
            )
        );

        // Injecter uloadhtml5 au dessus du formulaire joindre_document.
        $flux['data'] = $uploadhtml5.$flux['data'];
    }

    return $flux;
}