<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip ("inc/texte");
include_spip ("inc/session");
include_spip ("inc/extra");

// Le contexte indique dans quelle rubrique le visiteur peut proposer l article
function balise_FORMULAIRE_PROFILE ($p) {
	return calculer_balise_dynamique($p,'FORMULAIRE_PROFILE', array('id_article'));
}

function balise_FORMULAIRE_PROFILE_stat($args, $filtres) {

	// Pas d'id_auteur ? Erreur de squelette
	if (!$args[0])
		return erreur_squelette(
			_T('zbug_champ_hors_motif',
				array ('champ' => '#FORMULAIRE_PROFILE',
					'motif' => 'AUTEURS')), '');
	return ($args);
}

function balise_FORMULAIRE_PROFILE_dyn($bio=' ', $url_site=' ', $nom_site=' ',  $pgp=' ', $nom =' ', $email=' ', $new_pass=' ', $new_pass2=' ', $bio=' ') {
	global $REMOTE_ADDR, $afficher_texte, $_COOKIE, $_POST;

	$auteur_statut=$GLOBALS["auteur_session"]["statut"];
	$statut=$auteur_session['statut'];
	$id_auteur_session=$auteur_session['id_auteur'];
	$auteur=unserialize(get_auteur_infos($id_auteur_session));

	$id_auteur = _request('id_auteur');
	$nom = _request('nom');
	$email = _request('email');
	$telephone = _request('telephone');
	$fax = _request('fax');
	$skype = _request('skype');

	$statut = _request('statut');
	$login = _request('login');

	$pgp = _request('pgp');
	$bio = _request('bio');

	$url_site = _request('url_site');
	$nom_site = _request('nom_site');

	$adresse = _request('adresse');
	$codepostal = _request('codepostal');
	$ville = _request('ville');
	$pays = _request('pays');

	$latitude = _request('latitude');
	$longitude = _request('longitude');

	$new_pass =  _request('new_pass');
	$new_pass2 =  _request('new_pass2');

	$previsualiser= _request('previsualiser');
	$valider= _request('valider');
	
	$previsu = '';
	$bouton= '';
	$erreur= '';

	$statut=$auteur_session['statut'];
	$id_auteur_session=$auteur_session['id_auteur'];
	$auteur=unserialize(get_auteur_infos($id_auteur_session));

	if($valider)
		{
		// integrer a la base de donnee
	$nom2= _request('nom2');
	$bio2= _request('bio2');
	$email2= _request('email2');
	$telephone2 = _request('telephone2');
	$fax2 = _request('fax2');
	$skype2 = _request('skype2');
	$adresse2 = _request('adresse2');
	$codepostal2 = _request('codepostal2');
	$ville2 = _request('ville2');
	$pays2 = _request('pays2');
	$latitude2 = _request('latitude2');
	$longitude2 = _request('longitude2');
	$nom_site2= _request('nom_site2');
	$url_site2= _request('url_site2');
	$query_pass2 = _request('pass');


		// Modifier l'auteur dans la base
		$query = "UPDATE spip_auteurs SET $query_pass2
			nom='".addslashes($nom2)."',
			bio='".addslashes($bio2)."',
			email='".addslashes($email2)."',
			telephone='".addslashes($telephone2)."',
			fax='".addslashes($fax2)."',
			skype='".addslashes($skype2)."',
			adresse='".addslashes($adresse2)."',
			codepostal='".addslashes($codepostal2)."',
			ville='".addslashes($ville2)."',
			pays='".addslashes($pays2)."',
			latitude='".addslashes($latitude2)."',
			longitude='".addslashes($longitude2)."',
			nom_site='".addslashes($nom_site2)."',
			url_site='".addslashes($url_site2)."'
			$add_extra
			WHERE id_auteur=".$id_auteur;

		spip_query($query) OR die($query);

	return array('formulaire_profile', 0,
		array(
				'previsu' => $previsu,
				'id_auteur' => $id_auteur,
				'nom' => $nom,
				'email' => $email,
				'telephone' => $telephone,
				'fax' => $fax,
				'skype' => $skype,
				'adresse' => $adresse,
				'codepostal' => $codepostal,
				'ville' => $ville,
				'pays' => $pays,
				'latitude' => $latitude,
				'longitude' => $longitude,
				'url_site' => $url_site,
				'nom_site' => $nom_site,
				'pgp' => $pgp,
				'bio' => $bio
			));	
		}

	else{
		if($previsualiser)
		{

		if ($new_pass) {
			if ($new_pass != $new_pass2) {
				$erreur .= _T('info_passes_identiques');
				$bouton = '';
			}
			else if ($new_pass AND strlen($new_pass) < 6){
				$erreur .= _T('info_passe_trop_court');
				$bouton = '';
			}
			else {
				$modif_login = true;
				$auteur['new_pass'] = $new_pass;
				$bouton= _T('form_prop_confirmer_envoi');
			}
		}
		
// 		if ($modif_login) {
// 			zap_sessions ($auteur['id_auteur'], true);
// 			if ($id_auteur_session == $auteur['id_auteur'])
// 			supprimer_session($GLOBALS['spip_session']);
// 		}
	
		if ($new_pass) {
			$htpass = generer_htpass($new_pass);
			$alea_actuel = creer_uniqid();
			$alea_futur = creer_uniqid();
			$pass = md5($alea_actuel.$new_pass);
			$query_pass = "pass='$pass', htpass='$htpass', alea_actuel='$alea_actuel', alea_futur='$alea_futur', ";
			effacer_low_sec($auteur['id_auteur']);
		}

		else {
			$query_pass = '';
			$bouton= _T('form_prop_confirmer_envoi');
		}

		$previsu = inclure_balise_dynamique(
			array(
				'formulaire_profile_previsu', 0,
			array(
				'previsu' => $previsu,
				'id_auteur' => $id_auteur,
				'nom' => $nom,
				'email' => $email,
				'telephone' => $telephone,
				'fax' => $fax,
				'skype' => $skype,
				'url_site' => $url_site,
				'nom_site' => $nom_site,
				'adresse' => $adresse,
				'codepostal' => $codepostal,
				'ville' => $ville,
				'pays' => $pays,
				'latitude' => $latitude,
				'longitude' => $longitude,
				'pgp' => $pgp,
				'bio' => $bio,
				'bouton' => $bouton,
				'query_pass' => $query_pass,
				'erreur' => $erreur
				)
			), false);
		}

		return array('formulaire_profile', 0,
		array(
				'previsu' => $previsu,
				'id_auteur' => $id_auteur,
				'nom' => $nom,
				'email' => $email,
				'telephone' => $telephone,
				'fax' => $fax,
				'skype' => $skype,
				'url_site' => $url_site,
				'nom_site' => $nom_site,
				'adresse' => $adresse,
				'codepostal' => $codepostal,
				'ville' => $ville,
				'pays' => $pays,
				'latitude' => $latitude,
				'longitude' => $longitude,
				'nom' => $nom,
				'email' => $email,
				'url_site' => $url_site,
				'pgp' => $pgp,
				'bio' => $bio
			));
	}
}


?>