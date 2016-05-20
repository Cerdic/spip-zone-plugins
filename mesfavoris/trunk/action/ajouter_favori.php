<?php
/*
 * Plugin mesfavoris
 * (c) 2009-2013 Olivier Sallou, Cedric Morin, Gilles Vincent
 * Distribue sous licence GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_ajouter_favori_dist($arg=null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	$arg = explode("-", $arg);
	$objet = $arg[0];
	$id_objet = $arg[1];
	
	if (count($arg)>2) {
		$id_auteur = $arg[2];
	}
	else {
		$id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
	}
	
	if (count($arg)>3) {
		$categorie = $arg[3];
	}
	else {
		$categorie = '';
	}

	include_spip('inc/mesfavoris');
	mesfavoris_ajouter($id_objet, $objet, $id_auteur, $categorie);
}
