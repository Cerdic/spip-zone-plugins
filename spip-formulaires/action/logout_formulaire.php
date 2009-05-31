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
	 * action_logout_formulaire
	 *
	 * vérifie qu'on a affaire à un applicant identifié avant de supprimer ses cookies
	 * redirige vers la page d'accueil
	 *
	 * @author  Pierre Basson
	 */
	function action_logout_formulaire() {

		$lang	= $_REQUEST['lang'];
		$retour	= $_REQUEST['retour'];
		
		if (!empty($_COOKIE['spip_formulaires_mcrypt_iv']) AND !empty($_COOKIE['spip_formulaires_id_applicant'])) {
			$id_applicant = formulaires_identifier_applicant();
			$applicant = new applicant($id_applicant);
			if ($applicant->existe) {
				$applicant->supprimer_cookies();
			}
		}
			
		if (empty($retour))
			$redirection = generer_url_public($GLOBALS['meta']['spip_formulaires_fond_formulaire_espace_formulaire'], 'lang='.$lang, true);
		else
			$redirection = html_entity_decode($retour);

		header('Location: ' . $redirection);
		exit();

	}

?>