<?php


	/**
	 * SPIP-Formulaires
	 *
	 * @copyright 2006-2007 Artégo
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
		global $lang, $email, $mdp, $retour;
		$id_formulaire = intval($_POST['id_formulaire']);
		$identifier = false;

		if (isset($_COOKIE['spip_formulaires_test_cookie'])) {

			if (!empty($_COOKIE['spip_formulaires_mcrypt_iv']) AND !empty($_COOKIE['spip_formulaires_id_applicant'])) {
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
					$retour = parametre_url($retour, 'bad_pass', '1');
				}
			}

			$redirection = html_entity_decode($retour);

		} else {

			$redirection = generer_url_public('aide', "lang=$lang", true);

		}


		header('Location: ' . $redirection);
		exit();

	}

?>