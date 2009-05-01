<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * 
 * Chargement des valeurs par defaut des champs du formulaire
 * 
 * @return array L'ensemble des champs et de leur valeurs
 * @param int $id_article : Identifiant de l'article commande
 */
function formulaires_commander_article_charger_dist($id_article = 0){

	// pas d'id_article ? on sort !
	if (!$id_article) {
	//	return false;
	}
	
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
		"article" => $id_article,
		"type_commande" => "article",
	);
	
	$champs = array_merge($champs, $erreurs);

	return $champs;
}


function formulaires_commander_article_verifier_dist($id_article = 0){
	//initialise le tableau des erreurs
	$erreurs = array();

	// erreurs sur l'abonnement
	if(!$id_article) {
		$erreurs['message_erreur'] = _T("abo:erreur_presente");
	}

    return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}


function formulaires_commander_article_traiter_dist($id_article = 0){

	$hash = _request('hash');
	$message = " ";

	$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	 
	sql_insertq("spip_auteurs_elargis_articles", array(
		"id_auteur_elargi" => $id_auteur,
		"id_article" => $id_article,
		"statut_paiement" => 'a_confirmer',
		"hash" => $hash,
		"date" => date("Y-m-d H:i:s"),
	));
	
    return array('editable' => false, 'message_ok' => $message);
}
?>
