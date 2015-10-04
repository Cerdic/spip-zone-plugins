<?php
/**
 * Utilisations de pipelines par Massicot
 *
 * @plugin     Massicot
 * @copyright  2015
 * @author     Michel @ Vertige ASBL
 * @licence    GNU/GPL
 * @package    SPIP\Massicot\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Insérer le plugin jquery de selection du cadre
 *
 * @pipeline jquery_plugins
 * @param  array $scripts  Les scripts qui seront insérés dans la page
 * @return array       La liste des scripts complétée
 */
function massicot_jquery_plugins ($scripts) {

    $scripts[] = find_in_path('lib/jquery.imgareaselect.js/jquery.imgareaselect.dev.js');

    return $scripts;
}

/**
 * Ajoute le plugins jqueryui Slider
 *
 * @pipeline jqueryui_plugins
 * @param  array $scripts  Plugins jqueryui à charger
 * @return array       Liste des plugins jquerui complétée
 */
function massicot_jqueryui_plugins ($scripts) {

    $scripts[] = 'jquery.ui.slider';

    return $scripts;
}

/**
 * Ajouter un brin de CSS
 *
 * @pipeline header_prive
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function massicot_header_prive ($flux) {

    $flux .= '<link rel="stylesheet" type="text/css" media="screen" href="' .
          find_in_path('css/massicot.css') . '" />';

    $flux .= '<link rel="stylesheet" type="text/css" media="screen" href="' .
        find_in_path('lib/jquery.imgareaselect.js/distfiles/css/imgareaselect-default.css') . '" />';

    return $flux;
}

/**
 * Ajouter une action "recadrer" sur les documents
 *
 * @pipeline editer_document_actions
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function massicot_document_desc_actions ($flux) {

    $flux['data'] .= recuperer_fond('prive/squelettes/inclure/lien_recadre',
                                    $flux['args']);

    return $flux;
}