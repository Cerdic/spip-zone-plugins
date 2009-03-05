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
	 * action_abonnements
	 *
	 * @author  Pierre Basson
	 */
	function action_abonnements() {
		global $email, $nom, $format, $rubriques, $lang;

		// champs extra
		if ($champs_extra = $GLOBALS['champs_extra']['abonnes']) {
			$extras_obligatoires = array();
			$extra_erreurs = array();
			$extra = array();
			// champs obligatoires
			foreach ($champs_extra as $cle => $valeur) {
				if (ereg('!$', $cle))
					$extras_obligatoires[] = $cle;
			}
			foreach ($extras_obligatoires as $valeur) {
				$post = _request($valeur);
				if (empty($post))
					$extra_erreurs[] = $valeur;
			}
			$extras_ok = empty($extra_erreurs);
		} else {
			$extras_ok = true;
		}

		if (lettres_verifier_validite_email($email) and $extras_ok and isset($rubriques)) {
			$abonne = new abonne(0, $email);
			$abonne->nom = $nom;
			$abonne->format = $format;
			$abonne->enregistrer();
			foreach ($rubriques as $id_rubrique)
				$abonne->enregistrer_abonnement($id_rubrique);
			$themes = implode(',', $rubriques);
			$resultat = $abonne->envoyer_notification('abonnements', 
														array(
															'lang'		=> $lang,
															'rubriques'	=> $rubriques,
															'themes'	=> $themes
															)
														);
			$redirection = generer_url_public($GLOBALS['meta']['spip_lettres_fond_formulaire_lettres'], "lang=$lang&message=envoi_abonnements_".($resultat ? 'succes' : 'erreur')."&themes=$themes", true);
		} else {
			$redirection = generer_url_public($GLOBALS['meta']['spip_lettres_fond_formulaire_lettres'], "lang=$lang&email=$email&erreur=1", true);
		}

		header('Location: ' . $redirection);
		exit();

	}

?>