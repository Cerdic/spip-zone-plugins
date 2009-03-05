<?php


	/**
	 * SPIP-Lettres
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	include_spip('lettres_fonctions');


	/**
	 * action_changement_format
	 *
	 * @author  Pierre Basson
	 */
	function action_changement_format() {
		global $email, $format, $lang;

		if (lettres_verifier_validite_email($email)) {
			$abonne = new abonne(0, $email);
			if ($abonne->existe) {
				$resultat = $abonne->envoyer_notification('changement_format', 
															array(
																'lang'		=> $lang,
																'format'	=> $format
																)
															);
				$redirection = generer_url_public($GLOBALS['meta']['spip_lettres_fond_formulaire_lettres'], "lang=$lang&message=envoi_changement_format_".($resultat ? 'succes' : 'erreur'), true);
			} else {
				$redirection = generer_url_public($GLOBALS['meta']['spip_lettres_fond_formulaire_lettres'], "lang=$lang", true);
			}
		} else {
			$redirection = generer_url_public($GLOBALS['meta']['spip_lettres_fond_formulaire_lettres'], "lang=$lang&email=$email&erreur=1", true);
		}

		header('Location: ' . $redirection);
		exit();

	}

?>