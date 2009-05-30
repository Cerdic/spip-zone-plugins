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


	function action_supprimer_choix() {

		$id_choix	= $_REQUEST['id_choix'];
		$ajax		= $_REQUEST['ajax'];

		if (autoriser('editer', 'sondages')) {

			$choix = new choix($id_choix);
			$choix->supprimer();
		
		}
		
		if (!$ajax) {
			header('Location: '.generer_url_ecrire('sondages', 'id_sondage='.intval($choix->id_sondage), true));
			exit();
		}

	}
	

?>