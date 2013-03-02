<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// classe PHP et les services SOAP
include_spip('inc/soapsympa_trustedapp');

// chargement des valeurs par defaut des champs du formulaire
function formulaires_abosympa_prive_charger_dist($nomliste = ""){

	//initialise les variables d'environnement pas defaut
	//$valeurs = array('email');

	// On verifie que la liste est bien accessible

	$conf = unserialize($GLOBALS['meta']['soapsympa']);
	//instanciation de la classe SOAP-SYMPA 
	$Sympa = new SympaTrustedApp($conf['serveur_distant'], $conf['identifiant'], $conf['mot_de_passe']);
	$Sympa->USER_EMAIL = $conf['proprietaire'];	//on recupere l email du proprio des listes pour avoir le droit d utiliser le service SOAP
	$res = $Sympa->info($nomliste);

	if (!isset($res)) {
	$valeurs['message_erreur'] = _T("soapsympa:liste_non_existante");
	}else{
	$valeurs['listname'] = $nomliste;
	}
	return $valeurs;
}


function formulaires_abosympa_prive_verifier_dist($nomliste = ""){

	//initialise le tableau des erreurs
	$erreurs = array();

	// Faire une fonction de verif sur le mail pour validite
	$email = _request('email');
	
	if($email == ''){
		$erreurs['erreur_email'] = _T("soapsympa:email_oublie");
		spip_log("Aucun email n'est insere","soapsympa");
	}
	else{
		include_spip('inc/filtres'); # pour email_valide()
		if (!email_valide($email)){
			$erreurs['erreur_email'] = _T("soapsympa:email_valide");
			spip_log("Email non valide $email","soapsympa");
		}
		else{
			spip_log("Email = $email;","soapsympa");
		}
	}

   //message d'erreur
   if (count($erreurs)) {
      $erreurs['message_erreur'] .= _T('soapsympa:verifier_formulaire');
   }

   return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}

function formulaires_abosympa_prive_traiter_dist($nomliste = ""){
	
    $nom = _request('nom');
    $email = _request('email');
    $message = null;

    $conf = unserialize($GLOBALS['meta']['soapsympa']);
    //instanciation de la classe SOAP-SYMPA 
    $Sympa = new SympaTrustedApp($conf['serveur_distant'], $conf['identifiant'], $conf['mot_de_passe']);
    
    $message_listes = "<ul>";
    $Sympa->USER_EMAIL = $conf['proprietaire'];	//pour cette action SUBSCRIBE de SYMPA l'email
	
		try {
		    $soapResult = $Sympa->add($nomliste, $email, true);
		    $message .= _T("soapsympa:message_confirmation_a");
		    $probleme=false;
		    
		} catch (SoapFault $ex) {
		    $msg = $ex->detail ? $ex->detail : $ex->faultstring;
		  $message_listes .= "<li><strong>". _T('pass_erreur_probleme_technique')."</strong></li>";
			$probleme=true;
		}
		


      $message_listes .= "</ul><br class='nettoyeur' />";

      $message .= $message_listes;
	
      if ($probleme==false) return $message;
      else return $message_listes;
}
?>