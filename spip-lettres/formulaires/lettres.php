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


	function formulaires_lettres_charger_dist() {
		$message_ok = '';
		$message_erreur = '';
		switch(_request('message')) {
			case 'validation_abonnements_succes':
				$message_ok = _T('lettres:validation_abonnements_succes');
				break;
			case 'validation_abonnements_erreur':
				$message_erreur = _T('lettres:validation_abonnements_erreur');
				break;
			case 'validation_desabonnements_succes':
				$message_ok = _T('lettres:validation_desabonnements_succes');
				break;
			case 'validation_desabonnements_erreur':
				$message_erreur = _T('lettres:validation_desabonnements_erreur');
				break;
			case 'validation_changement_format_succes':
				$message_ok = _T('lettres:validation_changement_format_succes');
				break;
			case 'validation_changement_format_erreur':
				$message_erreur = _T('lettres:validation_changement_format_erreur');
				break;
		}
		$valeurs = array(
						'message_ok'		=> $message_ok,
						'message_erreur'	=> $message_erreur,
						'email'				=> '',
						'nom'				=> '',
						'rubriques'			=> '',
						'format'			=> 'mixte',
						'choix'				=> 'abonnements'
						);
		return $valeurs;
	}


	function formulaires_lettres_verifier_dist() {
		$email		= _request('email');
		$nom		= _request('nom');
		$rubriques	= _request('rubriques');
		$format		= _request('format');
		$choix		= _request('choix');

		$erreurs = array();

		if (!lettres_verifier_validite_email($email))
			$erreurs['email'] = _T('lettres:email_ko');

		if (!$choix) {
			$erreurs['choix'] = _T('lettres:choix_ko');
		} else {
			switch($choix) {
				case 'abonnements':
					if (empty($rubriques))
						$erreurs['rubriques'] = _T('lettres:vous_devez_choisir_un_theme');
					break;
				case 'desabonnements':
					if (empty($rubriques))
						$erreurs['rubriques'] = _T('lettres:vous_devez_choisir_un_theme');
					if (lettres_verifier_validite_email($email)) {
						$abonne = new abonne(0, $email);
						if (!$abonne->existe)
							$erreurs['choix'] = _T('lettres:vous_n_etes_pas_abonnes');
					}
					break;
				case 'changement_format':
					if (lettres_verifier_validite_email($email)) {
						$abonne = new abonne(0, $email);
						if (!$abonne->existe)
							$erreurs['choix'] = _T('lettres:vous_n_etes_pas_abonnes');
					}
					break;
			}
		}

		return $erreurs;
	}


	function formulaires_lettres_traiter_dist() {
		$email		= _request('email');
		$nom		= _request('nom');
		$rubriques	= _request('rubriques');
		$format		= _request('format');
		$choix		= _request('choix');
		$lang		= $GLOBALS['spip_lang'];

		switch($choix) {
			case 'abonnements':
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
				if ($resultat)
					return array('message_ok' => _T('lettres:envoi_abonnements_succes'));
				else
					return array('message_erreur' => _T('lettres:envoi_abonnements_erreur'));
			case 'desabonnements':
				$abonne = new abonne(0, $email);
				$themes = implode(',', $rubriques);
				$resultat = $abonne->envoyer_notification('desabonnements', 
															array(
																'lang'		=> $lang,
																'rubriques'	=> $rubriques,
																'themes'	=> $themes
																)
															);
				if ($resultat)
					return array('message_ok' => _T('lettres:envoi_desabonnements_succes'));
				else
					return array('message_erreur' => _T('lettres:envoi_desabonnements_erreur'));
			case 'changement_format':
				$abonne = new abonne(0, $email);
				$resultat = $abonne->envoyer_notification('changement_format', 
															array(
																'lang'		=> $lang,
																'format'	=> $format
																)
															);
				if ($resultat)
					return array('message_ok' => _T('lettres:envoi_changement_format_succes'));
				else
					return array('message_erreur' => _T('lettres:envoi_changement_format_erreur'));
		}
	}


?>