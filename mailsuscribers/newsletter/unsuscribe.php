<?php
/**
 * Plugin mailsuscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Desinscrire un suscriber par son email
 * si une ou plusieurs listes precisees, le suscriber est desinscrit de ces seules listes
 * si il n'en reste aucune, le statut du suscriber est suspendu
 * si aucune liste precisee, le suscriber est desinscrit de toutes les listes newsletter*
 *
 * @param $email
 *   champ obligatoire
 * @param array $options
 *   listes : array
 * @return bool
 *   true si inscrit, false sinon
 */
function newsletter_desinscrire_suscriber_dist($email,$options = array()){

	return true;
}