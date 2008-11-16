<?php
// action/spiplistes_changer_statut_abonne.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/spiplistes_api_globales');

function action_spiplistes_changer_statut_abonne_dist () {

	// les globales ne passent pas en action
	//global $connect_id_auteur;
	$connect_id_auteur = $GLOBALS['auteur_session']['id_auteur'];

	include_spip('inc/autoriser');
	include_spip(_DIR_PLUGIN_SPIPLISTES.'inc/spiplistes_api');

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$redirect = urldecode(_request('redirect'));
	$id_liste = urldecode(_request('id_liste'));
	$tri = urldecode(_request('tri'));
	$debut = urldecode(_request('debut'));

	$arg = explode('-',$arg);
	$id_auteur = intval($arg[0]);
	$action = $arg[1];

	if(($id_auteur > 0) && ($connect_id_auteur > 0)) {
		
		if ($action=='format') {
			//modification du format abonne ('html', 'texte' ou 'non')
			$statut = _request('statut');
			if(autoriser('modifierformat', 'abonne', $id_auteur)) {
				if(spiplistes_format_abo_modifier($id_auteur, $statut)) {
					spiplistes_log("FORMAT ID_AUTEUR #$id_auteur changed to [$statut] by ID_AUTEUR #$connect_id_auteur");
					if(!$redirect) {
						include_spip('inc/spiplistes_api_presentation');
						include_spip('inc/spiplistes_listes_selectionner_auteur');
						//echo(spiplistes_listes_boite_abonnes($id_liste, $tri, $debut, $script_retour));
						echo(spiplistes_listes_boite_abonnements($id_liste, $statut_liste, $tri, $debut, $redirect, $elligibles, $nb_elligibles));
						exit(0);
					}
				}
			}
		}
		// CP-20080324: l'abonnement par action/ actuellement pas utilise par le formulaire abonnes_tous.
		// A voir si on conserve 
		// CP-20081111: code probablement inutile. Un autre script action fait le meme boulot. A supprimer apres verif
		/* */
		if ($action=='listeabo') {
			//abonne un auteur, force en _SPIPLISTES_FORMAT_DEFAULT si pas de format
			if ($id_auteur 
				&& (($id_liste = intval($arg[2])) > 0)
				&& autoriser('abonnerauteur', 'liste', $id_liste, NULL, array('id_auteur'=>$id_auteur))
				) {
				spiplistes_abonnements_ajouter($id_auteur, $id_liste);
				//attribuer un format de reception si besoin (ancien auteur)
				if(
					(!$abo = spiplistes_format_abo_demande($id_auteur)) 
					|| ($abo == 'non')
				) {
					spiplistes_format_abo_modifier($id_auteur, _SPIPLISTES_FORMAT_DEFAULT);
				}
			}
			spiplistes_log("SUBSCRIBE ID_AUTEUR #$id_auteur to ID_LISTE #$id_liste by ID_AUTEUR #$connect_id_auteur");	
		}
		if ($action=='listedesabo') {
			// desabonne un auteur
			if ($id_liste = intval($arg[2])) {
				if (autoriser('desabonnerauteur', 'liste', $id_liste, NULL, array('id_auteur'=>$id_auteur))) {
					if(spiplistes_abonnements_auteur_desabonner ($id_auteur, $id_liste)) {
						spiplistes_log("UNSUBSCRIBE ID_AUTEUR #$id_auteur from ID_LISTE #$id_liste by ID_AUTEUR #$connect_id_auteur");	
					}
				}
			}
		}
		// CP-20081111: fin de code inutile.
	}
	if ($redirect){
		redirige_par_entete(str_replace("&amp;","&",$redirect)."#abo$id_auteur");
	}
} // action_spiplistes_changer_statut_abonne_dist()

?>