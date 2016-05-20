<?php
/*
 * Plugin mesfavoris
 * (c) 2009-2012 Olivier Sallou, Cedric Morin
 * Distribue sous licence GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_supprimer_favori_dist($id_favori) {
	if (is_null($id_favori)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$id_favori = $securiser_action();
	}

	include_spip('inc/mesfavoris');
	include_spip('inc/autoriser');
	
	if(autoriser('modifier', 'favori', $id_favori)) {
		mesfavoris_supprimer(array('id_favori'=>$id_favori));
	}
}
