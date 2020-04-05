<?php
/*
 * Plugin Alertes
 * Distribué sous licence GPL
 *
 * Ajout d'une Alerte. Fonction reprise du plugin Mes favoris de Olivier Sallou, Cedric Morin.
 */

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function action_ajouter_alerte_dist($arg = null) {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$arg = explode("-", $arg);
	$objet = $arg[0];
	$id_objet = $arg[1];
	if (count($arg) > 2) {
		$id_auteur = $arg[2];
	} else {
		$id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
	}

	include_spip('inc/alertes');
	alertes_ajouter($id_objet, $objet, $id_auteur);
}