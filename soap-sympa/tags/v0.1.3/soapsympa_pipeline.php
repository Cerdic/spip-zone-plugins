<?php

/**
 * Plugin Soap SYMPA
 * Licence GPL (c) 2012 Thomas Weiss 
**/
if (!defined("_ECRIRE_INC_VERSION")) return;


/**
* appel classe PHP et les services SOAP
**/
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
	  $contexte['email'] = $email;
	  $contexte['id_auteur'] = $Id;
	  $flux['data'] .= recuperer_fond('prive/boite/abonnements', $contexte);	
	  }
      }//fin if auteur

    //Page de configuration et d edition du plugin on affiche la liste des listes 
    if(($exec == 'configurer_soapsympa')||($exec == 'edition_soapsympa')) {
	$Sympa->USER_EMAIL = $conf['proprietaire'];	
	$res = $Sympa->complexLists($Sympa->USER_EMAIL);
	if (isset($res) && gettype($res) == 'array') {
	      
	      $Listes = array(array());
	      $i = 0;
	      foreach ($res as $list) {
		    list ($list->listName,$list->listDomain) = explode("@",$list->listAddress);
		    $Listes[$i]['listaddress'] = utf8_decode($list->listAddress) ;
		    $Listes[$i]['listname'] = utf8_decode($list->listName) ;
		    $Listes[$i]['subject'] = utf8_decode($list->subject) ;
		    $i++;
		    
	      }

	$contexte['listoflists'] = $Listes;
	$flux['data'] .= recuperer_fond('prive/boite/configuration', $contexte, array('ajax'=>true));
	}
    }//fin if exec = configurer_soapsympa


    //Page abonnés d'une liste (review)
    if($exec == 'soapsympa_review') {
        $List = _request('list');	
	$Listname = explode("@",$List);
	$contexte['listname'] = $Listname[0];
	$flux['data'] .= recuperer_fond('prive/boite/abonnes_liste', $contexte, array('ajax'=>true));
    }//fin if exec = review
		
}//fin ensuite retour des valeurs dans le flux
	
return $flux;
}
?>
