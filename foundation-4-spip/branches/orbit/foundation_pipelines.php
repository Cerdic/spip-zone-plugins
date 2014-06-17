<?php
/**
 * Utilisations de pipelines par foundation-4-spip
 *
 * @plugin     foundation-4-spip
 * @copyright  2013
 * @author     Phenix
 * @licence    GNU/GPL
 * @package    SPIP\Foundation\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/*
*   Pipeline Insert_head
*/
function foundation_insert_head ($flux) {
  include_spip('inc/foundation');
  return foundation_get_js($flux);
}

/*
*   Pipeline Insert_head_css
*/
function foundation_insert_head_css ($flux) {
  include_spip('inc/foundation');
  return foundation_get_css($flux);
}

// TODO: Charger foundation dans l'espace priver pour pouvoir prévisualiser les modèles.