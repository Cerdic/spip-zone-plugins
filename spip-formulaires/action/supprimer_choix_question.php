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


	function action_supprimer_choix_question() {

		$id_formulaire		= $_REQUEST['id_formulaire'];
		$id_bloc			= $_REQUEST['id_bloc'];
		$id_question		= $_REQUEST['id_question'];
		$id_choix_question	= $_REQUEST['id_choix_question'];
		$ajax				= $_REQUEST['ajax'];

		if (autoriser('editer', 'formulaires', $id_formulaire)) {

			$choix_question = new choix_question($id_formulaire, $id_bloc, $id_question, $id_choix_question);
			$choix_question->supprimer();

		}

		if ($ajax != 1) {
			header('Location: '.generer_url_ecrire('formulaires', 'id_formulaire='.$id_formulaire, true));
			exit();
		}

	}
	

?>