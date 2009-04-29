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


	function action_supprimer_application() {

		$id_formulaire	= $_REQUEST['id_formulaire'];
		$id_application	= $_REQUEST['id_application'];
		$lang			= $_REQUEST['lang'];

		if (!empty($_COOKIE['spip_formulaires_mcrypt_iv']) AND !empty($_COOKIE['spip_formulaires_id_applicant'])) {
			$id_applicant = formulaires_identifier_applicant();
			$applicant = new applicant($id_applicant);
			if ($applicant->existe) {
				$application = new application($applicant->id_applicant, $id_formulaire, $id_application);
				if ($application->existe) {
					$application->supprimer();
				}
			}
		}

		$url = generer_url_public('espace_applicant', 'lang='.$lang, true);
		header('Location: ' . $url);
		exit();

	}
	

?>