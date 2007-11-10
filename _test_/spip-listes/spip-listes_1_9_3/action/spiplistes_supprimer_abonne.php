<?php
// SPIP-Listes

// _SPIPLISTES_ACTION_SUPPRIMER_ABONNER

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

/*
	Supprime l'auteur (visiteur) demandé
	retourne sur redirect si précisé
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/spiplistes_api_globales');

function action_spiplistes_supprimer_abonne_dist () {

	include_spip('inc/autoriser');
	include_spip('inc/spiplistes_api');

	// les globales ne passent pas en action
	//global $connect_id_auteur;
	$connect_id_auteur = $GLOBALS['auteur_session']['id_auteur'];
		
//spiplistes_log("action_spiplistes_supprimer_abonne_dist() <<", LOG_DEBUG);

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_auteur = intval($securiser_action());
	$redirect = urldecode(_request('redirect'));

	if (autoriser('supprimer', 'auteur', $id_auteur)) {
	
		$result = spip_query("SELECT id_auteur,statut FROM spip_auteurs WHERE id_auteur=$id_auteur LIMIT 1");
		
		if ($row = spip_fetch_array($result)) {
		
			$id_auteur = $row['id_auteur'];
			$statut = $row['statut'];

			if($statut=='6forum') {
				spip_query("DELETE FROM spip_auteurs_courriers WHERE id_auteur=$id_auteur");
				//spip_query("DELETE FROM spip_auteurs_listes WHERE id_auteur=$id_auteur");
				spip_query("UPDATE spip_auteurs SET statut='5poubelle' WHERE id_auteur=$id_auteur LIMIT 1");
				spip_query("DELETE FROM `spip_auteurs_elargis` WHERE id_auteur=$id_auteur LIMIT 1");

				// garde une petite trace...
				spiplistes_log("ID_AUTEUR #$id_auteur deleted by ID_AUTEUR #$connect_id_auteur");
			}
		}
	}	
	
	if($redirect) {
		redirige_par_entete(str_replace("&amp;", "&", $redirect));
	}
}

?>