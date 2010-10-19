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


	/**
	 * action_validation_abonnements
	 *
	 * @author  Pierre Basson
	 */
	function action_validation_abonnements() {
		$email		= $_REQUEST['email'];
		$code		= $_REQUEST['code'];
		$rubriques	= $_REQUEST['rubriques'];
		$lang		= $_REQUEST['lang'];

		if (lettres_verifier_validite_email($email)) {
			$abonne = new abonne(0, $email);
			if ($abonne->existe and $abonne->verifier_code($code) and isset($rubriques)) {
				foreach ($rubriques as $id_rubrique)
					$abonne->valider_abonnement($id_rubrique, true);
				$redirection = generer_url_public($GLOBALS['meta']['spip_lettres_fond_formulaire_lettres'], "lang=$lang&message=validation_abonnements_succes", true);
			} else {
				$redirection = generer_url_public($GLOBALS['meta']['spip_lettres_fond_formulaire_lettres'], "lang=$lang&message=validation_abonnements_erreur", true);
			}
		} else {
			$redirection = generer_url_public($GLOBALS['meta']['spip_lettres_fond_formulaire_lettres'], "lang=$lang&message=validation_abonnements_erreur", true);
		}

		header('Location: ' . $redirection);
		exit();

	}

?>