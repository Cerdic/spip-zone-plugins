<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// classe PHP et les services SOAP
include_spip('inc/soapsympa_trustedapp');

function utf8DecodeArray($arr) {
	$utf8DecodedArray = array();
	foreach ($arr as $key => $value) {
		if (is_array($value)) {
			$utf8DecodedArray[$key] = utf8DecodeArray($value);
			continue;
        	}
		$utf8DecodedArray[$key] = utf8_decode($value);
	}
	return $utf8DecodedArray;
}


// chargement des valeurs par defaut des champs du formulaire
function formulaires_abosympa_review_charger_dist($nomliste = ""){
	$research = _request('search_abonne');
	// On verifie que la liste est bien accessible
	$conf = unserialize($GLOBALS['meta']['soapsympa']);
	//instanciation de la classe SOAP-SYMPA 
	$Sympa = new SympaTrustedApp($conf['serveur_distant'], $conf['identifiant'], $conf['mot_de_passe']);
	$Sympa->USER_EMAIL = $conf['proprietaire'];	//on recupere l email du listsmaster des listes pour avoir le droit d utiliser le service SOAP
	try { 
		$res = $Sympa->review($nomliste);
		$Abonnes = array(array());
		$i = 0;
		$res = utf8DecodeArray($res);
		foreach ($res as $abonne) {
		    if ($_POST['search_abonne_submit']) {//sin on effectue une recherche on affiche que les abonnes correspondants
		      
			if(substr_count($abonne,$research) != 0) {
			    $Abonnes[$i]['email'] = $abonne;
			}
			}else{//sinon on affiche tous les abonnes
			    $Abonnes[$i]['email'] = $abonne;
			}
			$i++;
		    
		}
		$Listname = explode("@",$List);
		$valeurs['listname'] = $nomliste;
		$valeurs['abonnes'] = $Abonnes;
	} catch(SoapFault $fault) {
		$valeurs['message_erreur'] .= _T("soapsympa:abonnes_liste_pas_droit");
	}
	
	return $valeurs;
}


function formulaires_abosympa_review_verifier_dist($nomliste = ""){

	//initialise le tableau des erreurs
	$erreurs = array();
	if ($_POST['desabonnement']) {
	    // Faire une fonction de verif si les cases sont coches
	    $case = _request('emails');
	}elseif ($_POST['search_abonne_submit']) {
	    $case = _request('search_abonne');
	}
	
	if($case == ''){
	    $erreurs['erreur_liste'] = _T("soapsympa:no_list_selected");
	    spip_log("Aucune liste selectionnee","soapsympa");
	}
	

   	//message d'erreur
   	if (count($erreurs)) {
     		$erreurs['message_erreur'] .= _T('soapsympa:verifier_formulaire');
   	}

   	return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera resoumis
}

function formulaires_abosympa_review_traiter_dist($nomliste = ""){
	
	if ($_POST['desabonnement']) {
			$conf = unserialize($GLOBALS['meta']['soapsympa']);
			//instanciation de la classe SOAP-SYMPA 
			$Sympa = new SympaTrustedApp($conf['serveur_distant'], $conf['identifiant'], $conf['mot_de_passe']);
			//recuperation des emails coches
			$emails = _request('emails', true);

			$message = null;

			
			$nb_listes = 0;
			foreach($emails as $id_abosympa) {
			      $nb_listes++;
			      $Sympa->USER_EMAIL = $conf['proprietaire'];	//pour cette action DELETE de SYMPA l'email est celui du listmaster
			      try {
				  $soapResult = $Sympa->del(_request('list'), $id_abosympa, true);
				  $probleme=false;
					  
			      } catch (SoapFault $ex) {
				  $msg = $ex->detail ? $ex->detail : $ex->faultstring;
				  $message_listes = "<strong>". _T('pass_erreur_probleme_technique')."</strong></li>";
				  $probleme=true;
			      }
			}
			$message_listes .= "<br class='nettoyeur' />";
			if ($probleme==false) $message .= _T("soapsympa:message_confirmation_d");
			$message .= $message_listes;
			
			if ($probleme==false) 	return $message;
			
			else return $message_listes;
	}
	
}
?>