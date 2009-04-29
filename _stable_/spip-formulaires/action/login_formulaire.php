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


	/**
	 * action_login_formulaire
	 *
	 * vérifie les identifiants
	 * si correct :
	 * - pose les cookies
	 * sinon :
	 * - boucle sur retour
	 *
	 * @author  Pierre Basson
	 */
	function action_login_formulaire() {

		$lang			= $_REQUEST['lang'];
		$email			= $_REQUEST['email'];
		$mdp			= $_REQUEST['mdp'];
		$id_formulaire	= $_REQUEST['id_formulaire'];

		$identifier = false;

		if ($id_formulaire) {
			$redirection = generer_url_public('formulaire', 'id_formulaire='.$id_formulaire, true);
		} else {
			$redirection = generer_url_public($GLOBALS['meta']['spip_formulaires_fond_formulaire_espace_applicant'], ($_lang ? 'lang='.$_lang : ''), true);
		}
		
		if ($email == '' or $mdp == '') {
			$redirection.= '&bad_pass=1';
			header('Location: ' . $redirection);
			exit();
		}

		if (isset($_COOKIE['spip_formulaires_test_cookie'])) {

			if (!empty($_COOKIE['spip_formulaires_mcrypt_iv']) and !empty($_COOKIE['spip_formulaires_id_applicant'])) {
				$id_applicant = formulaires_identifier_applicant();
				$applicant = new applicant($id_applicant);
				if ($applicant->existe) {
					if ($id_formulaire) {
						$application = new application($applicant->id_applicant, $id_formulaire);
						if (!$application->existe) { // non invité
							$applicant->supprimer_cookies();
							$identifier = true;
						}
					}
				} else { // erreur d'identification, cookies non valides
					$applicant->supprimer_cookies();
					$identifier = true;
				}
			} else {
				$identifier = true;
			}

			if ($identifier) {
				if ($id_applicant = formulaires_identifier_applicant_avec_email_et_mdp($email, $mdp)) { // c'est correct
					$applicant = new applicant($id_applicant);
					$applicant->poser_cookies();
				} else { // incorrect
					$redirection.= '&bad_pass=1';
				}
			}

		} else {

			$redirection.= '&erreur_cookie=oui';

		}

		header('Location: ' . $redirection);
		exit();

	}


?>