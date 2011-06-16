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


	function action_statut_abonne() {

		$id_abonne = $_GET['id_abonne'];
		$redirection = generer_url_ecrire('abonnes', "id_abonne=$id_abonne", true);

		if (autoriser('editer', 'lettres')) {

			$abonne = new abonne($id_abonne);

			if (!empty($_REQUEST['statut'])) {
				$statut = $_REQUEST['statut'];
				$redirection = $abonne->enregistrer_statut($statut);
			}
		
		}
		
		header('Location: ' . $redirection);
		exit();

	}
	

?>