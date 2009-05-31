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


	function action_statut_plan() {

		$id_plan = $_GET['id_plan'];
		$redirection = generer_url_ecrire('plans', "id_plan=$id_plan", true);

		if (autoriser('editer', 'plans')) {

			$plan = new plan($id_plan);

			if (!empty($_REQUEST['statut'])) {
				$statut = $_REQUEST['statut'];
				$redirection = $plan->enregistrer_statut($statut);
			}
		
		}
		
		header('Location: ' . $redirection);
		exit();

	}
	

?>