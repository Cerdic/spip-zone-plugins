<?php
/**
 * Définit les autorisations du plugin Incarner
 *
 * @plugin     Incarner
 * @copyright  2013
 * @author     Michel Bystranowski
 * @licence    GNU/GPL
 * @package    SPIP\Incarner\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function incarner_autoriser(){}

function autoriser_incarner_dist ($faire, $type, $id, $qui, $opt) {

  include_spip('inc/config');

  $cle = lire_config('incarner/cle');

  if ( ($cle AND ($_COOKIE['spip_cle_incarner'] == $cle))
       OR autoriser('webmestre')) {

    return True;
  }

  return False;
}