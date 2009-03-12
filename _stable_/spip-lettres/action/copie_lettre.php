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
	 * action_copie_lettre
	 *
	 * @author Pierre BASSON
	 **/
	function action_copie_lettre() {

		$copie_lettre = $_GET['copie_lettre'];

		if (autoriser('editer', 'lettres')) {

			$lettre = new lettre(-1);
			$lettre->copier_lettre($copie_lettre);
			$redirection = generer_url_ecrire('lettres', 'id_lettre='.$lettre->id_lettre, true);
			header('Location: ' . $redirection);
			exit();
		
		}

	}
	

?>