<?php

/**
 * Plugin Soap SYMPA
 * Licence GPL (c) 2012 Thomas Weiss 
**/
if (!defined("_ECRIRE_INC_VERSION")) return;


// classe PHP et les services SOAP
include_spip('inc/soapsympa_trustedapp');
include_spip('inc/autoriser');

/**
 * Fonction permettant de tester la connexion au serveur Soap
 *
 * @param string $serveur : adresse du fichier wsdl
 * @param string $ident : identifiant de connexion
 * @param string $psw : mot de passe de connexion
 *
 */
function soapsympa_api_tester($serveur, $ident, $psw) {
	$retour = array() ;
	try {
	$soap = new SympaTrustedApp($serveur, $ident, $psw);
	} catch(SoapFault $fault) {
		$retour['message_erreur'] .= $fault->faultstring;
	}
	return $retour;
}


/**
* Ajout des abonnements sur la page de visualisation d'un auteur
**/

function soapsympa_affiche_milieu($flux) {

$exec = _request('exec');
	  
if(($exec == 'auteur_infos')||($exec == 'configurer_soapsympa')||($exec == 'soapsympa_review')||($exec == 'edition_soapsympa')) {
    //on récupere les réglages du plugins (clés du serveur Sympa)
    $conf = unserialize($GLOBALS['meta']['soapsympa']);

    //instanciation de la classe 
    $Sympa = new SympaTrustedApp($conf['serveur_distant'], $conf['identifiant'], $conf['mot_de_passe']);
    //a remplace par les valeur de meta
   // $Sympa->remote_host = $conf['remote_host']; pas utile pour l (instant
    //$Sympa->SYMPA_ROBOT = $conf['robot']; pas utile pour l (instant

      if(($exec == 'auteur_infos')&&(autoriser('gerer_abonnements'))) {

      $Id = _request('id_auteur');
      $email = sql_getfetsel("email","spip_auteurs","id_auteur=$Id");

	  if($email) {

	  $Sympa->USER_EMAIL = $email;
	

		
	      //liste des listes auxquelles l auteur est abonné. 
	      // service WHICH 
		$res = $Sympa->which();
		if (isset($res) && gettype($res) == 'array') {
		$ListeAbo = array(array());
		$i = 0;
		foreach ($res as $list) {
		
		$ListeAbo[$i]['statut'] = $list[isOwner] ? "propriétaire" : ($list[isEditor] ? "modérateur" : "utilisateur");
		$ListeAbo[$i]['subject'] = $list[subject];
		$ListeAbo[$i]['listaddress'] = $list[listAddress];
		$listname = explode("@",$list[listAddress]);
		$ListeAbo[$i]['listname'] = $listname[0];
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
		      $ListeNonAbo[$i]['listaddress'] = $list->listAddress ;
		      $ListeNonAbo[$i]['listname'] = $list->listName ;
		      $ListeNonAbo[$i]['subject'] = $list->subject ;
		      $i++;
		      }
		}
	    
		  }

	      $contexte = array(
	      'abonne' => $ListeAbo,
	      'nonabonne' => $ListeNonAbo,
	      'email' => $email
	      );



	      $flux['data'] .= recuperer_fond('prive/boite/abonnements', $contexte, array('ajax'=>true));	
		
	  }// fin if email
    }//fin if auteur_infos

    //Page de configuration et d edition du plugin on affiche la liste des listes 
    if(($exec == 'configurer_soapsympa')||($exec == 'edition_soapsympa')) {
	$Sympa->USER_EMAIL = $conf['proprietaire'];	
	$res = $Sympa->complexLists($Sympa->USER_EMAIL);
	if (isset($res) && gettype($res) == 'array') {
	      
	      $Listes = array(array());
	      $i = 0;
	      foreach ($res as $list) {
		    list ($list->listName,$list->listDomain) = explode("@",$list->listAddress);
		    $Listes[$i]['listaddress'] = $list->listAddress ;
		    $Listes[$i]['listname'] = $list->listName ;
		    $Listes[$i]['subject'] = $list->subject ;
		    $i++;
		    
	      }

	$contexte['listoflists'] = $Listes;
	//$flux['data'] .= var_dump($contexte);
	$flux['data'] .= recuperer_fond('prive/boite/configuration', $contexte, array('ajax'=>true));
	  }
    }//fin if exec = configurer_soapsympa


    //Page abonnés d'une liste (review)
    if($exec == 'soapsympa_review') {
	$Sympa->USER_EMAIL = $conf['proprietaire'];
        $List = _request('list');	
	try {
	$res = $Sympa->review($List);

	    
		$Abonnes = array(array());
	      $i = 0;
	      foreach ($res as $abonne) {
		    
		    $Abonnes[$i]['email'] = $abonne;
		    $i++;
		    
	      }
	      $Listname = explode("@",$List);
		
		
	  $contexte = array(
	  'abonnes' => $Abonnes,
	  'listname' => $Listname[0]
	  ); 

	    }catch (SoapFault $ex) {
	    $contexte['erreur'] = 1; 
	    }
	   // $flux['data'] .= var_dump($contexte);
	 $flux['data'] .= recuperer_fond('prive/boite/abonnes_liste', $contexte, array('ajax'=>true));
	      


    }//fin if exec = review

		if ($_GET['subscribe'] == 1) {
		
		$Sympa->USER_EMAIL = $conf['proprietaire'];	//pour cette action ADD de SYMPA, seul le proprietaire est autorisé
		try {
		    $soapResult = $Sympa->add(_request('list'), _request('email'), true);
		    echo '<p class="message">'._T("soapsympa:abonnement_ok").'</p>';
		    
		} catch (SoapFault $ex) {
		    $msg = $ex->detail ? $ex->detail : $ex->faultstring;
		 echo  '<p class="message">'._T("soapsympa:abonnement_erreur").'</p>';
		
		}
	    
		}

		if ($_GET['signoff'] == 1) {
		
		$Sympa->USER_EMAIL = $conf['proprietaire'];//pour cette action DEL de SYMPA, seul le proprietaire est autorisé
		try {
		    $soapResult = $Sympa->del(_request('list'), _request('email'), true);
		   echo '<p class="message">'._T("soapsympa:desabonnement_ok").'</p>';
		} catch (SoapFault $ex) {
		    $msg = $ex->detail ? $ex->detail : $ex->faultstring;
		  echo '<p class="message">'._T("soapsympa:desabonnement_erreur").'</p>';
		}

		}
}//fin ensuite retour des valeurs dans le flux
	
return $flux;
}
?>

