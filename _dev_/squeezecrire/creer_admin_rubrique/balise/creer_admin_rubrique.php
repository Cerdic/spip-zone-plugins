<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/abstract_sql');


function balise_creer_admin_rubrique ($p) {
	return calculer_balise_dynamique($p,'creer_admin_rubrique', array());
}

function balise_creer_admin_rubrique_stat($args, $filtres) {
	return $args;
}

function balise_creer_admin_rubrique_dyn() {
	
	//recuperation des champs
	$nom= stripslashes(_request('nom'));
	$prenom= stripslashes(_request('prenom'));
	$nom_auteur= $nom." ".$prenom;
	
	$email= stripslashes(_request('email'));
	$login= _request('login');
	$pass= md5(_request('pass'));
	
	// recuperation des données login de cfg
	$login_cfg = lire_config('creer_admin_rubrique/login_cfg');
	if ($login_cfg == 1){
		$login= $nom_auteur;
	}

	// recuperation des données pass de cfg
	$pass_cfg = lire_config('creer_admin_rubrique/pass_cfg');
	if ($pass_cfg == 1){
		$pass= md5(lire_config('creer_admin_rubrique/pass'));
	}

	
	// secteur dans lequel on va créer la rubrique et statut de l'article
	$secteur= lire_config('creer_admin_rubrique/secteur');
	$rubrique_parent= lire_config('creer_admin_rubrique/secteur');
	$statut_article= lire_config('creer_admin_rubrique/statut_article');

	
	$statut= "0minirezo";
	$date= date("Y-m-j H:i:s");
		
	$valider= _request('valider');
	
	
	if ($GLOBALS["auteur_session"]) {
		$id_auteur_session = $GLOBALS['auteur_session']['id_auteur'];
		$id_auteur_statut = $GLOBALS['auteur_session']['statut'];
	}

	if (!$id_auteur_session || $id_auteur_statut != "0minirezo") {
		return;
	}

	if($valider){
	
		if ($erreur){
			return array('formulaires/creer_admin_rubrique', 0,
				array(
					'erreur' => $erreur,
					'nom' => $nom,
					'prenom' => $prenom,
					'email' => $email,
					'login' => $login
				));
		}

		//// 1- Création de l'auteur

		// on regarde si ce login est dispo
		$sql = spip_query("SELECT COUNT(*) AS nb_user FROM spip_auteurs WHERE LOWER(login) = '$login' LIMIT 1");
		$result = spip_fetch_array($sql);
		// le login est dispo
		if ($result['nb_user'] < 1) {
			// on ajoute l'auteur à la base
			spip_query("INSERT INTO spip_auteurs (id_auteur, nom, email, login, pass, statut) VALUES ('',"._q($nom_auteur).", '$email', "._q($login).", "._q($pass).", '$statut')" );
			$id_auteur = mysql_insert_id();
			spip_log("[plugin creer_admin_rubrique] OK ajoute auteur id : $id_auteur");
		}
		else {
			$erreur = "Ce login existe deja";
			spip_log("[plugin creer_admin_rubrique] ERREUR login existe deja : $login");
		}

		//// 2- Création de la rubrique

		$sql = spip_query("SELECT COUNT(*) AS rub_existe FROM spip_rubriques WHERE titre = "._q($nom_auteur)." LIMIT 1");
		$result = spip_fetch_array($sql);
		if ($result['rub_existe'] > 0) {
			$erreur = "Une rubrique portant ce nom existe deja";
			spip_log("[plugin creer_admin_rubrique] ERREUR rubrique existe deja id : $id_rubrique");
		}
		else {
			spip_query("INSERT INTO spip_rubriques (id_rubrique, id_parent, titre, id_secteur, statut, date) VALUES ('', '$rubrique_parent', "._q($nom_auteur).", '$secteur', 'publie', '$date')" );
			$id_rubrique = mysql_insert_id();
			spip_log("[plugin creer_admin_rubrique] OK ajoute rubrique id : $id_rubrique");
		}

		//// 3- On attache l'auteur à la rubrique et on crée un article dans la rubreique de l'auteur

		if ($id_auteur && $id_rubrique){
		
			spip_query("INSERT INTO spip_auteurs_rubriques (id_auteur, id_rubrique) VALUES ('$id_auteur', '$id_rubrique')");
			spip_log("[plugin creer_admin_rubrique] OK lier auteur - rubrique : $id_auteur - $id_rubrique");
			
			$retour= "Informations enregistr&eacute;es";
			
			$sql = spip_query("SELECT id_article FROM spip_articles WHERE id_rubrique = '$id_rubrique' AND titre = '$nom_auteur' LIMIT 1");
			if (spip_num_rows($sql) < 1) {
				spip_query("INSERT INTO spip_articles (id_article, id_rubrique, id_secteur, titre, date, statut ) VALUES ('', '$id_rubrique', '$secteur', "._q($nom_auteur).", '$date', '$statut_article')");
				$id_article = mysql_insert_id();
				spip_query("INSERT INTO spip_auteurs_articles (id_auteur, id_article) VALUES ('$id_auteur', '$id_article')");
				spip_log("[plugin creer_admin_rubrique] OK ajoute article : $id_article");
			}
			else {
				$erreur = "Erreur lors de la creation de l'article";
				spip_log("[plugin creer_admin_rubrique] ERREUR article existe deja titre : $nom_auteur");
			}
		}
		
	}
	
	return array(
		'formulaires/creer_admin_rubrique', 
		0, 
		array(
			'erreur' => $erreur,
			'retour' => $retour
			
		)
    );
	
}
?>
