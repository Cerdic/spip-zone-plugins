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

include_spip('inc/session');
if (!defined("_ECRIRE_INC_VERSION")) return;

	  
	  error_reporting(E_ALL);
// Authentifie via PMB et retourne la ligne SQL decrivant l'utilisateur si ok

// http://doc.spip.org/@inc_auth_ldap_dist
function auth_thelia_dist ($login, $pass, $md5pass="", $md5next="") {
			
	spip_log("thelia2 $login " . ($pass ? "mdp fourni" : "mdp absent"). ($md5pass ? "md5mdp fourni" : "md5mdp absent"));
	//connexion webservices pmb
	if (!file_exists("fonctions/action.php") ) {
	     spip_log("fichier thelia trouve");
	      return array();
	}
	//récupérer le nom de la base spip
	if ($result = mysql_query("SELECT DATABASE()")) {
	  $row = mysql_fetch_row($result);
	  $spip_db = $row[0];
	}
	spip_log("test1, db courante=".$spip_db);
	  $res =" foo ";
	 ob_start();
	  include_once('fonctions/moteur.php');
	  ob_end_clean();
	  //include_once('classes/Client.class.php');
	 spip_log("test2");
	
		      
	// Securite 
	if (!$login || (!$pass && !$md5pass)) return array();

		     
	 

	// Utilisateur connu ?
		$client = New Client();
		$rec = $client->charger($login, $pass);

		//revenir sur la base spip
		mysql_select_db($spip_db);

		if($rec) {
			spip_log("thelia $login utilisateur connu");
			$_SESSION['navig']->client = $client;
			$_SESSION['navig']->connecte = 1; 
			modules_fonction("apresconnexion");
		      
		      // importer les infos depuis thelia, 
		      // avec le statut par defaut a l'install
		      // refuser d'importer n'importe qui 
		      if (!$statut = $GLOBALS['thelia_statut_nouvel_auteur']) return array();
		      
		      
		      
		      if ($result = sql_fetsel("*", "spip_auteurs", "login=" . sql_quote($login) . " AND source='thelia'")) {
			
			    return $result;
		      }
		      
		      // Recuperer les donnees de l'auteur
		      // Convertir depuis UTF-8 (jeu de caracteres par defaut)
		      include_spip('inc/charsets');
		      $nom = $client->nom.' '.$client->prenom;
		      $email = $login;
		      //$login = strtolower(importer_charset($resultpmb->cb, 'utf-8'));
		      $bio = '';
		      $n = sql_insertq('spip_auteurs', array(
				      'source' => 'thelia',
				      'nom' => $nom,
				      'login' => $login,
				      'email' => $email,
				      'bio' => $bio,
				      'statut' => $statut,
				      'pass' => ''),0);
		       spip_log("Creation de l'auteur '$nom' depuis thelia dans spip_auteurs id->".$n);
		    
		      
		      spip_log("test6");
		       if ($n)	return sql_fetsel("*", "spip_auteurs", "id_auteur=$n");


		      spip_log("Creation de l'auteur '$nom' depuis thelia impossible");
		      
		      
		      
		      return array(); 
		      
	      } else {
		     //utilisateur inconnu
		   spip_log("thelia $login utilisateur inconnu");
		    return array();  
	      }
	
	return array();
      
}

?>
