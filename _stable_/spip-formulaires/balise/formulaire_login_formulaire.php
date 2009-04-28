<?php


	/**
	 * SPIP-Formulaires
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	include_spip('formulaires_fonctions');


	function balise_FORMULAIRE_LOGIN_FORMULAIRE($p) {
		return calculer_balise_dynamique($p, 'FORMULAIRE_LOGIN_FORMULAIRE', array());
	}


	function balise_FORMULAIRE_LOGIN_FORMULAIRE_dyn() {
		global $lang;
		$bad_pass		= intval($_GET['bad_pass']);
		$cookies		= intval($_GET['cookies']);
		$id_formulaire	= intval($_GET['id_formulaire']);

		if (isset($_COOKIE['spip_formulaires_test_cookie'])) {
			$cookie_ko = false;
			if (!empty($_COOKIE['spip_formulaires_mcrypt_iv']) AND !empty($_COOKIE['spip_formulaires_id_applicant'])) {
				$id_applicant = formulaires_identifier_applicant();
				$applicant = new applicant($id_applicant);
				if ($applicant->existe) {
					if ($id_formulaire) {
						$application = new application($id_applicant, $id_formulaire);
						if ($application->formulaire->limiter_invitation == 'oui' and $application->existe) { // invitation
							return	inclure_balise_dynamique(
										array(
											'formulaires/formulaire_login_formulaire',
											0,
											array(
												'id_applicant'	=> $applicant->id_applicant,
												'bad_pass'		=> $bad_pass ? ' ' : '',
												'logout'		=> ' ',
												'lang'			=> $lang
											)
										),
										false
									);
						}
					} else {
							return	inclure_balise_dynamique(
										array(
											'formulaires/formulaire_login_formulaire',
											0,
											array(
												'id_applicant'	=> $applicant->id_applicant,
												'bad_pass'		=> $bad_pass ? ' ' : '',
												'logout'		=> ' ',
												'lang'			=> $lang
											)
										),
										false
									);
					}
				}
			}
			return	inclure_balise_dynamique(
						array(
							'formulaires/formulaire_login_formulaire',
							0,
							array(
								'login'			=> ' ',
								'id_formulaire'	=> $id_formulaire,
								'bad_pass'		=> $bad_pass ? ' ' : '',
								'lang'			=> $lang
							)
						),
						false
					);
		} else {
			return	inclure_balise_dynamique(
						array(
							'formulaires/formulaire_login_formulaire',
							0,
							array(
								'cookies'	=> $cookies ? ' ' : '',
								'login'		=> ' ',
								'lang'		=> $lang
							)
						),
						false
					);
		}

	}



?>