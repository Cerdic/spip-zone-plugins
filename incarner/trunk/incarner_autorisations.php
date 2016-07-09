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
  include_spip('inc/cookie');

  $cle = lire_config('incarner/cle');

  if ( ($cle AND ($_COOKIE['spip_cle_incarner'] == $cle))
       OR autoriser('webmestre')) {
    $cle = base64_encode(openssl_random_pseudo_bytes(16));

    ecrire_config('incarner/cle', $cle);
    spip_setcookie('spip_cle_incarner', $cle);

    return True;
  }

  return False;
}