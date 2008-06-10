<?php

// action/spiplistes_moderateurs_gerer.php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/spiplistes_afficher_auteurs');
include_spip('inc/spiplistes_api_presentation');

//CP-20080603
// principalement utilisé par exec/spiplistes_liste_gerer.php
function action_spiplistes_moderateurs_gerer_dist () {
	global $auteur_session;
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	if(!preg_match(",^(\d+) (\d+) (\S+)$,", $arg, $r)) {
		spiplistes_log("action_spiplistes_moderateurs_gerer_dist $arg pas compris");
		return;
	}
	
	$id_liste = intval($r[1]);
	$id_auteur = intval($r[2]);
	$faire = $r[3];
	$select_abo = intval(_request('select_abo'));
	
	if($select_abo > 0) {
		$id_auteur = $select_abo;
	}

	if($id_liste > 0 && $id_auteur > 0) {
		$str_log = "id_liste #$id_liste BY id_auteur #".$auteur_session['id_auteur'];
		switch($faire) {
			case 'ajouter':
				spiplistes_mod_listes_ajouter($id_auteur, $id_liste);
				spiplistes_log("ADD moderator #$id_auteur TO ".$str_log);
				break;
			case 'supprimer':
				spiplistes_mod_listes_supprimer($id_auteur, $id_liste);
				spiplistes_log("DELETE moderator #$id_auteur FROM ".$str_log);
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