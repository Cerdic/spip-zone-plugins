<?php
/**
 * Utilisations de pipelines par Réservations multiples
 *
 * @plugin     Réservations multiples
 * @copyright  2014
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Reservations_multiples\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION'))
  return;

/**
 * Retourne la configuration
 *
 * @return array       Données de la configuration
 **/
function reservations_multiples_config() {
  include_spip('inc/config');
  $config = lire_config('reservations_multiples',array());

  // Si pas de config on met le défauts
  if (count($config) == 0) {
    $config = array('multiple_personnes' => 'on');
  }

  return $config;
}
