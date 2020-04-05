<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Une action pour facilement supprimer un logo
 * Exemple
 *
 * ```
 * [(#BOUTON_ACTION{Supprimer, #URL_ACTION_AUTEUR{supprimer_logo, #ENV{objet}-#ENV{id_objet}, #SELF}, button expanded alert ajax})]
 * ```
 *
 * @param string $arg
 * @access public
 */
function action_supprimer_logo_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// Extraire les éléments
	list($objet, $id_objet) = explode('-', $arg);

	if (!autoriser('iconifier', $objet, $id_objet)) {
		include_spip('inc/minipres');
		echo minipres(_T('info_acces_interdit'));
		die();
	}

	include_spip('action/editer_logo');
	logo_supprimer($objet, $id_objet, 'on');

	// On invalide les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='$objet/$id_objet'");
}
