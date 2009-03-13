<?php


	/**
	 * SPIP-Formulaires
	 * 
	 * @copyright 2006-2007 Artégo
	 */


	include_spip('formulaires_fonctions');


	/**
	 * balise_FORMULAIRE_ESPACE_APPLICANT
	 *
	 * @param p est un objet SPIP
	 * @return string formulaire
	 * @author Pierre Basson
	 **/
	function balise_FORMULAIRE_ESPACE_APPLICANT($p) {
		return calculer_balise_dynamique($p,'FORMULAIRE_ESPACE_APPLICANT', array());
	}


	/**
	 * balise_FORMULAIRE_ESPACE_APPLICANT_dyn
	 *
	 * Calcule la balise #FORMULAIRE_ESPACE_APPLICANT
	 *
	 * @return formulaire
	 * @author Pierre Basson
	 **/
	function balise_FORMULAIRE_ESPACE_APPLICANT_dyn() {

		if (isset($_COOKIE['spip_formulaires_test_cookie'])) {
			$cookie_ko = false;
			if (!empty($_COOKIE['spip_formulaires_mcrypt_iv']) AND !empty($_COOKIE['spip_formulaires_id_applicant'])) {
				$id_applicant = formulaires_identifier_applicant();
				$applicant = new applicant($id_applicant);
				if ($applicant->existe) {
					return	inclure_balise_dynamique(
								array(
									'formulaires/formulaire_espace_applicant',
									0,
									array(
										'id_applicant'	=> $applicant->id_applicant
									)
								),
								false
							);
				}
			}
			return	inclure_balise_dynamique(
						array(
							'formulaires/formulaire_espace_applicant',
							0,
							array(
								'applicant_ko'	=> ' '
							)
						),
						false
					);

		} else {
			return	inclure_balise_dynamique(
						array(
							'formulaires/formulaire_espace_applicant',
							0,
							array(
								'cookie_ko'	=> ' '
							)
						),
						false
					);
		}

	}



?>