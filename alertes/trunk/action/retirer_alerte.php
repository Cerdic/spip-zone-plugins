<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function action_retirer_alerte_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	$arg = explode("-", $arg);
	$objet = $arg[0];
	$id_objet = $arg[1];
	if (count($arg) > 2) {
		$id_auteur = $arg[2];
	} else {
		$id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
	}

	include_spip('inc/alertes');
	alertes_supprimer(array('id_objet' => $id_objet, 'objet' => $objet, 'id_auteur' => $id_auteur));
}