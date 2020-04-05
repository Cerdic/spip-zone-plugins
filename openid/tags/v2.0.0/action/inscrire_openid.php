<?php
/**
 * Plugin OpenID
 * Licence GPL (c) 2007-2009 Edouard Lafargue, Mathieu Marcillaud, Cedric Morin, Fil
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// Cette fonction est appelee lors du retour de l'authentification OpenID
// Elle doit verifier si l'authent est OK, puis chercher l'utilisateur
// associé dans spip (champ openid dans la base), et finalement l'authentifier
// en creant le bon cookie.

function action_inscrire_openid_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!$idurl = $arg) {
		spip_log("action_inscrire_openid_dist $arg pas compris");
	}
	else {
		include_spip('inc/openid');
		$redirect = _request('redirect');

		$retour = openid_url_retour_insc($idurl);
		$auteur = terminer_authentification_openid($retour);

		// si l'auth a retourne une erreur, retourner sur la page initiale avec une erreur
		if (is_string($auteur)){
			$redirect = parametre_url($redirect,'url_openid',$idurl); // erreur !
			$redirect = parametre_url($redirect,'var_erreur',$auteur); // erreur !
		}
		elseif (is_array($auteur)
			AND isset($auteur['openid'])){
/*			include_spip('balise/formulaire_');
			$balise = balise_FORMULAIRE__dyn('inscription','6forum',true);
			$balise[2]['editable'] = ' ';
			$balise[2]['action'] = $redirect;
			$balise[2]['openid'] = $auteur['openid'];
			$balise[2]['nom_inscription'] = $auteur['login'];
			$balise[2]['email_inscription'] = $auteur['email'];

			include_spip('public/assembler');
			$form = inclure_balise_dynamique($balise,false);

			echo "<html><head><title>",
			 "OpenID transaction in progress",
			 "</title></head>",
			 "<body>",// onload='document.getElementById(\"".$form_id."\").submit()'>",
			 $form,
			 "</body></html>";

			exit;*/

			$redirect = parametre_url($redirect,'openid',$auteur['openid']);
			$redirect = parametre_url($redirect,'nom_inscription',$auteur['login']);
			$redirect = parametre_url($redirect,'mail_inscription',$auteur['email']);
			
			$redirect = pipeline('openid_inscrire_redirect',array(
				'args'=> array('url' => $redirect,'infos_auteur' =>$auteur),
				'data'=> $redirect
			));

		}

		set_request('redirect',$redirect);
	}
}

?>