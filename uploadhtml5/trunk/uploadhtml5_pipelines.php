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