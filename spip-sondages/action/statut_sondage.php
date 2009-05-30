<?php


	/**
	 * SPIP-Sondages
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	include_spip('sondages_fonctions');


	function action_statut_sondage() {

		$id_sondage = $_GET['id_sondage'];
		$redirection = generer_url_ecrire('sondages', "id_sondage=$id_sondage", true);

		if (autoriser('editer', 'sondages')) {

			$sondage = new sondage($id_sondage);

			if (!empty($_REQUEST['statut'])) {
				$statut = $_REQUEST['statut'];
				$redirection = $sondage->enregistrer_statut($statut);
			}
		
		}
		
		header('Location: ' . $redirection);
		exit();

	}
	

?>