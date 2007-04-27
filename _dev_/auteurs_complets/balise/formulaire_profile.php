<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip ("inc/texte");
include_spip ("inc/session");
include_spip ("inc/extra");

function balise_FORMULAIRE_PROFILE ($p) {
	return calculer_balise_dynamique($p,'FORMULAIRE_PROFILE', array('id_auteur'));
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

	$nom_table="spip_auteurs";

	$auteur_statut=$GLOBALS["auteur_session"]["statut"];
	$statut=$auteur_session['statut'];
	$id_auteur_session=$auteur_session['id_auteur'];
	$auteur=unserialize(get_auteur_infos($id_auteur_session));

	$id_auteur = _request('id_auteur');
	$nom_famille = _request('nom_famille');
	$prenom = _request('prenom');
	$email = _request('email');
	$telephone = _request('telephone');
	$fax = _request('fax');
	$skype = _request('skype');
	$organisation = _request('organisation');
	$url_organisation = _request('url_organisation');

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

	$previsualiser_auteur= _request('previsualiser_auteur');
	$valider_auteur= _request('valider_auteur');
	
	$previsu = '';
	$bouton= '';
	$erreur= '';

	$statut=$auteur_session['statut'];
	$id_auteur_session=$auteur_session['id_auteur'];
	$auteur=unserialize(get_auteur_infos($id_auteur_session));

	if($valider_auteur){

		// Modifier l'auteur dans la base
		$query = "UPDATE ".$nom_table." SET $query_pass2
			nom='".addslashes($prenom)." ".addslashes($nom_famille)."',
			bio='".addslashes($bio)."',
			email='".addslashes($email)."',
			nom_famille='".addslashes($nom_famille)."',
			prenom='".addslashes($prenom)."',
			organisation='".addslashes($organisation)."',
			url_organisation='".addslashes($url_organisation)."',
			telephone='".addslashes($telephone)."',
			fax='".addslashes($fax)."',
			skype='".addslashes($skype)."',
			adresse='".addslashes($adresse)."',
			codepostal='".addslashes($codepostal)."',
			ville='".addslashes($ville)."',
			pays='".addslashes($pays)."',
			latitude='".addslashes($latitude)."',
			longitude='".addslashes($longitude)."',
			nom_site='".addslashes($nom_site)."',
			url_site='".addslashes($url_site)."'
			$add_extra
			WHERE id_auteur=".$id_auteur;

		spip_query($query) OR die($query);

		return header("Location: ".$_SERVER['HTTP_REFERER']) ;
		}

	else{
		if($previsualiser_auteur)
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
				$bouton= _T('form_prop_confirmer');
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
			$bouton= _T('form_prop_confirmer');
		}

		$previsu = inclure_balise_dynamique(
			array(
				'formulaire_profile_previsu', 0,
			array(
				'previsu' => $previsu,
				'id_auteur' => $id_auteur,
				'nom_famille' => $nom_famille,
				'prenom' => $prenom,
				'email' => $email,
				'organisation' => $organisation,
				'url_organisation' => $url_organisation,
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
				'id_auteur' => $id_auteur,
				'nom_famille' => $nom_famille,
				'prenom' => $prenom,
				'email' => $email,
				'organisation' => $organisation,
				'url_organisation' => $url_organisation,
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
			)
		);
	}
}
?>
