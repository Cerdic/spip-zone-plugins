<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2008                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
 * \***************************************************************************/

include_spip('inc/session');
if (!defined("_ECRIRE_INC_VERSION")) return;


error_reporting(E_ALL);
// Authentifie via Thélia et retourne la ligne SQL decrivant l'utilisateur si ok

// http://doc.spip.org/@inc_auth_ldap_dist
function auth_thelia_dist($login, $pass, $md5pass = "", $md5next = ""){

	if (lire_config("spip_thelia/auth_unique_spip_thelia", "non")=="oui"){

		spip_log("thelia2 $login " . ($pass ? "mdp fourni" : "mdp absent") . ($md5pass ? "md5mdp fourni" : "md5mdp absent"),"thelia");
		if (!file_exists(_RACINE_THELIA . "fonctions/moteur.php")){
			spip_log("fichier thelia trouve","thelia");
			return array();
		}
		spip_log("thelia1","thelia"); //récupérer le nom de la base spip
		if ($result = mysql_query("SELECT DATABASE()")){
			$row = mysql_fetch_row($result);
			$spip_db = $row[0];
		}
		spip_log("test1, db courante=" . $spip_db,"thelia");
		$res = " foo ";
		ob_start();
		include_once(_DIR_RACINE . _RACINE_THELIA . 'fonctions/moteur.php');
		ob_end_clean();
		//include_once(_DIR_RACINE . _RACINE_THELIA . 'classes/Client.class.php');
		spip_log("test2","thelia");


		// Securite
		if (!$login || (!$pass && !$md5pass)) return array();


		// Utilisateur connu ?
		$client = New Client();
		$rec = $client->charger($login, $pass);

		//revenir sur la base spip
		$resultconnect = mysql_select_db($spip_db);
		spip_log("spipdb=" . $spip_db . " - " . $resultconnect,"thelia");
		if ($rec){
			spip_log("thelia $login utilisateur connu","thelia");
			$_SESSION['navig']->client = $client;
			$_SESSION['navig']->connecte = 1;
			modules_fonction("apresconnexion");

			// importer les infos depuis thelia,
			// avec le statut par defaut a l'install
			// refuser d'importer n'importe qui
			if (!$statut = $GLOBALS['thelia_statut_nouvel_auteur']) return array();

			if ($result = sql_fetsel("*", "spip_auteurs", "login=" . sql_quote($login) . " AND source='thelia'")){
				$data = pipeline('thelia_authentifie', array("auteur" => $result, "statut" => "existant"));
				return $data['auteur'];
			}

			spip_log("thelia2","thelia");
			// Recuperer les donnees de l'auteur
			// Convertir depuis UTF-8 (jeu de caracteres par defaut)
			include_spip('inc/charsets');
			$nom = $client->nom . ' ' . $client->prenom;
			$email = $login;
			$bio = '';
			$resultconnect = mysql_select_db($spip_db);
			spip_log("thelia2bis-connect=" . $resultconnect,"thelia");
			$n = sql_insertq('spip_auteurs', array(
				'source' => 'thelia',
				'nom' => $nom,
				'login' => $login,
				'email' => $email,
				'bio' => $bio,
				'statut' => $statut,
				'pass' => ''), 0);
			spip_log("thelia3","thelia");
			spip_log("Creation de l'auteur '$nom' depuis thelia dans spip_auteurs id->" . $n,"thelia");

			spip_log("thelia4","thelia");

			if ($_SESSION['navig']->urlpageret){
				spip_log("redirige vers " . $_SESSION['navig']->urlpageret,"thelia");
				redirige($_SESSION['navig']->urlpageret);
			} else {
				spip_log("redirige vers index.php","thelia");
				redirige("index.php");
			}
			spip_log("test6","thelia");
			if ($n){
				$auteur = sql_fetsel("*", "spip_auteurs", "id_auteur=$n");
				$data = pipeline('thelia_authentifie', array("auteur" => $auteur, "statut" => "nouveau"));
				return $data['auteur'];
			}

			spip_log("Creation de l'auteur '$nom' depuis thelia impossible","thelia");


			return array();

		} else {
			//utilisateur inconnu
			spip_log("thelia $login utilisateur inconnu","thelia");
			redirige("connexion.php?errconnex=1");
			return array();
		}
	}
	spip_log("thelia0","thelia");

	return array();

}

/*
	$auteur = array(login,pass,client)
*/
function creer_auteur_thelia($auteur){

	if (empty($auteur))
		return array();

	//Empecher un doublon
	if (isset($auteur['login'])
	  AND $login = $auteur['login']
	  AND  $result = sql_fetsel("*", "spip_auteurs", "login=" . sql_quote($login) . " AND source='thelia'")){
		spip_log("l'utilisateur $login est déjà enregistré dans spip tout va bien", 'thelia');
		return $result;
	}

	//charger le support thelia	
	ob_start();
	include_once(_DIR_RACINE . _RACINE_THELIA . 'fonctions/moteur.php');
	ob_end_clean();

	//Empecher un doublon
	if (isset($auteur['client'])
	  AND $login = $auteur['client']->email
	  AND $result = sql_fetsel("*", "spip_auteurs", "login=" . sql_quote($login) . " AND source='thelia'")){
		spip_log("l'utilisateur $login est déjà enregistré dans spip tout va bien", 'thelia');
		return $result;
	}

	//Tester la présence dans thelia
	$client = $auteur['client'];
	if (!$auteur['client']){
		$client = New Client();
		if (!$client->charger($auteur['login'], $auteur['pass']))
			return array();
	}

	//Valeur par défaut
	if (!$statut = $GLOBALS['thelia_statut_nouvel_auteur']){
		spip_log('erreur pas de statut defini par defaut', 'thelia');
		return array();
	}

	spip_log("enregistrement auteur", 'thelia');
	// Recuperer les donnees de l'auteur
	// Convertir depuis UTF-8 (jeu de caracteres par defaut)
	include_spip('inc/charsets');
	$nom = $client->nom . ' ' . $client->prenom;
	$login = $email = $client->email;
	$bio = '';
	$n = sql_insertq(
		'spip_auteurs',
		array(
			'source' => 'thelia',
			'nom' => $nom,
			'login' => $login,
			'email' => $email,
			'bio' => $bio,
			'statut' => $statut,
			'pass' => '')
	);
	spip_log('Auteur depuis thelia d\'id ' . $n, 'thelia');

	return sql_fetsel("*", "spip_auteurs", "id_auteur=$n");
}

