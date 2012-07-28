<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// classe PHP et les services SOAP
include_spip('inc/soapsympa_trustedapp');

// chargement des valeurs par defaut des champs du formulaire
function formulaires_abosympa_auteurinfos_charger_dist($email = ""){
    
	$conf = unserialize($GLOBALS['meta']['soapsympa']);
	//instanciation de la classe SOAP-SYMPA 
	$Sympa = new SympaTrustedApp($conf['serveur_distant'], $conf['identifiant'], $conf['mot_de_passe']);
	$Sympa->USER_EMAIL = $email;	//on recupere l email de l auteur
	$res = $Sympa->which(); //service SOAP

	if (isset($res) && gettype($res) == 'array') {
		$ListeAbo = array(array());
		$i = 0;
		foreach ($res as $list) {
		
		$ListeAbo[$i]['statut'] = $list[isOwner] ? "propriétaire" : ($list[isEditor] ? "modérateur" : "utilisateur");
		$ListeAbo[$i]['subject'] = utf8_decode($list[subject]);
		$ListeAbo[$i]['listaddress'] = utf8_decode($list[listAddress]);
		$listname = explode("@",$list[listAddress]);
		$ListeAbo[$i]['listname'] = utf8_decode($listname[0]);
		$i++;          
		}
		}
	  
		// Autres LISTS 
	    
		$res = $Sympa->complexlists($Sympa->USER_EMAIL);
		if (isset($res) && gettype($res) == 'array') {
		$ListeNonAbo = array(array());
		$i = 0;
		foreach ($res as $list) {
		    list ($list->listName,$list->listDomain) = explode("@",$list->listAddress);
		    $res1 = $Sympa->ami($list->listAddress,"subscriber", $Sympa->USER_EMAIL);
		    $res2 = $Sympa->ami($list->listAddress,"owner", $Sympa->USER_EMAIL);
		    $res3 = $Sympa->ami($list->listAddress,"editor", $Sympa->USER_EMAIL);

		    if (($res1)||($res2)||($res3)) {
			next;//on n'affiche pas les listes auxquelle l utilisateur est deja abonné ou est propriétaire
		    }else{  
		      $ListeNonAbo[$i]['listaddress'] = utf8_decode($list->listAddress) ;
		      $ListeNonAbo[$i]['listname'] = utf8_decode($list->listName) ;
		      $ListeNonAbo[$i]['subject'] = utf8_decode($list->subject) ;
		      $i++;
		    }
		}
	}

	$Listname = explode("@",$List);
		
	$contexte = array(
	      'abonne' => $ListeAbo,
	      'nonabonne' => $ListeNonAbo
	      );	
	  


	$valeurs['listname'] = $Listname[0];
	$valeurs['abonne'] = $contexte['abonne'];
	$valeurs['nonabonne'] = $contexte['nonabonne'];
	
	return $valeurs;
}


function formulaires_abosympa_auteurinfos_verifier_dist($email= ""){

	//initialise le tableau des erreurs
	$erreurs = array();
	$abonnement = _request('abonnement');
	$desabonnement = _request('desabonnement');
	// Faire une fonction de verif si les cases sont coches
	if($desabonnement) $case = _request('listes_des');
	if($abonnement) $case = _request('listes_ab');
	if($case == ''){
		$erreurs['erreur_liste'] .= _T("soapsympa:no_list_selected");
		spip_log("Aucune liste selectionnee","soapsympa");
	}
	

   	//message d'erreur
   	if (count($erreurs)) {
      	$erreurs['message_erreur'] .= _T('soapsympa:verifier_formulaire');
   	}

	return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}

function formulaires_abosympa_auteurinfos_traiter_dist($email = ""){
	$abonnement = _request('abonnement');
	$desabonnement = _request('desabonnement');
	//recuperation des listes coches
	if($desabonnement) $lists = _request('listes_des');
	if($abonnement) $lists = _request('listes_ab');

	$message = null;

	$conf = unserialize($GLOBALS['meta']['soapsympa']);
	//instanciation de la classe SOAP-SYMPA 
	$Sympa = new SympaTrustedApp($conf['serveur_distant'], $conf['identifiant'], $conf['mot_de_passe']);

	$nb_listes = 0;
	foreach($lists as $id_abosympa) {
	$nb_listes++;
	$Sympa->USER_EMAIL = $conf['proprietaire'];	//pour ces actions DELETE et ADD de SYMPA l'email est celui du listmaster
	   

	try {
	    if($desabonnement) $soapResult = $Sympa->del($id_abosympa, $email, true);
	    if($abonnement) $soapResult = $Sympa->add($id_abosympa, $email, true);
	    $probleme=false;
		    
	} catch (SoapFault $ex) {
	    $msg = $ex->detail ? $ex->detail : $ex->faultstring;
	    $message_listes = "<strong>". _T('pass_erreur_probleme_technique')."</strong>";
	    $probleme=true;
		}

	}
	
	$message_listes .= "<br class='nettoyeur' />";
	if ($probleme==false) {
	if($desabonnement) $message .= _T("soapsympa:message_confirmation_d");
	if($abonnement) $message .= _T("soapsympa:message_confirmation_a");
	}
	$message .= $message_listes;
	
	
	if ($probleme==false) 	return $message;
	 
	else return $message_listes;
}
?>
