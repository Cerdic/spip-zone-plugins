<?php

// action/spiplistes_liste_des_abonnes.php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/spiplistes_afficher_auteurs');
include_spip('inc/spiplistes_api_presentation');
include_spip('inc/spiplistes_listes_selectionner_auteur');

//CP-20080603
// principalement utilisé par exec/spiplistes_liste_gerer.php
// et exec/spiplistes_listes_toutes.php
function action_spiplistes_liste_des_abonnes_dist () {

	include_spip('inc/autoriser');
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if(!preg_match(",^(\d+) (\d+) (\S+)$,", $arg, $r)) {
		spiplistes_log("action_spiplistes_liste_des_abonnes_dist $arg pas compris");
		return;
	}
	$id_liste = intval($r[1]);
	$debut = intval($r[2]);
	$tri = $r[3];

	echo(spiplistes_listes_boite_abonnes($id_liste, $tri, $debut, $redirect));
	
	exit(0);

} //
?>