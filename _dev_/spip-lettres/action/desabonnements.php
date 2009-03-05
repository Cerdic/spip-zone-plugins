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
	 * action_desabonnements
	 *
	 * @author  Pierre Basson
	 */
	function action_desabonnements() {
		global $email, $rubriques, $lang;

		if (lettres_verifier_validite_email($email)) {
			$abonne = new abonne(0, $email);
			if ($abonne->existe and isset($rubriques)) {
				$themes = implode(',', $rubriques);
				$resultat = $abonne->envoyer_notification('desabonnements', 
															array(
																'lang'		=> $lang,
																'rubriques'	=> $rubriques,
																'themes'	=> $themes
																)
															);
				$redirection = generer_url_public($GLOBALS['meta']['spip_lettres_fond_formulaire_lettres'], "lang=$lang&message=envoi_desabonnements_".($resultat ? 'succes' : 'erreur')."&themes=$themes", true);
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