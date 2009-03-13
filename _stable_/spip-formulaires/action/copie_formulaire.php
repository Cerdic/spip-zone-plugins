<?php


	/**
	 * SPIP-Formulaires
	 *
	 * @copyright 2006-2007 Artégo
	 **/


	include_spip('formulaires_fonctions');


	function action_copie_formulaire() {
		if ($GLOBALS['auteur_session']['statut'] == '0minirezo') {
			$id_formulaire = intval($_GET['id_formulaire']);

			$res = spip_query('SELECT * FROM spip_formulaires WHERE id_formulaire='.$id_formulaire);
			$formulaire = spip_fetch_array($res);

			spip_query('INSERT INTO spip_formulaires (id_rubrique,
													titre,
													descriptif,
													texte,
													ps,
													date_debut,
													date_fin,
													maj,
													type,
													limiter_temps,
													limiter_invitation,
													limiter_applicant,
													notifier_applicant,
													notifier_auteurs,
													en_ligne,
													statut)
											VALUES ('.$formulaire['id_rubrique'].',
													"'.addslashes('Copie - '.$formulaire['titre']).'",
													"'.addslashes($formulaire['descriptif']).'",
													"'.addslashes($formulaire['texte']).'",
													"'.addslashes($formulaire['ps']).'",
													"'.addslashes($formulaire['date_debut']).'",
													"'.addslashes($formulaire['date_fin']).'",
													NOW(),
													"'.addslashes($formulaire['type']).'",
													"'.addslashes($formulaire['limiter_temps']).'",
													"'.addslashes($formulaire['limiter_invitation']).'",
													"'.addslashes($formulaire['limiter_applicant']).'",
													"'.addslashes($formulaire['notifier_applicant']).'",
													"'.addslashes($formulaire['notifier_auteurs']).'",
													"non",
													"'.addslashes($formulaire['statut']).'")');
			$id = spip_insert_id();

			// logos
			include_spip('inc/chercher_logo');
			$logo_f = charger_fonction('chercher_logo', 'inc');
			if ($logo_on = $logo_f($id_formulaire, 'id_formulaire', 'on'))
				copy($logo_on[0], $logo_on[1].'formulaireon'.$id.'.'.$logo_on[3]);
			if ($logo_off = $logo_f($id_formulaire, 'id_formulaire', 'off'))
				copy($logo_off[0], $logo_off[1].'formulaireoff'.$id.'.'.$logo_off[3]);

			// documents
			$res = spip_query('SELECT D.* 
								FROM spip_documents AS D
								INNER JOIN spip_documents_formulaires AS DO ON D.id_document=DO.id_document
								WHERE DO.id_formulaire='.$id_formulaire);
			while ($arr = spip_fetch_array($res)) {
				$fichier_from = $arr['fichier'];
				$fichier_to = ereg_replace(basename($fichier_from), mktime().'-'.basename($fichier_from), $fichier_from);
				if (file_exists($fichier_from)) {
					copy($fichier_from, $fichier_to) or die($fichier_to);
					spip_query('INSERT INTO spip_documents (id_type,
															titre,
															date,
															descriptif,
															fichier,
															taille,
															largeur,
															hauteur,
															mode,
															maj)
													VALUES ('.$arr['id_type'].',
															"'.addslashes($arr['titre']).'",
															"'.addslashes($arr['date']).'",
															"'.addslashes($arr['descriptif']).'",
															"'.addslashes($fichier_to).'",
															"'.$arr['taille'].'",
															"'.$arr['largeur'].'",
															"'.$arr['hauteur'].'",
															"'.$arr['mode'].'",
															NOW())');
					$id_document = spip_insert_id();
					spip_query('INSERT INTO spip_documents_formulaires (id_document, id_formulaire) VALUES ('.$id_document.', '.$id.')');
					if ($arr['id_vignette'] != 0) {
						$vig = spip_query('SELECT * FROM spip_documents WHERE id_document='.$arr['id_vignette']);
						$vignette = spip_fetch_array($vig);
						$fichier_from = $vignette['fichier'];
						$fichier_to = ereg_replace(basename($fichier_from), mktime().'-'.basename($fichier_from), $fichier_from);
						if (file_exists($fichier_from)) {
							copy($fichier_from, $fichier_to) or die($fichier_to);
							spip_query('INSERT INTO spip_documents (id_type,
																	titre,
																	date,
																	descriptif,
																	fichier,
																	taille,
																	largeur,
																	hauteur,
																	mode,
																	maj)
															VALUES ('.$arr['id_type'].',
																	"'.addslashes($arr['titre']).'",
																	"'.addslashes($arr['date']).'",
																	"'.addslashes($arr['descriptif']).'",
																	"'.addslashes($fichier_to).'",
																	"'.$arr['taille'].'",
																	"'.$arr['largeur'].'",
																	"'.$arr['hauteur'].'",
																	"'.$arr['mode'].'",
																	NOW())');
							$id_vignette = spip_insert_id();
							spip_query('UPDATE spip_documents SET id_vignette='.$id_vignette.' WHERE id_document='.$id_document);
						}
					}
				}
			}

			// blocs
			$blocs = spip_query('SELECT * FROM spip_blocs WHERE id_formulaire='.$id_formulaire);
			while ($bloc = spip_fetch_array($blocs)) {
				spip_query('INSERT INTO spip_blocs (id_formulaire, ordre, titre, descriptif, texte)
											VALUES ('.$id.', '.$bloc['ordre'].', "'.addslashes($bloc['titre']).'", "'.addslashes($bloc['descriptif']).'", "'.addslashes($bloc['texte']).'")');
				$id_bloc = spip_insert_id();
				// questions
				$questions = spip_query('SELECT * FROM spip_questions WHERE id_bloc='.$bloc['id_bloc']);
				while ($question = spip_fetch_array($questions)) {
					spip_query('INSERT INTO spip_questions (id_bloc, ordre, titre, descriptif, type, obligatoire, controle)
								VALUES ('.$id_bloc.', '.$question['ordre'].', "'.addslashes($question['titre']).'", "'.addslashes($question['descriptif']).'", "'.addslashes($question['type']).'", "'.addslashes($question['obligatoire']).'", "'.addslashes($question['controle']).'")');
					$id_question = spip_insert_id();
					// choix
					$choixs = spip_query('SELECT * FROM spip_choix_question WHERE id_question='.$question['id_question']);
					while ($choix = spip_fetch_array($choixs)) {
						spip_query('INSERT INTO spip_choix_question (id_question, ordre, titre)
										VALUES ('.$id_question.', '.$choix['ordre'].', "'.addslashes($choix['titre']).'")');
					}
				}
			}

			// auteurs
			$auteurs = spip_query('SELECT * FROM spip_auteurs_formulaires WHERE id_formulaire='.$id_formulaire);
			while ($arr = spip_fetch_array($auteurs)) {
				spip_query('INSERT INTO spip_auteurs_formulaires (id_auteur, id_formulaire) VALUES ('.$arr['id_auteur'].', '.$id.')');
			}
			
			// mots-clés
			$mots = spip_query('SELECT * FROM spip_mots_formulaires WHERE id_formulaire='.$id_formulaire);
			while ($arr = spip_fetch_array($mots)) {
				spip_query('INSERT INTO spip_mots_formulaires (id_mot, id_formulaire) VALUES ('.$arr['id_mot'].', '.$id.')');
			}
			
			$redirection = generer_url_ecrire('formulaires', 'id_formulaire='.$id, true);
			header('Location: ' . $redirection);
			exit();
		}
	}


?>