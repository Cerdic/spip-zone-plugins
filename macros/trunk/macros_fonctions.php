<?php
/**
 * Fonctions utiles au plugin Macros
 *
 * @plugin     Macros
 * @copyright  2013
 * @author     Michel Bystranowski
 * @licence    GNU/GPL
 * @package    SPIP\Macros\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/macros');

function balise_MACRO($p) {

  $nom      = interprete_argument_balise (1, $p);
  $contexte = interprete_argument_balise (2, $p);
  $p->code  = 'recuperer_macro(' . $nom . ', ' . $contexte . ')';

  return $p;
}