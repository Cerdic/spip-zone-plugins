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


	function action_editer_choix() {

		$id_sondage = $_POST['id_sondage'];
		$id_choix	= $_POST['id_choix'];
		$titre		= $_POST['titre'];
		$position	= $_POST['position'];
		$retour		= $_POST['retour'];

		if (autoriser('editer', 'sondages')) {

			$choix = new choix($id_choix);
			$choix->id_sondage	= $id_sondage;
			$choix->titre		= $titre;
			$choix->enregistrer();
#			$choix->enregistrer_position($position);

		}
		
		if ($retour) {
			header('Location: '.generer_url_ecrire('sondages', 'id_sondage='.intval($choix->id_sondage), true));
			exit();
		}
		
	}
	

?>