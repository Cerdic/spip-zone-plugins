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


	function balise_FORMULAIRE_ESPACE_APPLICANT($p) {
		return calculer_balise_dynamique($p,'FORMULAIRE_ESPACE_APPLICANT', array());
	}


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