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


	function action_statut_formulaire() {

		$id_formulaire = $_GET['id_formulaire'];
		$redirection = generer_url_ecrire('formulaires', "id_formulaire=$id_formulaire", true);

		if (autoriser('editer', 'formulaires', $id_formulaire)) {

			$formulaire = new formulaire($id_formulaire);

			if (!empty($_REQUEST['statut'])) {
				$statut = $_REQUEST['statut'];
				if ($statut!="en_ligne" || autoriser('publierdans','rubrique',$formulaire->id_rubrique)) {
					$redirection = $formulaire->enregistrer_statut($statut);
				}
			}
		
		}
		
		if ($redirection) {
			header('Location: ' . $redirection);
			exit();
		}

	}
	

?>