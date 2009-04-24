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


	function exec_applications() {

		global $spip_lang_right;

		if (!autoriser('voir', 'formulaires')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		$id_application = intval($_GET['id_application']);
		$t = sql_fetsel('id_formulaire, id_applicant', 'spip_applications', 'id_application='.intval($id_application));
		$id_formulaire = $t['id_formulaire'];
		$id_applicant = $t['id_applicant'];
		$application = new application($id_applicant, $id_formulaire, $id_application);

		pipeline('exec_init',array('args'=>array('exec'=>'applications','id_application'=>$application->id_application),'data'=>''));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page($application->applicant->email, "naviguer", "formulaires_tous");

		echo debut_gauche('', true);
		echo '<div class="cadre cadre-info verdana1">';
		echo '<div class="cadre_padding">';
		echo '<div class="infos">';
		echo '<div class="numero">';
		echo _T('formulairesprive:application_numero').' :';
		echo '<p>'.$application->id_application.'</p>';
		echo '</div>';
		echo '<ul class="instituer instituer_article">';
		echo '<li>';
		echo '<strong>'._T('formulairesprive:action').'</strong>';
		echo '<ul>';
		echo '<li class="prepa selected">'.http_img_pack('puce-blanche.gif', 'puce-blanche', '')._T('formulairesprive:hors_ligne').'</li>';
		echo '<li class="publie"><a href="'.generer_url_action('statut_formulaire', 'id_formulaire='.$formulaire->id_formulaire.'&statut=en_ligne', false, true).'">'.http_img_pack('puce-verte.gif', 'puce-verte', '')._T('formulairesprive:a_mettre_en_ligne').'</a></li>';
		echo '</ul>';
		echo '</li>';
		echo '</ul>';
		echo '</div>';
		echo '</div>';
		echo '</div>';


		$raccourcis.= icone_horizontale(_T('formulairesprive:retour_formulaire'), generer_url_ecrire("formulaires", "id_formulaire=".$application->formulaire->id_formulaire), _DIR_PLUGIN_FORMULAIRES.'/prive/images/formulaire-24.png', 'rien.gif', false);
		$raccourcis.= icone_horizontale(_T('formulairesprive:exporter_ce_resultat'), generer_url_action("applications_export", "id_application=".$application->id_application."&id_formulaire=".$application->formulaire->id_formulaire), _DIR_PLUGIN_FORMULAIRES.'/prive/images/export.png', 'rien.gif', false);
		echo bloc_des_raccourcis($raccourcis);

		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'applications','id_application'=>$application->id_application),'data'=>''));

		creer_colonne_droite();
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'applications','id_application'=>$application->id_application),'data'=>''));

    	echo debut_droite('', true);

		echo '<div class="fiche_objet">';

		echo '<div class="bandeau_actions">';
		echo '<div style="float: right;">';
		echo icone_inline(_T('formulairesprive:modifier_application'), generer_url_ecrire("applications_edit", "id_application=".$application->id_application), _DIR_PLUGIN_FORMULAIRES.'/prive/images/applications.png', "edit.gif", $GLOBALS['spip_lang_left']);
		echo '</div>';
		echo '</div>';

		echo '<h1>'.$application->applicant->email.'</h1>';

		echo '<br class="nettoyeur" />';

		echo pipeline('affiche_milieu',array('args'=>array('exec'=>'applications','application'=>$application->id_application),'data'=>''));

		echo '</div><!-- fin fiche_objet -->';

		$blocs = sql_select('*', 'spip_blocs', 'id_formulaire='.intval($application->formulaire->id_formulaire), '', 'ordre');
		while ($bloc = sql_fetch($blocs)) {
			echo debut_cadre_trait_couleur(_DIR_PLUGIN_FORMULAIRES.'/prive/images/bloc.png', true, "", $bloc['titre']);
			$questions = sql_select('*', 'spip_questions', 'id_bloc='.intval($bloc['id_bloc']), 'ordre');
			if (sql_count($questions) > 0) {
				echo "<div class='liste'>\n";
				echo "<table width='100%' cellpadding='3' cellspacing='0' border='0' background=''>\n";
			}
			while ($question = sql_fetch($questions)) {
				echo "<tr class='tr_liste' valign='top'>\n";
				echo "<td class='arial2' width='40%'>\n";
				echo typo($question['titre']);
				echo "</td>\n";
				echo "<td class='arial2' width='60%'>\n";
				$reponses = sql_select('*', 'spip_reponses', 'id_question='.intval($question['id_question']).' AND id_application='.intval($application->id_application));
				$tableau_reponses = array();
				while ($reponse = sql_fetch($reponses)) {
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
							$choix = sql_getfetsel('titre', 'spip_choix_question', 'id_choix_question='.intval($id_choix));
							echo typo($choix).'<br />';
						}
						break;
					case 'abonnements':
						foreach ($tableau_reponses as $id_choix) {
							$t = sql_fetsel('titre, id_rubrique', 'spip_choix_question', 'id_choix_question='.intval($id_choix));
							$choix = $t['choix'];
							$id_rubrique = $t['id_rubrique'];
							echo '<a href="'.generer_url_ecrire('naviguer', 'id_rubrique='.$id_rubrique).'">'.typo($choix).'</a><br />';
						}
						break;
					case 'fichier':
						foreach ($tableau_reponses as $id_choix) {
							$docs = sql_fetsel('*', 'spip_documents', 'id_document='.intval($id_choix));
							while ($document = sql_fetch($docs)) {
								echo '<a href="../'.$document['fichier'].'" target="_blank">'.$document['titre'].'</a><br />';
							}
						}
						break;
				}
				echo "</td>\n";
				echo "</tr>\n";
			}
			if (sql_count($questions) > 0) {
				echo "</table>\n";
				echo "</div>\n";
			}
			echo fin_cadre_trait_couleur(true);
		}

		echo fin_gauche();

		echo fin_page();

	}

?>