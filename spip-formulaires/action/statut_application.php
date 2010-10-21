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


	function action_statut_application() {

		$id_application = intval($_GET['id_application']);
		list($id_formulaire, $id_applicant) = spip_fetch_array(spip_query('SELECT id_formulaire, id_applicant FROM spip_applications WHERE id_application="'.$id_application.'"'), SPIP_NUM);
		$application = new application($id_applicant, $id_formulaire, $id_application);

		if (autoriser('editer', 'formulaires', $id_formulaire)) {

			if ($_REQUEST['statut'] == 'poubelle') {
				$application->supprimer();
				$url = generer_url_ecrire('formulaires', 'id_formulaire='.$id_formulaire, true);
				header('Location: ' . $url);
				exit();
			}

			if ($_REQUEST['statut'] == 'export') {
				$application->exporter();
			}
			
			if ($_REQUEST['statut'] == 'inviter') {
				$application->envoyer_invitation();
				$url = generer_url_ecrire('applications', 'id_application='.$id_application, true);
				header('Location: ' . $url);
				exit();
			}
			
		}
		
	}
	

?>