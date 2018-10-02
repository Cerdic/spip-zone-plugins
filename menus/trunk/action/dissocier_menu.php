<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_dissocier_menu_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	list($id_menu, $objet, $id_objet) = explode('-', $arg);

	include_spip('action/editer_liens');
	objet_dissocier(array('menu' => $id_menu), array($objet => $id_objet));

	return $entrees;
}
