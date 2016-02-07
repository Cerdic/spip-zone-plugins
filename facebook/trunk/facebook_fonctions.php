<?php
/**
 * Fonctions utiles au plugin Facebook
 *
 * @plugin     Facebook
 * @copyright  2016
 * @author     vertige
 * @licence    GNU/GPL
 * @package    SPIP\Facebook\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/facebook');

function balise_FACEBOOK_LOGIN_dist($p) {

	$action = interprete_argument_balise(1, $p);

	include_spip('inc/facebook');
	$p->code = "facebook_lien_connection($action)";

	return $p;
}
