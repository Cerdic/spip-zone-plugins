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


	function action_valider_application() {
		$id_formulaire = $t['id_formulaire'];
		if (autoriser('editer', 'formulaires', $id_formulaire)) {
			$id_application = $_REQUEST['id_application'];
			$t = sql_fetsel('id_formulaire, id_applicant', 'spip_applications', 'id_application='.intval($id_application));
			$id_applicant = $t['id_applicant'];
			$application = new application($id_applicant, $id_formulaire, $id_application);
			if (!empty($_POST['enregistrer'])) {
				$blocs = $application->formulaire->recuperer_blocs();
				foreach ($blocs as $valeur) {
					$application->enregistrer_bloc($valeur);
				}
				$id_dernier_bloc = $application->formulaire->recuperer_dernier_bloc();
				// on regarde si on a toutes les réponses aux questions obligatoires jusqu'au dernier bloc
				$tableau = $application->valider_bloc_par_bloc_jusquau_bloc($id_dernier_bloc, true);
				$resultat_bon = $tableau['resultat_bon'];
				if ($resultat_bon) {
					$url = generer_url_ecrire('applications', 'id_application='.$application->id_application, true);
					header('Location: ' . $url);
					exit();
				} else {
					$url = generer_url_ecrire('applications_edit', 'id_application='.$application->id_application, true);
					header('Location: ' . $url);
					exit();
				}
			}
		}
	}
	

?>