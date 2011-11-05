<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// Cette fonction est appelee lors du retour de l'authentification chez Facebook.
// Elle doit verifier si la connexion est OK, puis chercher l'utilisateur
function action_inscrire_fblogin_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	include_spip('inc/fblogin');
	$redirect = _request('redirect');

	$retour = fblogin_url_retour_inscription();
	$auteur = fblogin_terminer_authentification();
	
	// Si l'auth a retourné une erreur, retourner sur la page initiale avec une erreur
	if (is_string($auteur)){
		$redirect = parametre_url($redirect,'var_erreur', $auteur); // erreur !
	}
	elseif (is_array($auteur)
		and isset($auteur['fb_uid'])
	){
		$redirect = parametre_url($redirect, 'fb_uid', $auteur['fb_uid']);
		$redirect = parametre_url($redirect, 'nom_inscription', $auteur['login']);
		$redirect = parametre_url($redirect, 'mail_inscription', $auteur['email']);
		
		// Un pipeline pour ajouter des infos si on en veut plus
		$redirect = pipeline(
			'fblogin_inscrire_redirect',
			array(
				'args'=> array('url' => $redirect, 'infos_auteur' =>$auteur),
				'data'=> $redirect
			)
		);

		//var_dump($redirect);exit;
	}

	set_request('redirect', $redirect);
}

?>
