<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// classe PHP et les services SOAP
include_spip('inc/soapsympa_trustedapp');

    

function formulaires_abosympa_charger_dist(){
	global $visiteur_session;

	//initialise les variables d'environnement par défaut
	$valeurs = array();

	// Si le visiteur est logue au site on utilise ses informations de connexion par défaut
	$valeurs['email'] = _request('email') ? _request('email') : $visiteur_session['email'];
	$valeurs['nom'] = _request('nom') ? _request('nom') : $visiteur_session['nom'];
	
	//on récupere les réglages du plugins (clés du serveur Sympa)
	$conf = unserialize($GLOBALS['meta']['soapsympa']);

	//instanciation de la classe SOAP-SYMPA 
	$Sympa = new SympaTrustedApp($conf['serveur_distant'], $conf['identifiant'], $conf['mot_de_passe']);
  
	$Sympa->USER_EMAIL = $conf['proprietaire'];	//on recupere l email du listsmaster  pour avoir le droit d utiliser le service SOAP 
	$res = $Sympa->complexLists($Sympa->USER_EMAIL);

	if (isset($res) && gettype($res) == 'array') {
	      
	      $Listes = array(array());
	      $i = 0;
	      foreach ($res as $list) {
		    list ($list->listName,$list->listDomain) = explode("@",$list->listAddress);
		    $Listes[$i]['listname'] = $list->listName ;
		    $Listes[$i]['subject'] = $list->subject ;
		    
		    $i++;
		  }
	  }  
	     

		$valeurs['listes'] = $Listes;
	
		//on retourne la liste des listes au formulaire d abonnement

	return $valeurs;
}


function formulaires_abosympa_verifier_dist(){

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
		$erreurs['erreur_email'] = _T("soapsympa:email_oublie");
	}
	else{
		include_spip('inc/filtres'); # pour email_valide()
		if (!email_valide($email)){
			$erreurs['email'] = _T("form_email_non_valide");
		}
		else{
			spip_log("Email = $email;","soapsympa");
			
		}
	}

	if(empty($listes)){
		$erreurs['listes'] = _T("soapsympa:choisir_liste");
	}

    //message d'erreur generalise
    if (count($erreurs)) {
        $erreurs['message_erreur'] .= _T('soapsympa:verifier_formulaire');
    }

    return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera ressoumis
}

function formulaires_abosympa_traiter_dist(){

$conf = unserialize($GLOBALS['meta']['soapsympa']);
//instanciation de la classe SOAP-SYMPA 
$Sympa = new SympaTrustedApp($conf['serveur_distant'], $conf['identifiant'], $conf['mot_de_passe']);


	$nom = _request('nom');
	$email = _request('email');
	$listes = _request('listes', true);
	$abonnement = _request('abonnement');
	$desabonnement = _request('desabonnement');

	$message = null;

	$message_listes = "<ul>";

	$nb_listes = 0;
	foreach($listes as $id_abosympa) {
		$nb_listes++;

		//on initialise l'envoi
		// on traite chaque liste via une fonction reutilisable ailleurs
		$Sympa->USER_EMAIL = $email;	//pour cette action SUBSCRIBE de SYMPA l'email
	if($abonnement){
		try {
		    $soapResult = $Sympa->subscribe($id_abosympa, $nom);
		    $message .= _T("soapsympa:message_confirmation_a");
		    
		} catch (SoapFault $ex) {
		    $msg = $ex->detail ? $ex->detail : $ex->faultstring;
		  $message_listes .= "<li><strong>". _T('pass_erreur_probleme_technique')."</strong></li>";
			$probleme=true;
		}
		

		
	}


      if($desabonnement){

      try {
		    $soapResult = $Sympa->signoff($id_abosympa, $email);
		    $message .= _T("soapsympa:message_confirmation_d");
		    
		} catch (SoapFault $ex) {
		    $msg = $ex->detail ? $ex->detail : $ex->faultstring;
		  $message_listes .= "<li><strong>". _T('pass_erreur_probleme_technique')."</strong></li>";
			$probleme=true;
		}

}


}

	

	

	if ($probleme==false)
		return $message;
	else
		return $message_listes;
}
?>