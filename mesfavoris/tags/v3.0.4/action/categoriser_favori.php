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

function action_categoriser_favori_dist($arg=null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	$arg = explode("-", $arg, 4);
	$objet = $arg[1];
	$id_objet = $arg[0];
	$id_auteur = $arg[2];
	$categorie = $arg[3];

	include_spip('inc/mesfavoris');
#	include_spip('inc/autoriser');

#	if ( autoriser('modifier', 'favori', $arg) ) {
		mesfavoris_categoriser($id_objet, $objet, $id_auteur, $categorie);
#		mesfavoris_categoriser($arg[0], $arg[1], $arg[2], $arg[3] );
#	}
}
