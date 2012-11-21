<?php
/**
 * Plugin mailsuscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Decrit les informations d'un inscrit
 * Pour retirer une liste il faut desinscrire
 *
 * @param $email
 *   champ obligatoire
 * @return bool|array
 *   false si n'existe pas
 *   array :
 *     nom : string
 *     listes : array
 *     lang : string
 *     status : on|pending|off
 *     url_unsuscribe : url de desabonnement
 */
function newsletter_suscriber_dist($email){

	return false;
}