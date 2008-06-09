<?php

// action/spiplistes_supprimer_abonne.php

// _SPIPLISTES_ACTION_SUPPRIMER_ABONNER

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

/*
	Supprime l'auteur (visiteur) demandé
	retourne sur redirect si précisé
*/

// CP-20080324: ce script de SPIP-Listes-V n'est pas encore utilisé. A conserver

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/spiplistes_api_globales');

function action_spiplistes_supprimer_abonne_dist () {

	include_spip('inc/autoriser');
	include_spip('inc/spiplistes_api');

	// les globales ne passent pas en action
	//global $connect_id_auteur;
	$connect_id_auteur = $GLOBALS['auteur_session']['id_auteur'];
		
//spiplistes_log("action_spiplistes_supprimer_abonne_dist() <<", _SPIPLISTES_LOG_DEBUG);

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_auteur = intval($securiser_action());
	$redirect = urldecode(_request('redirect'));

	if (autoriser('supprimer', 'auteur', $id_auteur)) {
	
		$result = sql_select("id_auteur,statut", "spip_auteurs", "id_auteur=".sql_quote($id_auteur), '','', 1);
		
		if ($row = sql_fetch($result)) {
		
			$id_auteur = intval($row['id_auteur']);
			$statut = $row['statut'];

			if(
				($id_auteur > 0)
				&& ($statut=='6forum') 
			) {
				sql_delete("spip_auteurs_courriers", "id_auteur=".sql_quote($id_auteur));
				sql_update(
					"spip_auteurs"
					, "statut=".sql_quote('5poubelle')
					, "id_auteur=".sql_quote($id_auteur)." LIMIT 1"
				);
				spiplistes_format_abo_modifier($id_auteur, 'non');

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