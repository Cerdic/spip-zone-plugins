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
	 * exec_applications
	 *
	 * Visualisation d'une application
	 *
	 * @author Pierre Basson
	 **/
	function exec_applications() {
		global $dir_lang, $lang, $spip_lang_right, $options;

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

		if (!$application->existe) {
			$url = generer_url_ecrire('formulaires_tous');
			header('Location: ' . $url);
			exit();
		}

		if (!empty($_POST['changer'])) {
			if ($_POST['changer_action'] == 'poubelle')
				$application->supprimer();
			$url = generer_url_ecrire('formulaires', 'id_formulaire='.$id_formulaire, true);
			header('Location: ' . $url);
			exit();
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
		echo icone_horizontale(_T('formulairesprive:exporter_ce_resultat'), generer_url_action("applications_export", "id_application=".$application->id_application."&id_formulaire=".$application->formulaire->id_formulaire), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/export.png', "", '');
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
		icone(_T('formulairesprive:modifier_application'), generer_url_ecrire("applications_edit","id_formulaire=".$application->formulaire->id_formulaire."&id_applicant=".$application->applicant->id_applicant."&id_application=".$application->id_application), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/applications.png', "edit.gif");
		echo "</td>";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<div>&nbsp;</div>";
		
		echo generer_url_post_ecrire("applications", "id_application=$id_application", 'formulaire');
		debut_cadre_relief();
		echo "<center><B>"._T('formulairesprive:action')."</B>&nbsp;";
		echo "<SELECT NAME='changer_action' SIZE='1' CLASS='fondl'>\n";
		echo '	<OPTION VALUE="aucune">'._T('formulairesprive:action_aucune').'</OPTION>'."\n";
		echo '	<OPTION VALUE="poubelle">'._T('formulairesprive:action_poubelle').'</OPTION>'."\n";
		echo "</SELECT>";
		echo "&nbsp;&nbsp;<INPUT TYPE='submit' NAME='changer' CLASS='fondo' VALUE='"._T('formulairesprive:changer')."' STYLE='font-size:10px'>";
		echo '</center>';
		fin_cadre_relief();
		echo '</form>';

		echo fin_cadre_relief();

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
				echo propre($question['titre']);
				echo "</td>\n";
				echo "<td class='arial2' width='60%'>\n";
				$reponses = spip_query('SELECT * FROM spip_reponses WHERE id_question="'.$question['id_question'].'" AND id_application="'.$application->id_application.'"');
				$tableau_reponses = array();
				while ($reponse = spip_fetch_array($reponses)) {
					$tableau_reponses[] = $reponse['valeur'];
				}
				switch ($question['type']) {
					case 'champ_texte':
					case 'zone_texte':
					case 'email_applicant':
					case 'nom_applicant':
						foreach ($tableau_reponses as $valeur)
							echo nl2br($valeur);
						break;
					case 'date':
						foreach ($tableau_reponses as $valeur)
							echo $valeur;
						break;
					case 'boutons_radio':
					case 'cases_a_cocher':
					case 'liste':
					case 'liste_multiple':
					case 'auteurs':
						foreach ($tableau_reponses as $id_choix) {
							list($choix) = spip_fetch_array(spip_query('SELECT titre FROM spip_choix_question WHERE id_choix_question="'.$id_choix.'"'), SPIP_NUM);
							echo propre($choix).'<br />';
						}
						break;
					case 'abonnements':
						foreach ($tableau_reponses as $id_choix) {
							list($choix, $id_rubrique) = spip_fetch_array(spip_query('SELECT titre, id_rubrique FROM spip_choix_question WHERE id_choix_question="'.$id_choix.'"'), SPIP_NUM);
							echo '<a href="'.generer_url_ecrire('naviguer', 'id_rubrique='.$id_rubrique).'">'.propre($choix).'</a><br />';
						}
						break;
					case 'fichier':
						foreach ($tableau_reponses as $id_choix) {
							$docs = spip_query('SELECT * FROM spip_documents WHERE id_document="'.$id_choix.'"');
							while ($document = spip_fetch_array($docs)) {
								echo '<a href="../'.$document['fichier'].'" target="_blank">'.$document['titre'].'</a><br />';
							}
						}
						break;
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

		echo fin_gauche();

		echo fin_page();

	}

?>