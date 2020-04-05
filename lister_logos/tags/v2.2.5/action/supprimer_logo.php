<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_supprimer_logo_dist($objet = null, $id_objet = null, $etat = 'on') {
	// appel direct depuis une url avec arg = "objet/id"
	if (is_null($objet) or is_null($id_objet)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
		list($objet, $id_objet, $etat) = array_pad(explode('/', $arg, 3), 3, null);
	}

	// appel incorrect ou depuis une url erronnée interdit
	if (is_null($objet) or is_null($id_objet)) {
		include_spip('inc/minipres');
		echo minipres(_T('info_acces_interdit'));
		die();
	}
	logo_supprimer($objet, $id_objet, $etat);
}

/**
 * Supprimer le logo d'un objet
 *
 * @param string $objet
 * @param int $id_objet
 * @param string $etat
 *     `on` ou `off`
 */
function logo_supprimer($objet, $id_objet, $etat) {
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	$objet = objet_type($objet);
	$primary = id_table_objet($objet);
	include_spip('inc/chercher_logo');

	// existe-t-il deja un logo ?
	$logo = $chercher_logo($id_objet, $primary, $etat);
	if ($logo) {
		spip_unlink($logo[0]);
	}
}
