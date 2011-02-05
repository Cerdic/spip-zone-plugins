<?php
/**
 * Plugin fblogin
 * Licence GPL (c) 2007-2009 Edouard Lafargue, Mathieu Marcillaud, Cedric Morin, Fil
 *
 */


// Cette fonction est appelee lors du retour de l'authentification fblogin
// Elle doit verifier si l'authent est OK, puis chercher l'utilisateur
// associé dans spip (champ fblogin dans la base), et finalement l'authentifier
// en creant le bon cookie.

function action_inscrire_fblogin_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!$idurl = $arg) {
		spip_log("action_inscrire_fblogin_dist $arg pas compris");
	}
	else {
		include_spip('inc/fblogin');
		$redirect = _request('redirect');

		$retour = fblogin_url_retour_insc($idurl);
		$auteur = terminer_authentification_fblogin($retour);

		// si l'auth a retourne une erreur, retourner sur la page initiale avec une erreur
		if (is_string($auteur)){
			$redirect = parametre_url($redirect,'url_fblogin',$idurl); // erreur !
			$redirect = parametre_url($redirect,'var_erreur',$auteur); // erreur !
		}
		elseif (is_array($auteur)
			AND isset($auteur['fb_uid'])){
/*			include_spip('balise/formulaire_');
			$balise = balise_FORMULAIRE__dyn('inscription','6forum',true);
			$balise[2]['editable'] = ' ';
			$balise[2]['action'] = $redirect;
			$balise[2]['fblogin'] = $auteur['fblogin'];
			$balise[2]['nom_inscription'] = $auteur['login'];
			$balise[2]['email_inscription'] = $auteur['email'];

			include_spip('public/assembler');
			$form = inclure_balise_dynamique($balise,false);

			echo "<html><head><title>",
			 "fblogin transaction in progress",
			 "</title></head>",
			 "<body>",// onload='document.getElementById(\"".$form_id."\").submit()'>",
			 $form,
			 "</body></html>";

			exit;*/

			$redirect = parametre_url($redirect,'fb_uid',$auteur['fb_uid']);
			$redirect = parametre_url($redirect,'nom_inscription',$auteur['login']);
			$redirect = parametre_url($redirect,'mail_inscription',$auteur['email']);
			
			$redirect = pipeline('fblogin_inscrire_redirect',array(
				'args'=> array('url' => $redirect,'infos_auteur' =>$auteur),
				'data'=> $redirect
			));

		}

		set_request('redirect',$redirect);
	}
}

?>