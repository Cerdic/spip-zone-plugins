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


 	include_spip('inc/presentation');
	include_spip('inc/date');
	include_spip('inc/chercher_logo');
	include_spip('formulaires_fonctions');
	include_spip('inc/documents');
	include_spip('inc/rubriques');
	include_spip('inc/headers');
	include_spip('inc/iconifier');
	include_spip('surcharges_fonctions');
	include_spip('public/assembler');


	/**
	 * exec_applications_edit
	 *
	 * Edition d'une application
	 *
	 * @author Pierre Basson
	 **/
	function exec_applications_edit() {
		global $dir_lang, $spip_lang_right, $options;

		if ($GLOBALS['connect_statut'] != "0minirezo") {
			echo _T('avis_non_acces_page');
			echo fin_page();
			exit;
		}

		if (empty($_GET['id_application'])) {
			$url = generer_url_ecrire('formulaires_tous');
			header('Location: ' . $url);
			exit();
		}
		
		$id_application = intval($_GET['id_application']);
		list($id_formulaire, $id_applicant) = spip_fetch_array(spip_query('SELECT id_formulaire, id_applicant FROM spip_applications WHERE id_application="'.$id_application.'"'), SPIP_NUM);
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
			}
		}


		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page($formulaire->titre, "naviguer", "applications_tous");


		debut_gauche();
		debut_boite_info();
		echo "<div align='center'>\n";
		echo "<font face='Verdana,Arial,Sans,sans-serif' size='1'><b>"._T('formulairesprive:numero_application')."</b></font>\n";
		echo "<br><font face='Verdana,Arial,Sans,sans-serif' size='6'><b>".$application->id_application."</b></font>\n";
		echo "</div>\n";
		fin_boite_info();

		debut_raccourcis();
		echo icone_horizontale(_T('formulairesprive:retour_formulaire'), generer_url_ecrire("formulaires", "id_formulaire=".$application->formulaire->id_formulaire), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/formulaire-24.png', "", '');
		echo icone_horizontale(_T('formulairesprive:retour_application'), generer_url_ecrire("applications", "id_application=".$application->id_application), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/applications.png', "", '');
		fin_raccourcis();


    	debut_droite();
		debut_cadre_relief('../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/applications.png');

		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		if ($application->est_vide())
			$logo_statut = "puce-blanche.gif";
		else
			$logo_statut = "puce-verte.gif";
		echo "<tr width='100%'><td width='100%' valign='top'>";
#	 	echo "<span $dir_lang class='arial1 spip_medium'><b>" . $application->formulaire->titre . "</b></span>\n";
		gros_titre($application->applicant->email, $logo_statut);
		echo "</td>";
		echo "<td>", http_img_pack("rien.gif", ' ', "width='5'") ."</td>\n";
		echo "<td  align='$spip_lang_right' valign='top'>";
		icone(_T('formulairesprive:retour_application'), generer_url_ecrire("applications","id_application=".$application->id_application), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/applications.png', "rien.gif");
		echo "</td>";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<div>&nbsp;</div>";
		
		echo fin_cadre_relief();

		echo '<form action="'.generer_url_ecrire('applications_edit', 'id_application='.$application->id_application).'" name="formulaire" method="post" enctype="multipart/form-data">';

		if ($application->formulaire->limiter_invitation == 'oui' and $application->est_vide()) { // invitation
			$erreurs = array();
		} else {
			$id_dernier_bloc = $application->formulaire->recuperer_dernier_bloc();
			// on regarde si on a toutes les réponses aux questions obligatoires jusqu'au dernier bloc
			$tableau = $application->valider_bloc_par_bloc_jusquau_bloc($id_dernier_bloc, true);
			$erreurs = $tableau['erreurs'];
		}

		$blocs = spip_query('SELECT * FROM spip_blocs WHERE id_formulaire="'.$application->formulaire->id_formulaire.'" ORDER BY ordre');
		while ($bloc = spip_fetch_array($blocs)) {
			debut_cadre_couleur('../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/bloc.png', false, "", $bloc['titre']);
			$questions = spip_query('SELECT * FROM spip_questions WHERE id_bloc="'.$bloc['id_bloc'].'" ORDER BY ordre');
			if (spip_num_rows($questions) > 0) {
				echo "<div class='liste'>\n";
				echo "<table width='100%' cellpadding='3' cellspacing='0' border='0' background=''>\n";
			}
			while ($question = spip_fetch_array($questions)) {
				echo "<tr class='tr_liste' valign='top'>\n";
				echo "<td class='arial2' width='40%'>\n";
				echo "<label for='q_".$question['id_question']."'>\n";
				echo propre($question['titre']);
				echo "</label>\n";
				echo "</td>\n";
				echo "<td class='arial2' width='60%'>\n";
				$reponses = spip_query('SELECT * FROM spip_reponses WHERE id_question="'.$question['id_question'].'" AND id_application="'.$application->id_application.'"');
				$tableau_reponses = array();
				while ($reponse = spip_fetch_array($reponses)) {
					$tableau_reponses[] = $reponse['valeur'];
				}
				$choix = spip_query('SELECT * FROM spip_choix_question WHERE id_question="'.$question['id_question'].'" ORDER BY ordre');
				switch ($question['type']) {
					case 'champ_texte':
					case 'date':
					case 'email_applicant':
					case 'nom_applicant':
						echo '<input type="text" name="q_'.$question['id_question'].'" id="q_'.$question['id_question'].'" value="'.$tableau_reponses[0].'" class="'.($question['obligatoire'] ? 'fondo' : 'fondl').'" style="width: 100%;" /><br />';
						break;
					case 'zone_texte':
						echo '<textarea name="q_'.$question['id_question'].'" id="q_'.$question['id_question'].'" class="'.($question['obligatoire'] ? 'fondo' : 'fondl').'" style="width: 100%;">'.$tableau_reponses[0].'</textarea><br />';
						break;

					case 'boutons_radio':
						while ($choix_question = spip_fetch_array($choix)) {
							echo '<input type="radio" name="q_'.$question['id_question'].'" id="c_'.$choix_question['id_choix_question'].'" value="'.$choix_question['id_choix_question'].'" ';
							if (in_any($choix_question['id_choix_question'], $tableau_reponses, ''))
								echo 'checked="checked" ';
							echo '/> <label for="c_'.$choix_question['id_choix_question'].'">'.propre($choix_question['titre']).'</label><br />';
						}
						break;

					case 'cases_a_cocher':
						while ($choix_question = spip_fetch_array($choix)) {
							echo '<input type="checkbox" name="q_'.$question['id_question'].'[]" id="c_'.$choix_question['id_choix_question'].'" value="'.$choix_question['id_choix_question'].'" ';
							if (in_any($choix_question['id_choix_question'], $tableau_reponses, ''))
								echo 'checked="checked" ';
							echo '/> <label for="c_'.$choix_question['id_choix_question'].'">'.propre($choix_question['titre']).'</label><br />';
						}
						break;

					case 'liste':
						echo '<select name="q_'.$question['id_question'].'" id="q_'.$question['id_question'].'" class="'.($question['obligatoire'] ? 'fondo' : 'fondl').'" style="width: 100%;">';
						while ($choix_question = spip_fetch_array($choix)) {
							echo '<option value="'.$choix_question['id_choix_question'].'"';
							if (in_any($choix_question['id_choix_question'], $tableau_reponses, ''))
								echo 'selected="selected" ';
							echo '>'.propre($choix_question['titre']).'</option>';
						}
						echo '</select><br />';
						break;

					case 'liste_multiple':
						echo '<select name="q_'.$question['id_question'].'[]" id="q_'.$question['id_question'].'" multiple="multiple" size="10" class="'.($question['obligatoire'] ? 'fondo' : 'fondl').'" style="width: 100%;">';
						while ($choix_question = spip_fetch_array($choix)) {
							echo '<option value="'.$choix_question['id_choix_question'].'"';
							if (in_any($choix_question['id_choix_question'], $tableau_reponses, ''))
								echo 'selected="selected" ';
							echo '>'.propre($choix_question['titre']).'</option>';
						}
						echo '</select><br />';
						break;

					case 'abonnements':
						while ($choix_question = spip_fetch_array($choix)) {
							echo '<input type="checkbox" name="q_'.$question['id_question'].'[]" id="c_'.$choix_question['id_choix_question'].'" value="'.$choix_question['id_choix_question'].'" ';
							if (in_any($choix_question['id_choix_question'], $tableau_reponses, ''))
								echo 'checked="checked" ';
							echo '/> <label for="c_'.$choix_question['id_choix_question'].'">'.propre($choix_question['titre']).'</label><br />';
						}
						break;

					case 'fichier':
						$reponses = spip_query('SELECT * FROM spip_reponses WHERE id_question="'.$question['id_question'].'" AND id_application="'.$application->id_application.'"');
						while ($reponse = spip_fetch_array($reponses)) {
							list($fichier, $titre) = spip_fetch_array(spip_query('SELECT fichier, titre FROM spip_documents WHERE id_document="'.$reponse['valeur'].'"'), SPIP_NUM);
							echo '<a href="../'.$fichier.'" target="_blank">'.$titre.'</a> ('._T('formulairesprive:supprimer').' <input type="checkbox" class="case_a_cocher" name="s_'.$reponse['id_reponse'].'" value="'.$reponse['valeur'].'" />)<br />';
						}
						echo '<input type="file" id="q_'.$question['id_question'].'" name="q_'.$question['id_question'].'" /><br />';
						break;

					case 'auteurs':
						echo '<select name="q_'.$question['id_question'].'" id="q_'.$question['id_question'].'" class="'.($question['obligatoire'] ? 'fondo' : 'fondl').'" style="width: 100%;">';
						while ($choix_question = spip_fetch_array($choix)) {
							echo '<option value="'.$choix_question['id_choix_question'].'"';
							if (in_any($choix_question['id_choix_question'], $tableau_reponses, ''))
								echo 'selected="selected" ';
							echo '>'.propre($choix_question['titre']).'</option>';
						}
						echo '</select><br />';
						break;
				}
				if (in_any($question['id_question'], $erreurs, '')) {
					echo '<strong>'.formulaires_afficher_erreur(true, $question['controle']).'</strong>';
				}
				echo "</td>\n";
				echo "</tr>\n";
			}
			if (spip_num_rows($questions) > 0) {
				echo "</table>\n";
				echo "</div>\n";
			}
			fin_cadre_couleur();
		}

		echo "<div align='right'>";
		echo "<INPUT CLASS='fondo' TYPE='submit' NAME='enregistrer' VALUE='"._T('formulairesprive:enregistrer')."'>";
		echo "</div>";

		echo "</form>";

		echo fin_gauche();

		echo fin_page();

	}

?>