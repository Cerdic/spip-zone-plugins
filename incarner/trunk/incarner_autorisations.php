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

function autoriser_incarner_dist($faire, $type, $id, $qui, $opt) {

	include_spip('inc/config');

	$clefs = lire_config('incarner/cles');

	if (($clefs and in_array($_COOKIE['spip_cle_incarner'], $clefs))
			or autoriser('webmestre')) {
		return true;
	}

	return false;
}
