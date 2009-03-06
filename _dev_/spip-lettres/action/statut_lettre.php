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
	 * action_statut_lettre
	 *
	 * @author Pierre BASSON
	 **/
	function action_statut_lettre() {

		$id_lettre = $_GET['id_lettre'];
		$redirection = generer_url_ecrire('lettres', "id_lettre=$id_lettre", true);

		if (autoriser('editer', 'lettres')) {

			$lettre = new lettre($id_lettre);

			if (!empty($_REQUEST['statut'])) {
				$statut = $_REQUEST['statut'];
				$redirection = $lettre->enregistrer_statut($statut);
			}
		
		}
		
		header('Location: ' . $redirection);
		exit();

	}
	

?>