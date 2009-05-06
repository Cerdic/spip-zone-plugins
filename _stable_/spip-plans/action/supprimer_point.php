<?php


	/**
	 * SPIP-Plans
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	include_spip('plans_fonctions');


	function action_supprimer_point() {

		$id_plan	= $_REQUEST['id_plan'];
		$id_point	= $_REQUEST['id_point'];
		$ajax		= $_REQUEST['ajax'];

		if (autoriser('editer', 'plans')) {

			$point = new point($id_plan, $id_point);
			$point->supprimer();
		
		}
		
		if (!$ajax) {
			header('Location: '.generer_url_ecrire('plans', 'id_plan='.intval($id_plan), true));
			exit();
		}

	}
	

?>