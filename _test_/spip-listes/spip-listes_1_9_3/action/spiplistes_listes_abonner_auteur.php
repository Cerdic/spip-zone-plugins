<?php

// action/spiplistes_listes_abonner_auteur.php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/spiplistes_afficher_auteurs');
include_spip('inc/spiplistes_api_presentation');

//CP-20080603
// principalement utilisé par exec/spiplistes_liste_gerer.php
function action_spiplistes_listes_abonner_auteur_dist () {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = intval($securiser_action());
	$id_liste = intval(_request('id_liste'));
	$id_auteur = max(_request('select_abo'), _request('nouv_auteur'));
	$action = _request('action');
	$tri = urldecode(_request('tri'));
	$redirect = urldecode(_request('redirect'));
	
	if(($id_liste > 0) && ($id_auteur > 0)) {
		include_spip('inc/spiplistes_listes_selectionner_auteur');
		spiplistes_abonnements_ajouter ($id_auteur, $id_liste);
		echo(spiplistes_listes_boite_abonnements($id_liste, 'liste', $tri, $redirect));
	}
	exit;
} //
?>