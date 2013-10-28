<?php
/**
 * Plugin Inscription3 pour SPIP
 * © 2007-2013 - cmtmt, BoOz, kent1
 * Licence GPL v3
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

//email envoye lors de l'inscription
function inc_envoyer_inscription3_dist($id_auteur,$mode) {
	if(!function_exists('lire_config'))
		include_spip('inc/config');
	$config_i3 = lire_config('inscription3',array());

	include_spip('inc/mail');

	if(!$mode)
		$mode = 'inscription';

	// On récupère les données nécessaires à envoyer le mail de validation
	// La fonction envoyer_mail se chargera de nettoyer cela plus tard
	$nom_site_spip = $GLOBALS['meta']["nom_site"];
	$adresse_site = $GLOBALS['meta']["adresse_site"];

	$prenom = ($config_i3['prenom'] == 'on') ? "prenom," : "";
	$nom = ($config_i3['nom_famille'] == 'on') ? "nom_famille," : "";

	$var_user = sql_fetsel(
		"nom,$prenom $nom id_auteur, alea_actuel, login, email",
		"spip_auteurs",
		"id_auteur = ".intval($id_auteur)
	);

	// On défini le nom qui sera utilisé dans le mail

	if($var_user['nom'])
		$nom_final = $var_user['nom'];
	else
		$nom_final = $var_user['email'];

	// Dans le cas ou on ne demande pas de mot de passe dans le formulaire de création de compte
	if($var_user['alea_actuel']==''){
 		include_spip('inc/acces'); # pour creer_uniqid
		$cookie = creer_uniqid();
		sql_updateq("spip_auteurs", array("cookie_oubli" => $cookie), "id_auteur=" . $id_auteur);
	}
 	if($mode=="inscription"){
 		// nettoyer le mode sup
		$message = _T('inscription3:message_auto')."\n\n"
				. _T('inscription3:email_bonjour', array('nom'=> $nom_final))."\n\n"
				. _T('inscription3:texte_email_inscription', array(
						'nom_site' => $nom_site_spip, 'url_site' => $adresse_site,
						'link_activation' => generer_url_public('spip_pass','p='.$cookie, true),
						'link_suppresion' => generer_url_public('spip_pass','s='.$cookie, true),
					));
		$sujet = "[$nom_site_spip] "._T('inscription3:activation_compte');
	}else if($mode=="inscription_pass"){
		// a nettoyer
		$message = _T('inscription3:message_auto')."\n\n"
				. _T('inscription3:email_bonjour', array('nom'=> $nom_final))."\n\n"
				. _T('inscription3:texte_email_confirmation', array('login' => $var_user['login'], 'nom_site' => $nom_site_spip, 'url_site' => $adresse_site));
		$sujet = "[$nom_site_spip] "._T('inscription3:compte_active',array('nom_site'=>$nom_site_spip));
	}

	if (envoyer_mail($var_user['email'],
			$sujet,
			$message))
		return;
	else
		return _T('inscription3:probleme_email');
}
?>