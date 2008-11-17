<?php

// action/spiplistes_listes_abonner_auteur.php

if (!defined("_ECRIRE_INC_VERSION")) return;

/*
 * Ajouter un ID à une liste
 * soit en tant qu'abonné
 * soit en tant que modérateur
 */

include_spip('inc/actions');
include_spip('inc/spiplistes_api_globales');
include_spip('inc/spiplistes_api');
include_spip('inc/spiplistes_api_presentation');

//CP-20080603
// principalement utilise par exec/spiplistes_liste_gerer.php
function action_spiplistes_listes_abonner_auteur_dist () {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = intval($securiser_action());
	$id_liste = intval(_request('id_liste'));
	$ajouter_id_abo = intval(_request('ajouter_id_abo'));
	$ajouter_id_mod = intval(_request('ajouter_id_mod'));
	$nouv_auteur = intval(_request('nouv_auteur'));
	$action = _request('action');
	$tri = urldecode(_request('tri'));
	$debut = intval(_request('debut'));
	$redirect = urldecode(_request('redirect'));
	$cherche_auteur = trim(urldecode(_request('cherche_auteur')));

	// appel de exec/spiplistes_liste_gerer.php ?
	if($id_liste > 0) {
		
		include_spip('inc/spiplistes_listes_selectionner_auteur');
		
		$statut_liste = sql_getfetsel('statut', 'spip_listes', "id_liste=".sql_quote($id_liste), '', '', 1);
		
		if(!empty($cherche_auteur)) {
			// ne rafraichir que le formulaire
			$result = spiplistes_listes_selectionner_elligibles (0, 0, $id_liste, $tri, '', '', '', '', true);
		}
		else if($ajouter_id_abo > 0) {
			spiplistes_abonnements_ajouter($ajouter_id_abo, $id_liste);
			$scrip_exec = urldecode(_request('scrip_exec'));
			$result = spiplistes_listes_boite_abonnes($id_liste, $statut_liste, $tri, $debut, $scrip_exec)
				. spiplistes_listes_boite_elligibles ($id_liste, $statut_liste, $tri, $debut);
		}
	
		// echo(spiplistes_listes_boite_abonnements($id_liste, $statut_liste, $tri, $debut, $redirect));
		echo($result);
	} 
	else {
		
	}
	
	exit(0);
}

?>