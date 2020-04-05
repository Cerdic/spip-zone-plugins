<?php
/**
 * Supprime l'auteur (visiteur) demande
 *
 * _SPIPLISTES_ACTION_SUPPRIMER_ABONNER
 * @package spiplistes
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/spiplistes_api_globales');

/**
 * Supprime l'auteur (visiteur) demande
 * retourne sur redirect si precise
 * @global int $GLOBALS['auteur_session']['id_auteur']
 */
function action_spiplistes_supprimer_abonne_dist () {

	include_spip('inc/autoriser');
	include_spip('inc/spiplistes_api');

	// les globales ne passent pas en action
	//global $connect_id_auteur;
	$connect_id_auteur = $GLOBALS['auteur_session']['id_auteur'];
		
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
				$sql_whereq = "id_auteur=".sql_quote($id_auteur);
				
				if(
						// vide la queue du courrier en attente pour cet abonne'
					spiplistes_courriers_en_queue_supprimer($sql_whereq)
						// supprime l'abonne' des abonnements
					&& spiplistes_abonnements_auteur_desabonner($id_auteur, 'toutes')
						// supprime l'abonne' des formats elargis
					&& spiplistes_format_abo_supprimer($id_auteur)
				) {
					spiplistes_log("ID_AUTEUR #$id_auteur UNSUBSCRIBE BY ID_AUTEUR #$connect_id_auteur");
					  // ne peut supprimer que les invites
					if($statut=='6forum') {
						if(spiplistes_auteurs_auteur_delete($sql_whereq)) {
								// garde une petite trace...
							spiplistes_log("ID_AUTEUR #$id_auteur DELETED BY ID_AUTEUR #$connect_id_auteur");
						}
					}
				}
			}
		}
	}	
	
	if($redirect) {
		redirige_par_entete(str_replace("&amp;", "&", $redirect));
	}
} // action_spiplistes_supprimer_abonne_dist()

