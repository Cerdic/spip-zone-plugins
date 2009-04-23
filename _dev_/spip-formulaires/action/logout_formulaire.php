<?php


	/**
	 * SPIP-Formulaires
	 *
	 * @copyright 2006-2007 Artégo
	 **/


	include_spip('formulaires_fonctions');

	
	/**
	 * action_logout_formulaire
	 *
	 * vérifie qu'on a affaire à un applicant identifié avant de supprimer ses cookies
	 * redirige vers la page d'accueil
	 *
	 * @author  Pierre Basson
	 */
	function action_logout_formulaire() {
		global $lang, $retour;
		
		if (!empty($_COOKIE['spip_formulaires_mcrypt_iv']) AND !empty($_COOKIE['spip_formulaires_id_applicant'])) {
			$id_applicant = formulaires_identifier_applicant();
			$applicant = new applicant($id_applicant);
			if ($applicant->existe) {
				$applicant->supprimer_cookies();
			}
		}
			
		if (empty($retour))
			$redirection = generer_url_public($GLOBALS['meta']['spip_formulaires_fond_formulaire_espace_applicant'], 'lang='.$lang, true);
		else
			$redirection = html_entity_decode($retour);

		header('Location: ' . $redirection);
		exit();

	}

?>