<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_ajout_inscription_charger_dist() {
	$valeurs = array('nom_adherent_inscription'=>'', 'prenom_inscription'=>'',
'adresse_postale_inscription'=>'', 'cp_inscription'=>'1070',
'localite_inscription'=>'Anderlecht', 'no_adherent_inscription'=>'',
'statut_adherent_inscription'=>'S', 'cotisation_inscription'=>'',
'sexe_inscription'=>'', 'tel_inscription'=>'', 'gsm_inscription'=>'', 'email_inscription'=>'');

	return $valeurs;
}


function formulaires_ajout_inscription_verifier_dist() {

	$erreurs = array();
	include_spip('inc/filtres');	

	if (!$nom = _request('nom_adherent_inscription'))
		$erreurs['nom_adherent_inscription'] = _T("info_obligatoire");
	
	return $erreurs;

}


function formulaires_ajout_inscription_traiter_dist() {

	$res = array('message_erreur'=>_T('titre_probleme_technique'));

	$nom_adherent = _request('nom_adherent_inscription');
	$prenom = _request('prenom_inscription');
	$adresse_postale = _request('adresse_postale_inscription');
	$code_postal = _request('cp_inscription');
	$localite = _request('localite_inscription');
	$no_fichier = _request ('no_adherent_inscription');
	$statut_adherent = _request ('statut_adherent_inscription');
	$cotisation = _request ('cotisation_inscription');
	$sexe = _request ('sexe_inscription');
	$tel = _request ('tel_inscription');
	$gsm = _request ('gsm_inscription');
        $email = _request ('email_inscription');
        $pseudo = $prenom .' '. $nom_adherent;
	$login = test_login($pseudo, $email);

if ($statut_adherent =="M") $statut="1comite"; else $statut="6forum";


	include_spip('base/abstract_sql');

	if ($id_auteur = sql_insertq('spip_auteurs', array(
'nom' => $pseudo,
'statut' => $statut,
'email' => $email,
'login' => $login 
))) {		$res = array('message_ok' => "auteur OK", 'id_auteur'=>$id_auteur);
		$pass = creer_pass_pour_auteur($id_auteur);

		if ($id_fichier = sql_insertq('spip_adherents', array(

'id_auteur' => sql_getfetsel('id_auteur','spip_auteurs','nom='.sql_quote($pseudo)),
'nom_adherent' => $nom_adherent,
'prenom' => $prenom,
'adresse_postale' => $adresse_postale,
'code_postal' => $code_postal,
'localite' => $localite,
'no_adherent' => $no_fichier,
'statut_adherent' => $statut_adherent,
'cotisation' => $cotisation,
'sexe' => $sexe, 
'telephone' => $tel,
'gsm' => $gsm
)))
		$res = array('message_ok' => "Enregistrement adherent OK", 'id_fichier'=>$id_fichier);}

	return $res;
}

// https://code.spip.net/@test_login
function test_login($nom, $mail) {
	include_spip('inc/charsets');
	$nom = strtolower(translitteration($nom));
	$login_base = preg_replace("/[^\w\d_]/", "_", $nom);

	// il faut eviter que le login soit vraiment trop court
	if (strlen($login_base) < 3) {
		$mail = strtolower(translitteration(preg_replace('/@.*/', '', $mail)));
		$login_base = preg_replace("/[^\w\d]/", "_", $nom);
	}
	if (strlen($login_base) < 3)
		$login_base = 'user';

	// eviter aussi qu'il soit trop long (essayer d'attraper le prenom)
	if (strlen($login_base) > 10) {
		$login_base = preg_replace("/^(.{4,}(_.{1,7})?)_.*/",
			'\1', $login_base);
		$login_base = substr($login_base, 0,13);
	}

	$login = $login_base;

	for ($i = 1; ; $i++) {
		if (!sql_countsel('spip_auteurs', "login='$login'"))
			return $login;
		$login = $login_base.$i;
	}
}

// https://code.spip.net/@creer_pass_pour_auteur
function creer_pass_pour_auteur($id_auteur) {
	include_spip('inc/acces');
	include_spip('auth/sha256.inc');
	$pass = creer_pass_aleatoire(8, $id_auteur);
	$mdpass = sha256($pass);
	$htpass = generer_htpass($pass);
	sql_updateq('spip_auteurs', array('pass'=>$mdpass, 'htpass'=>$htpass),"id_auteur = ".intval($id_auteur));
	ecrire_acces();
	
	return $pass;
}


?>
