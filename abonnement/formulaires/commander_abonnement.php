<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * 
 * Chargement des valeurs par defaut des champs du formulaire
 * 
 * @return array L'ensemble des champs et de leur valeurs
 */
function formulaires_commander_abonnement_charger_dist(){
	
	// si pas d'id_auteur, on prend la personne identifiee si elle existe
	// sinon c'est une inscription.
	$erreurs = array();
	$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	if (!$id_auteur) {
			// inscription ?
			$erreurs = array(
				"message_erreur" => _T('abo:erreur_identification'),
				"editable" => false,
			);
	}
	
	// creer un hash unique pour la transaction
	include_spip('inc/acces');
	$hash = creer_uniqid();
		
	$champs = array(
		"hash" => $hash,
		"id_auteur" => $id_auteur,
		"abonnement" => "",
		"type_commande" => "abonnement",
	);

	$champs = array_merge($champs, $erreurs);
	
	return $champs;
}



function formulaires_commander_abonnement_verifier_dist(){
   
	//initialise le tableau des erreurs
	$erreurs = array();
	
	// erreurs sur l'abonnement
	if(!_request('abonnement')) {
		$erreurs['abonnement'] = _T("abo:erreur_selection_abonnement");
		$erreurs['message_erreur'] = _T("abo:erreur_presente");
	}

    return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}



function formulaires_commander_abonnement_traiter_dist(){
	global $tables_principales;
	
	$hash = _request('hash');
	$message = " ";
	
	/* c'est un abonnement */
	$id_abonnement = _request('abonnement');
	$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	
	// [attention aux doublons...]
	sql_insertq("spip_auteurs_elargis_abonnements", array(
		"id_auteur" => $id_auteur,
		"id_abonnement" => $id_abonnement,
		"hash" => $hash,
		"date" => date("Y-m-d H:i:s"),
		"statut_paiement" => 'a_confirmer'
	));

	return array('editable'=>false,'message_ok' => $message);

}
?>
