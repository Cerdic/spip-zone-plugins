<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/abstract_sql');

// chargement des valeurs par defaut des champs du formulaire
//une seule liste = [(#FORMULAIRE_ABOMAILMAN{1})]
function formulaires_abomailman_charger_dist($id_abomailman = ""){

	//initialise les variables d'environnement pas défaut
	$valeurs = array();

	// Si le visiteur est logue au site on utilise ses informations de connexion par défaut
	include_spip('inc/session');
	$valeurs['email'] = _request('email') ? _request('email') : session_get('email');
	$valeurs['nom'] = _request('nom') ? _request('nom') : session_get('nom');

	//si id_abomailman est renseigne, on envoie qu'une liste
	if(intval($id_abomailman)){
		$valeurs['id_abomailman'] = $id_abomailman;
		$ok=sql_getfetsel('id_abomailman','spip_abomailmans','id_abomailman ='.intval($id_abomailman).' AND desactive = 0');
	}
	else {
		$valeurs['listes'] = _request('listes');
		// on verifie s'il existe des listes disponibles
		$ok=sql_count(sql_select('id_abomailman','spip_abomailmans'));
	}
	$valeurs['nobot'] = _request('nobot');
	if ($ok)
	return $valeurs;
}


function formulaires_abomailman_verifier_dist($id_abomailman = ""){

	//initialise le tableau des erreurs
	$erreurs = array();

	// recuperation des valeurs du formulaire
	$nom = _request('nom');
	$email = _request('email');
	$listes = _request('listes', true);
	$abonnement = _request('abonnement');
	$desabonnement = _request('desabonnement');

	// Faire une fonction de verif sur le mail pour validite

	if($email == ''){
		$erreurs['email'] = _T("abomailmans:email_oublie");
	}
	else{
		include_spip('inc/filtres'); # pour email_valide()
		if (!email_valide($email)){
			$erreurs['email'] = _T("form_email_non_valide");
		}
		else{
			spip_log("Email = $email;","abomailmans");
			//TODO
			// stocker l'email dans un fichier ou la session, histoire de ne pas se presenter 2 fois
		}
	}

	if(empty($listes)){
		$erreurs['listes'] = _T("abomailmans:choisir_liste");
	}

    //message d'erreur generalise
    if (count($erreurs)) {
        $erreurs['message_erreur'] .= _T('abomailmans:verifier_formulaire');
    }

    return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera ressoumis
}

function formulaires_abomailman_traiter_dist($id_abomailman = ""){
	// Pour l'envoi de l'email
	include_spip('inc/abomailmans');

	// Antispam basique :
	// si l'input invisible a ete renseigne, ca ne peut etre qu'un bot
	if (strlen(_request('nobot'))){
		return array('message_erreur'=>_T('abomailmans:erreur_nobot'));
	}

	$nom = _request('nom');
	$email = _request('email');
	$listes = _request('listes', true);
	$abonnement = _request('abonnement');
	$desabonnement = _request('desabonnement');

	$message = null;

	$message_listes = "<ul>";

	$nb_listes = 0;
	foreach($listes as $id_abomailman) {
		$nb_listes++;

		//on initialise l'envoi
		// on traite chaque liste via une fonction reutilisable ailleurs
		$traiter=abomailman_traiter_abonnement($id_abomailman,$abonnement);
		$titre = $traiter[0];
		$proprio_email=$traiter[1];
		$liste_email=$traiter[2];
		$sujet=$traiter[3];
		$body= array( 
			'texte' => $traiter[4], 
			'nom_envoyeur' => $nom 
		); 
		$headers=$traiter[5];

		// si on veut ajouter un mail de notification ou de test
		/*
		$liste_email = array(
			$liste_email,"verif@exemple.com"
		);
		*/
		if (abomailman_mail($nom, $email, $proprio_email,$liste_email, $sujet, $body,'',$headers)){
			$message_listes  .= "<li><strong>$titre</strong></li>";
		}else{
			$message_listes .= "<li><strong>". _T('pass_erreur_probleme_technique')."</strong></li>";
			$probleme=true;
		}
	}

	$message_listes .= "</ul><br class='nettoyeur' />";

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

	if ($probleme==false)
		return $message;
	else
		return $message_listes;
}
?>
