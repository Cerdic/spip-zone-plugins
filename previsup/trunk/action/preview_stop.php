<?php
/**
 * Suppression du cookie de Prévisualisation persistante
 *
 * @plugin     Prévisualisation persistante
 * @copyright  2017
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Previsup\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Action pour supprimer le cookie de previsualisation persistante
 **/
function action_preview_stop_dist() {
	// suppression du cookie
	include_spip('inc/cookie');
	spip_setcookie(_COOKIE_PREVISUALISATION_PERSISTANTE, null, time() - 1);
}
