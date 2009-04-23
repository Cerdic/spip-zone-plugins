<?php


	/**
	 * SPIP-Formulaires
	 *
	 * @copyright 2006-2007 Artégo
	 **/


	include_spip('formulaires_fonctions');
	include_spip('surcharges_fonctions');


	function action_applications_export() {
		if ($GLOBALS['auteur_session']['statut'] == '0minirezo') {

			$id_formulaire		= intval($_GET['id_formulaire']);
			$id_application		= intval($_GET['id_application']);

			if ($id_application)
				$where = ' AND id_application='.$id_application;

			$resultats = array();
			$i = 0;

			$questions = spip_query('SELECT Q.* FROM spip_questions AS Q INNER JOIN spip_blocs AS B ON B.id_bloc=Q.id_bloc WHERE B.id_formulaire="'.$id_formulaire.'" ORDER BY B.ordre, Q.ordre');
			while ($question = spip_fetch_array($questions))
				$resultats[$i][] = propre($question['titre']);
			$i++;

			$applications = spip_query('SELECT * FROM spip_applications WHERE id_formulaire="'.$id_formulaire.'"'.$where);
			while ($application = spip_fetch_array($applications)) {
				$questions = spip_query('SELECT Q.* FROM spip_questions AS Q INNER JOIN spip_blocs AS B ON B.id_bloc=Q.id_bloc WHERE B.id_formulaire="'.$id_formulaire.'" ORDER BY B.ordre, Q.ordre');
				while ($question = spip_fetch_array($questions)) {
					$reponses = spip_query('SELECT * FROM spip_reponses WHERE id_question="'.$question['id_question'].'" AND id_application="'.$application['id_application'].'"');
					$tableau_reponses = array();
					$tableau_choix = array();
					while ($reponse = spip_fetch_array($reponses)) {
						$tableau_reponses[] = $reponse['valeur'];
					}
					switch ($question['type']) {
						case 'champ_texte':
						case 'zone_texte':
						case 'email_applicant':
						case 'nom_applicant':
						case 'fichier':
							$resultats[$i][] = implode(', ', $tableau_reponses);
							break;
						case 'boutons_radio':
						case 'cases_a_cocher':
						case 'liste':
						case 'liste_multiple':
						case 'abonnements':
						case 'auteurs':
							foreach ($tableau_reponses as $id_choix) {
								list($choix) = spip_fetch_array(spip_query('SELECT titre FROM spip_choix_question WHERE id_choix_question="'.$id_choix.'"'), SPIP_NUM);
								$tableau_choix[] = propre($choix);
							}
							$resultats[$i][] = implode(', ', $tableau_choix);
							break;
					}
				}
				$i++;
			}

			surcharges_exporter_csv('resultats', $resultats);

		}

	}


?>