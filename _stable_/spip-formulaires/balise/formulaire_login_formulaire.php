<?php


	/**
	 * SPIP-Formulaires
	 *
	 * @copyright 2006-2007 Artégo
	 **/


	include_spip('formulaires_fonctions');
	include_spip('inc/formulaires_classes');


	/**
	 * balise_FORMULAIRE_LOGIN_FORMULAIRE
	 *
	 * @param p est un objet SPIP
	 * @return string formulaire
	 * @author Pierre Basson
	 **/
	function balise_FORMULAIRE_LOGIN_FORMULAIRE($p) {
		return calculer_balise_dynamique($p,'FORMULAIRE_LOGIN_FORMULAIRE', array());
	}


	/**
	 * balise_FORMULAIRE_LOGIN_FORMULAIRE_dyn
	 *
	 * Calcule la balise #FORMULAIRE_LOGIN_FORMULAIRE
	 *
	 * @return formulaire
	 * @author Pierre Basson
	 **/
	function balise_FORMULAIRE_LOGIN_FORMULAIRE_dyn() {
		global $lang;
		$bad_pass = intval($_GET['bad_pass']);
		$id_formulaire = intval($_GET['id_formulaire']);

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
												'bad_pass'		=> $bad_pass,
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
												'bad_pass'		=> $bad_pass,
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
								'bad_pass'		=> $bad_pass,
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
								'cookie_ko'	=> ' ',
								'lang'		=> $lang
							)
						),
						false
					);
		}

	}



?>