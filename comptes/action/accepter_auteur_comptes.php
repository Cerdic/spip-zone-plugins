<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Valider un auteur comptes
 *
 */
function action_accepter_auteur_comptes_dist() {
	$securiser_action = charger_fonction('securiser_action','inc');
	$id = $securiser_action();
	accepter_auteur_comptes($id);
	return true;
}

function accepter_auteur_comptes($comptes){
	include_spip('base/abstract_sql');
	include_spip('formulaires/oubli');

	$auteur = sql_allfetsel('id_auteur','spip_auteurs_comptes_specifique',"id_comptes='$comptes'");
	$id_auteur = $auteur[0]['id_auteur'];
	$req_email = sql_allfetsel('email','spip_auteurs',"id_auteur='$id_auteur'");
	$email = $req_email[0]['email'];
		
	// On envoit un mail pour générer le mot de pass
	$message = message_oubli($email,'p');
	
	// On change le statut
	include_spip('inc/acces');
	$n = sql_updateq('spip_auteurs',array('statut'=>'comptes'),"id_auteur=".$id_auteur);
	ecrire_acces();	

	if($message){
		spip_log("Acceptation de l'auteur $id_auteur");
		echo _T('gestion:acceptation_auteur', array('id'=>$id_auteur));
		return true;
	}	
	return false;
}
