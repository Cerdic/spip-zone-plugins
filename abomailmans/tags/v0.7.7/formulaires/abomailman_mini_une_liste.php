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
function formulaires_abomailman_mini_une_liste_charger_dist($id_abomailman = ""){

	//initialise les variables d'environnement pas defaut
	$valeurs = array('email');

	// On verifie que la liste est bien accessible
	if (! sql_getfetsel('id_abomailman','spip_abomailmans','id_abomailman ='.intval($id_abomailman).' AND desactive = 0')) {
		spip_log("Le numero de liste n'est pas valable : $id_abomailman","abomailmans");
		$valeurs['message_erreur'] = _T("abomailmans:liste_non_existante");
	}
	return $valeurs;
}


function formulaires_abomailman_mini_une_liste_verifier_dist($id_abomailman = ""){

	//initialise le tableau des erreurs
	$erreurs = array();

	// Faire une fonction de verif sur le mail pour validite
	$email = _request('email');
	
	if($email == ''){
		$erreurs['erreur_email'] = _T("abomailmans:email_oublie");
		spip_log("Aucun email n'est insere","abomailmans");
	}
	else{
		include_spip('inc/filtres'); # pour email_valide()
		if (!email_valide($email)){
			$erreurs['erreur_email'] = _T("abomailmans:email_valide");
			spip_log("Email non valide $email","abomailmans");
		}
		else{
			spip_log("Email = $email;","abomailmans");
		}
	}

   //message d'erreur
   if (count($erreurs)) {
      $erreurs['message_erreur'] .= _T('abomailmans:verifier_formulaire');
   }

   return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}

function formulaires_abomailman_mini_une_liste_traiter_dist($id_abomailman = ""){
	include_spip('inc/abomailmans');
   $nom = _request('nom');
	$email = _request('email');

	$message = null;

   // on initialise l'envoi
	// on traite chaque liste via une fonction reutilisable ailleurs
	// on passe abonnement � true d'office
	$traiter=abomailman_traiter_abonnement($id_abomailman,true);
	$titre = $traiter[0];
	$proprio_email=$traiter[1];
	$liste_email=$traiter[2];
	$sujet=$traiter[3];
	$body="$nom - $email ".$traiter[4];
	$headers=$traiter[5];
	
	if (abomailman_mail($nom, $email, $proprio_email,$liste_email, $sujet, $body,$headers)){
		$message_listes  .= "<p><strong>$titre</strong><p>";
	}else{
		$message_listes .= "<p><strong>". _T('pass_erreur_probleme_technique')."</strong></p>";
		$probleme=true;
	} 
		
 	$message .= $body."". _T("abomailmans:message_confirmation_unique_a");
	$message .= $message_listes;
	$message .= "<p>" . _T("abomailmans:message_confirm_suite") . "</p>";

	if ($probleme==false)
		return $message;
	else
		return $message_listes;
}
?>