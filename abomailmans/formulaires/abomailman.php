<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2008                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/abstract_sql');

// chargement des valeurs par defaut des champs du formulaire
function formulaires_abomailman_charger_dist(){
	global $visiteur_session;
	
	//initialise les variables d'environnement pas défaut
	$valeurs = array();
	
	// Si le visiteur est logué au site on utilise ses information de connexion par défaut
	$valeurs['email'] = _request('email') ? _request('email') : $visiteur_session['email'];
	$valeurs['nom'] = _request('nom') ? _request('nom') : $visiteur_session['nom'];
	$valeurs['listes'] = _request('listes');
	
	// on verifie s'il existe des listes disponibles 
	$nb_listes = sql_count(sql_select('id_abomailman','spip_abomailmans'));	
	
	if($nb_listes>0){
		$valeurs['editable'] = true;
		spip_log("ABOMAILMAN : il existe $nb_listes listes","abomailman");
	}
	else{
		$valeurs['editable'] = false;
		spip_log("ABOMAILMAN : Il n'y a pas de listes disponibles","abomailman");
	}
	return $valeurs;
}


function formulaires_abomailman_verifier_dist(){

	// récupération des valeurs du formulaire
	$nom = _request('nom');
	$email = _request('email');
	$listes = _request('listes', true);
	$abonnement = _request('abonnement');
	$desabonnement = _request('desabonnement');

	//charge la fonction de controle du login et mail
	//$test_inscription = charger_fonction('test_inscription');

	//initialise le tableau des erreurs
	$erreurs = array();

	// Faire une fonction de verif sur le mail pour validite
	
	if($email == ''){
		$erreurs['email'] = _T("abomailmans:email_oublie");
		spip_log("Aucun email n'est inséré","abomailman");
	}
	else{
		include_spip('inc/filtres'); # pour email_valide()
		if (!email_valide($email)){
			$erreurs['email'] = _T("form_email_non_valide");
			spip_log("Email non valide $email","abomailman");
		}
		else{
			spip_log("Email = $email;","abomailman");
		}
	}
	
	if(empty($listes)){
		$erreurs['listes'] = _T("abomailmans:choisir_liste");
	}
	
    //message d'erreur genéralisé
    if (count($erreurs)) {
        $erreurs['message_erreur'] .= _T('abomailmans:verifier_formulaire');
    }

    return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}

function formulaires_abomailman_traiter_dist(){
	// Pour l'envoi de l'email
	include_spip('inc/abomailmans');
	
	$nom = _request('nom');
	$email = _request('email');
	$listes = _request('listes', true);
	$abonnement = _request('abonnement');
	$desabonnement = _request('desabonnement');
	
	$message = '';
	
	$message_listes = "<ul>";
	
	$nb_listes = 0;
	foreach($listes as $liste) {
		$nb_listes++;
		$listes_datas = sql_fetsel("*","spip_abomailmans","id_abomailman = $liste");

		// 1er cas : c'est une liste MAILMAN
		if($listes_datas['email_sympa'] == '') {
			$liste_email = explode ("@", $listes_datas['email']);
			// cas de l'abonnement : on rajoute -join dans l'email de la liste 
			if(!empty($abonnement)){
				$liste_email = $liste_email[0]."-join@".$liste_email[1];
			}
			// sinon pour le désabonnement on rajoute -leave
			else{
				$liste_email = $liste_email[0]."-leave@".$liste_email[1];
			}
			if (abomailman_mail ($nom, $email, $liste_email, $liste_email)) {
				$message_listes  .= "<li><strong>". $listes_datas['titre'] ."</strong> (".$listes_datas['email'].")</li>";
			}
		}

		// 2eme cas : c'est une liste SYMPA (présence de deux @ à suivre)
		else {
			spip_log("on s'abonne à sympa","abomailmans");
			$proprio_email = $listes_datas['email_sympa'];
			$sujet = (empty($abonnement)) ? 'UNSUBSCRIBE ' : 'SUBSCRIBE ';
			$sujet .= $listes_datas['email'].' ';
			$sujet .= (empty($desabonnement)) ? $nom : '';
			if (abomailman_mail($nom, $email, $listes_datas['titre'], $proprio_email, $sujet)) {
				$message_listes .= "<li><strong>".$listes_datas['titre']."</strong> (". $listes_datas['email'] .")</li>";
			}
		}
	}
	$message_listes .= "</ul>";
	
	if($abonnement){
		if($nb_listes>1){
			$message .= _T("abomailmans:message_confirmation_a");
		}else{
			$message .= _T("abomailmans:message_confirmation_unique_a");
		}
	} else{
		if($nb_listes>1){
			$message .= _T("abomailmans:message_confirmation_d");
		}else{
			$message .= _T("abomailmans:message_confirmation_unique_d");
		}
	}
	
	$message .= $message_listes;
	$message .= "<p>" . _T("abomailmans:message_confirm_suite") . "</p>";

	return $message;
}
?>