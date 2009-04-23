<?php


	/**
	 * SPIP-Formulaires
	 *
	 * @copyright 2006-2007 Artégo
	 **/


	include_spip('formulaires_fonctions');


	/**
	 * action_valider_formulaire
	 *
	 * @author  Pierre Basson
	 */
	function action_valider_formulaire() {

		$id_formulaire		= intval($_POST['id_formulaire']);
		$id_bloc			= intval($_POST['id_bloc']);
		$retour				= $_POST['retour'];
		$lang				= $_POST['lang'];
		$nouvel_applicant	= false;

		if (isset($_COOKIE['spip_formulaires_test_cookie'])) {

			if (!empty($_COOKIE['spip_formulaires_mcrypt_iv']) AND !empty($_COOKIE['spip_formulaires_id_applicant'])) {
				$id_applicant = formulaires_identifier_applicant();
				$applicant = new applicant($id_applicant);
				if ($applicant->existe) {
					$nouvel_applicant = false;
				} else {
					$applicant->supprimer_cookies;
					$nouvel_applicant = true;
				}
			} else {
				$nouvel_applicant = true;
			}

			if ($nouvel_applicant) {
				$applicant = new applicant();
				$applicant->poser_cookies();
			}

			$url_formulaire	= generer_url_formulaire($id_formulaire);

			if ($id_formulaire AND $applicant->existe) {
				$application = new application($applicant->id_applicant, $id_formulaire); // pas besoin d'id_application pour l'instant, l'applicant ne peut avoir de ttes façons qu'un seul formulaire "temporaire" ouvert à la fois
				if (!$application->existe)
					$application->enregistrer();
				switch ($application->formulaire->type) {
					case 'plusieurs_pages':
						$application->enregistrer_bloc($id_bloc);
						$id_bloc = $application->formulaire->recuperer_bloc_apres($id_bloc);
						if (!empty($retour))
							$retour = parametre_url($retour, 'id_bloc', $id_bloc);
						$url_formulaire = parametre_url($url_formulaire, 'id_bloc', $id_bloc);
						break;
					case 'une_seule_page':
						$blocs = $application->formulaire->recuperer_blocs();
						foreach ($blocs as $valeur) {
							$application->enregistrer_bloc($valeur);
						}
						if (!empty($retour))
							$retour = parametre_url($retour, 'resultat', 'oui');
						$url_formulaire = parametre_url($url_formulaire, 'resultat', 'oui');
						break;
				}
			}
			$redirection = parametre_url($url_formulaire, 'lang', $lang);
			$retour = ereg_replace('&amp;', '&', $retour); 
			$redirection = ereg_replace('&amp;', '&', $redirection); 

		} else {

			$redirection = generer_url_public('aide', "lang=$lang", true);

		}

		if (!empty($retour))
			$redirection = $retour;

		header('Location: ' . $redirection.'#formulaire');
		exit();

	}

?>