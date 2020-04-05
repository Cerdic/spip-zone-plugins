<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/config');
include_spip('inc/mailsubscribers');
include_spip('mailsubscribers_fonctions');

/**
 * Renvoi les listes de diffusion disponibles avec leur status
 * (open,close,?)
 *
 * @param array $options
 *   status : filtrer les listes sur le status
 * @return array
 *   listes
 *   chaque liste est decrite par :
 *   id => array(titre=>'','status'=>'','descriptif'=>'','from_name'=>'','from_email'=>'')
 */
function newsletter_lists_dist($options = array()) {

	$options['category'] = 'newsletter';
	$options['segments'] = true;
	$res = mailsubscribers_listes($options);

	return $res;
}
