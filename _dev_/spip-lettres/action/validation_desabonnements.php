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
	include_spip('inc/filtres');


	/**
	 * action_validation_desabonnements
	 *
	 * @author  Pierre Basson
	 */
	function action_validation_desabonnements() {
		$email		= $_REQUEST['email'];
		$code		= $_REQUEST['code'];
		$rubriques	= $_REQUEST['rubriques'];
		$lang		= $_REQUEST['lang'];

		if (email_valide($email)) {
			$abonne = new abonne(0, $email);
			if ($abonne->existe and $abonne->verifier_code($code) and isset($rubriques)) {
				if (is_array($rubriques)) {
					foreach ($rubriques as $id_rubrique)
						$abonne->valider_desabonnement($id_rubrique);
				} else if ($rubriques == -1) {
					$abonne->valider_desabonnement(-1);
				}
				$abonne->supprimer_si_zero_abonnement();
				$redirection = generer_url_public($GLOBALS['meta']['spip_lettres_fond_formulaire_lettres'], "lang=$lang&message=validation_desabonnements_succes", true);
			} else {
				$redirection = generer_url_public($GLOBALS['meta']['spip_lettres_fond_formulaire_lettres'], "lang=$lang&message=validation_desabonnements_erreur", true);
			}
		} else {
			$redirection = generer_url_public($GLOBALS['meta']['spip_lettres_fond_formulaire_lettres'], "lang=$lang&message=validation_desabonnements_erreur", true);
		}

		header('Location: ' . $redirection);
		exit();

	}

?>