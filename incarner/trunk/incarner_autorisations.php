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

/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function incarner_autoriser() {
}

/* On n'est autorisé à changer d'utilisateur que si l'on est webmestre, ou qu'on
	 a un cookie connu. */
function autoriser_incarner_dist($faire, $type, $id, $qui, $opt) {

	include_spip('incarner_fonctions');

	if (autoriser('webmestre')
			or (isset($_COOKIE['spip_cle_incarner'])
					and incarner_cle_valide($_COOKIE['spip_cle_incarner']))) {
		return true;
	} else {
		return false;
	}
}

/* Le cookie d'incarnation donne droit aux fonctions de debug */
function autoriser_debug($faire, $type, $id, $qui, $opt) {

	return autoriser('incarner');
}
