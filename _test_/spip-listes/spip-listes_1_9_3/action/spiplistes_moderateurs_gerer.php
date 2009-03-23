<?php

// action/spiplistes_moderateurs_gerer.php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/spiplistes_api_presentation');

//CP-20080603
// utilise' par exec/spiplistes_liste_gerer.php
function action_spiplistes_moderateurs_gerer_dist () 
{
	global $auteur_session;
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	// arg#1: id_liste
	// arg#2: si faire == 'supprimer', id_auteur, sinon 0
	// arg#3: faire
	if(!preg_match(",^(\d+) (\d+) (\S+)$,", $arg, $r)) {
		spiplistes_log("action_spiplistes_moderateurs_gerer_dist $arg pas compris");
		return;
	}
	
	$id_liste = intval($r[1]);
	$id_auteur = intval($r[2]);
	$faire = $r[3];

	//spiplistes_log("action_spiplistes_moderateurs_gerer_dist id_liste: $id_liste, id_auteur: $id_auteur, faire: $faire");
	
	if($id_liste > 0) {

		include_spip('inc/spiplistes_api');
		
		switch($faire) 
		{
			case 'ajouter':
				$id_auteur = intval(_request('ajouter_id_mod'));
				if($id_auteur > 0) {
					spiplistes_mod_listes_ajouter($id_auteur, $id_liste);
				}
				break;
			case 'supprimer':
				if($id_auteur > 0) {
					spiplistes_mod_listes_supprimer($id_auteur, $id_liste);
				}
				break;
		}
	}
	else {
		spiplistes_log("action_spiplistes_moderateurs_gerer_dist $id_liste $id_auteur erreur");
		return;
	}
	
	include_spip('inc/spiplistes_listes_selectionner_auteur');
	echo(spiplistes_listes_boite_moderateurs($id_liste, _SPIPLISTES_EXEC_LISTE_GERER, 'mods-conteneur'));
	
	exit(0);

} //
?>