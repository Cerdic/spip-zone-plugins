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


	if (!defined("_ECRIRE_INC_VERSION")) return;
 	include_spip('inc/presentation');
	include_spip('formulaires_fonctions');


	function exec_applications_edit() {

		global $spip_lang_right;

		$id_application = intval($_GET['id_application']);
		$t = sql_fetsel('id_formulaire, id_applicant', 'spip_applications', 'id_application='.intval($id_application));
		$id_applicant = $t['id_applicant'];
		$id_formulaire = $t['id_formulaire'];
		if (!autoriser('editer', 'formulaires',$id_formulaire)) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		$application = new application($id_applicant, $id_formulaire, $id_application);

		pipeline('exec_init',array('args'=>array('exec'=>'applications_edit','id_application'=>$application->id_application),'data'=>''));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page($application->applicant->email, "naviguer", "formulaires_tous");

		echo debut_gauche('', true);
		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'applications','id_application'=>$application->id_application),'data'=>''));

		creer_colonne_droite();
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'applications','id_application'=>$application->id_application),'data'=>''));

    	echo debut_droite('', true);

		echo '<div class="cadre-formulaire-editer">';
		echo '<div class="entete-formulaire">';
		echo icone_inline(_T('icone_retour'), generer_url_ecrire('applications', 'id_application='.$application->id_application), _DIR_PLUGIN_FORMULAIRES.'/prive/images/applications.png', "rien.gif", $GLOBALS['spip_lang_left']);
		echo _T('formulairesprive:edition');
		echo '<h1>'.$application->applicant->email.'</h1>';
		echo '</div>';

		if ($application->formulaire->limiter_invitation == 'oui' and $application->est_vide()) { // invitation
			$erreurs = array();
		} else {
			$id_dernier_bloc = $application->formulaire->recuperer_dernier_bloc();
			// on regarde si on a toutes les réponses aux questions obligatoires jusqu'au dernier bloc
			$tableau = $application->valider_bloc_par_bloc_jusquau_bloc($id_dernier_bloc, true);
			$erreurs = $tableau['erreurs'];
		}

		echo '<div class="formulaire_spip formulaire_editer">';
		echo '<form method="post" action="'.generer_url_action('valider_application', 'id_application='.$application->id_application, true, true).'" enctype="multipart/form-data">';
		echo '<div>';

	  	echo '<ul>';

		$blocs = sql_select('*', 'spip_blocs', 'id_formulaire='.intval($application->formulaire->id_formulaire), '', 'ordre');
		while ($bloc = sql_fetch($blocs)) {
			echo '<li class="fieldset">';
			echo '<fieldset>';
			echo '<h3 class="legend">'.$bloc['titre'].'</h3>';
			echo '<ul>';

			$questions = sql_select('*', 'spip_questions', 'id_bloc='.intval($bloc['id_bloc']), '', 'ordre');
			while ($question = sql_fetch($questions)) {
			    echo '<li class="'.($question['obligatoire'] ? 'obligatoire' : '').'">';
				echo '<label for="q_'.$question['id_question'].'">'.typo($question['titre']).'</label>';
				$reponses = sql_select('*', 'spip_reponses', 'id_question='.intval($question['id_question']).' AND id_application='.intval($application->id_application));
				$tableau_reponses = array();
				while ($reponse = sql_fetch($reponses)) {
					$tableau_reponses[] = $reponse['valeur'];
				}
				$choix = sql_select('*', 'spip_choix_question', 'id_question='.intval($question['id_question']), 'ordre');
				switch ($question['type']) {
					case 'champ_texte':
					case 'date':
						echo '<input type="text" class="text" name="q_'.$question['id_question'].'" id="q_'.$question['id_question'].'" value="'.$tableau_reponses[0].'" />';
						break;
					case 'nom_applicant':
						echo '<input type="text" class="text" name="q_'.$question['id_question'].'" id="q_'.$question['id_question'].'" value="'.$application->applicant->nom.'" />';
						break;
					case 'email_applicant':
						echo '<input type="text" class="text" name="q_'.$question['id_question'].'" id="q_'.$question['id_question'].'" value="'.$application->applicant->email.'" />';
						break;
					case 'zone_texte':
						echo '<textarea class="textarea" name="q_'.$question['id_question'].'" id="q_'.$question['id_question'].'">'.$tableau_reponses[0].'</textarea>';
						break;

					case 'boutons_radio':
						while ($choix_question = sql_fetch($choix)) {
							echo '<div class="choix">';
							echo '<input id="c_q_'.$choix_question['id_choix_question'].'" type="radio" class="radio" value="'.$choix_question['id_choix_question'].'" name="q_'.$question['id_question'].'" ';
							if (in_any($choix_question['id_choix_question'], $tableau_reponses, ''))
								echo 'checked="checked" ';
							echo '/> ';
							echo '<label for="c_q_'.$choix_question['id_choix_question'].'">'.typo($choix_question['titre']).'</label>';
							echo '</div>';
						}
						break;

					case 'cases_a_cocher':
						while ($choix_question = sql_fetch($choix)) {
							echo '<div class="choix">';
							echo '<input id="c_q_'.$choix_question['id_choix_question'].'" type="checkbox" class="checkbox" value="'.$choix_question['id_choix_question'].'" name="q_'.$question['id_question'].'[]" ';
							if (in_any($choix_question['id_choix_question'], $tableau_reponses, ''))
								echo 'checked="checked" ';
							echo '/> ';
							echo '<label for="c_q_'.$choix_question['id_choix_question'].'">'.typo($choix_question['titre']).'</label>';
							echo '</div>';
						}
						break;

					case 'liste':
						echo '<select class="select" name="q_'.$question['id_question'].'" id="q_'.$question['id_question'].'">';
						while ($choix_question = sql_fetch($choix)) {
							echo '<option value="'.$choix_question['id_choix_question'].'"';
							if (in_any($choix_question['id_choix_question'], $tableau_reponses, ''))
								echo 'selected="selected" ';
							echo '>'.typo($choix_question['titre']).'</option>';
						}
						echo '</select>';
						break;

					case 'liste_multiple':
						echo '<select class="select" name="q_'.$question['id_question'].'[]" id="q_'.$question['id_question'].'" multiple="multiple" size="10">';
						while ($choix_question = sql_fetch($choix)) {
							echo '<option value="'.$choix_question['id_choix_question'].'"';
							if (in_any($choix_question['id_choix_question'], $tableau_reponses, ''))
								echo 'selected="selected" ';
							echo '>'.typo($choix_question['titre']).'</option>';
						}
						echo '</select>';
						break;

					case 'abonnements':
						while ($choix_question = sql_fetch($choix)) {
							echo '<div class="choix">';
							echo '<input id="c_q_'.$choix_question['id_choix_question'].'" type="checkbox" class="checkbox" value="'.$choix_question['id_choix_question'].'" name="q_'.$question['id_question'].'[]" ';
							if (in_any($choix_question['id_choix_question'], $tableau_reponses, ''))
								echo 'checked="checked" ';
							echo '/> ';
							echo '<label for="c_q_'.$choix_question['id_choix_question'].'">'.typo($choix_question['titre']).'</label>';
							echo '</div>';
						}
						break;

					case 'fichier':
						$reponses = sql_select('*', 'spip_reponses', 'id_question='.intval($question['id_question']).' AND id_application='.intval($application->id_application));
						while ($reponse = sql_fetch($reponses)) {
							$tab = sql_fetsel('fichier, titre', 'spip_documents', 'id_document='.intval($reponse['valeur']));
							$fichier = $tab['fichier'];
							$titre = $tab['titre'];
							echo '<a href="../'.$fichier.'" target="_blank">'.$titre.'</a> ('._T('formulairesprive:supprimer').' <input type="checkbox" class="case_a_cocher" name="s_'.$reponse['id_reponse'].'" value="'.$reponse['valeur'].'" />)<br />';
						}
						echo '<input type="file" class="file" id="q_'.$question['id_question'].'" name="q_'.$question['id_question'].'" />';
						$q = new question($application->formulaire->id_formulaire, $bloc['id_bloc'], $question['id_question']);
						echo '<span class="explication">'.implode(', ', $q->fichiers).'</span>';
						break;

					case 'auteurs':
						echo '<select class="select" name="q_'.$question['id_question'].'" id="q_'.$question['id_question'].'">';
						while ($choix_question = sql_fetch($choix)) {
							echo '<option value="'.$choix_question['id_choix_question'].'"';
							if (in_any($choix_question['id_choix_question'], $tableau_reponses, ''))
								echo 'selected="selected" ';
							echo '>'.typo($choix_question['titre']).'</option>';
						}
						echo '</select>';
						break;
				}
				if (in_any($question['id_question'], $erreurs, '')) {
					echo '<span class="erreur_message">'.formulaires_afficher_erreur(true, $question['controle']).'</span>';
				}
				echo '</li>';
			}
			echo '</ul>';
			echo '</fieldset>';
			echo '</li>';
		}

		echo '</ul>';

	  	echo '<p class="boutons"><input type="submit" class="submit" name="enregistrer" value="'._T('formulairesprive:enregistrer').'" /></p>';

		echo '<input type="hidden" name="id_formulaire" value="'.$application->formulaire->id_formulaire.'" />';
		echo '<input type="hidden" name="lang" value="'.$application->formulaire->lang.'" />';

		echo '</div>';
		echo '</form>';
		echo '</div>';
		echo '</div>';

		echo fin_gauche();

		echo fin_page();

	}





?>