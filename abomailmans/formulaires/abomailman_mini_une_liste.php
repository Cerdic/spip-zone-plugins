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

//charger cfg
include_spip('cfg_options');   
include_spip('base/abstract_sql');

// chargement des valeurs par defaut des champs du formulaire
function formulaires_abomailman_mini_une_liste_charger_dist($id_abomailman = ""){

	//initialise les variables d'environnement pas défaut
	$valeurs = array('email');

	// On verifie que la liste est bien accessible
	if (sql_getfetsel('id_abomailman','spip_abomailmans','id_abomailman ='.intval($id_abomailman).' AND desactive = 0')) {
		$editable = true;
		spip_log("La liste existe, on peut charger le formulaire","abomailman");
	}
	else{
		$editable = false;
		spip_log("Le numero de liste n'est pas valable","abomailman");
	}
	return array($editable,$valeurs);
}


function formulaires_abomailman_mini_une_liste_verifier_dist($id_abomailman = ""){

	//charge la fonction de controle du login et mail
	//$test_inscription = charger_fonction('test_inscription');

	//initialise le tableau des erreurs
	$erreurs = array();

	// Faire une fonction de verif sur le mail pour validite

	$email = _request('email');
	
	if($email == ''){
		$erreurs['erreur_email'] = _T("abomailmans:email_oublie");
		spip_log("Aucun email n'est inséré","abomailman");
	}
	else{
		include_spip('inc/filtres'); # pour email_valide()
		if (!email_valide($email)){
			$erreurs['erreur_email'] = _T("abomailmans:email_valide");
			spip_log("Email non valide $email","abomailman");
		}
		else{
			spip_log("Email = $email;","abomailman");
		}
	}

   
    //message d'erreur genéralisé
    if (count($erreurs)) {
        $erreurs['message_erreur'] .= _T('abomailmans:verifier_formulaire');
    }

    return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}

function formulaires_abomailman_mini_une_liste_traiter_dist($id_abomailman = ""){
    $message = '';
    spip_log("id de la liste = $id_abomailman","abomailman");
    $email = _request('email');
    // on récupère les données de la liste
    $id_liste = sql_select('*','spip_abomailmans','id_abomailman ='.intval($id_abomailman));
    $liste = sql_fetch($id_liste);
    spip_log($liste,"abomailman");
    include_spip('inc/abomailmans');
    if($liste['email_sympa'] != ''){
		spip_log("on est dans le cas d'une liste sympa","abomailman");
		$message .= _T('login_par_ici');
		$sujet = "SUBSCRIBE ". $liste['email'];
		if(abomailman_mail ($email, $email, $liste['email_sympa'], $liste['email_sympa'], $sujet)) {
			spip_log("on envoie le mail d'abonnement","abomailman");
			$message = _T('abomailmans:sympa_message_confirmation').' '.$email;
		}
    }
    else{
		spip_log("on est dans le cas d'une liste mailman","abomailman");
		$message = _T('abomailmans:mailman_message_confirmation');
    }

    return $message;
}

?>
