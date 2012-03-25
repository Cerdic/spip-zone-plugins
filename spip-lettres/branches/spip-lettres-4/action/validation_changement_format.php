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

if (!defined("_ECRIRE_INC_VERSION")) return;

	include_spip('lettres_fonctions');


	/**
	 * action_validation_changement_format
	 *
	 * @author  Pierre Basson
	 */
	function action_validation_changement_format() {
		$email	= $_REQUEST['email'];
		$code	= $_REQUEST['code'];
		$format	= $_REQUEST['format'];
		$lang	= $_REQUEST['lang'];

		if (lettres_verifier_validite_email($email)) {
			$abonne = new abonne(0, $email);
			if ($abonne->existe and $abonne->verifier_code($code) and isset($format)) {
				$abonne->format = $format;
				$abonne->enregistrer();
				$redirection = generer_url_public($GLOBALS['meta']['spip_lettres_fond_formulaire_lettres'], "lang=$lang&message=validation_changement_format_succes", true);
			} else {
				$redirection = generer_url_public($GLOBALS['meta']['spip_lettres_fond_formulaire_lettres'], "lang=$lang&message=validation_changement_format_erreur", true);
			}
		} else {
			$redirection = generer_url_public($GLOBALS['meta']['spip_lettres_fond_formulaire_lettres'], "lang=$lang&message=validation_changement_format_erreur", true);
		}

		if ($id_theme = intval($_REQUEST['id_theme']) AND sql_countsel ("spip_themes", "id_theme=$id_theme") == 1)
			$redirection .= "&id_theme=$id_theme";

		header('Location: ' . $redirection);
		exit();

	}

?>