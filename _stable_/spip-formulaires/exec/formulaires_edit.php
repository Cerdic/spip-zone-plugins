<?php


	/**
	 * SPIP-Formulaires
	 *
	 * @copyright 2006-2007 Artégo
	 **/


 	include_spip('inc/presentation');
	include_spip('inc/barre');
	include_spip('inc/documents');
	include_spip('inc/rubriques');
	include_spip('formulaires_fonctions');
	include_spip('inc/headers');


	/**
	 * exec_formulaires_edit
	 *
	 * Page d'édition d'un formulaire
	 *
	 * @author Pierre Basson
	 **/
	function exec_formulaires_edit() {
	 	
		if ($GLOBALS['connect_statut'] != "0minirezo") {
			echo _T('avis_non_acces_page');
			echo fin_page();
			exit;
		}

		$id_formulaire = intval($_GET['id_formulaire']);
		
		if (!empty($_POST['enregistrer'])) {
			$formulaire = new formulaire($id_formulaire);

			$formulaire->titre 				= addslashes($_POST['titre']);
			$formulaire->id_rubrique		= $_POST['id_parent'];
			$formulaire->descriptif			= addslashes($_POST['descriptif']);
			$formulaire->chapo				= addslashes($_POST['chapo']);
			$formulaire->type				= $_POST['type'];
			$formulaire->limiter_temps		= $_POST['limiter_temps'];
			$formulaire->limiter_invitation	= $_POST['limiter_invitation'];
			if ($formulaire->limiter_invitation	== 'oui') {
				$formulaire->limiter_applicant	= 'oui';
				$formulaire->notifier_applicant	= 'non';
			} else {
				$formulaire->limiter_applicant	= $_POST['limiter_applicant'];
				$formulaire->notifier_applicant	= $_POST['notifier_applicant'];
			}
			$formulaire->notifier_auteurs	= $_POST['notifier_auteurs'];
			$formulaire->texte				= addslashes($_POST['texte']);
			$formulaire->ps					= addslashes($_POST['ps']);

			$formulaire->enregistrer();
			
			$url = generer_url_ecrire('formulaires', 'id_formulaire='.$formulaire->id_formulaire, true);
			header('Location: ' . $url);
			exit();
		}

		pipeline('exec_init',array('args'=>array('exec'=>'formulaires_edit','id_formulaire'=>$id_formulaire),'data'=>''));

		if (!empty($_GET['id_formulaire'])) {
			$formulaire = new formulaire($_GET['id_formulaire']);
		} else {
			$new		= true;
			$formulaire = new formulaire();
			$onfocus	= " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
		}

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('formulairesprive:formulaires'), "naviguer", "formulaires_tous");

	 	debut_gauche();
		if (!$new) {
			echo afficher_documents_colonne($id_formulaire, "formulaire");
		}
		
		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'formulaires_edit','id_formulaire'=>$formulaire->id_formulaire),'data'=>''));

		creer_colonne_droite();
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'formulaires_edit','id_formulaire'=>$formulaire->id_formulaire),'data'=>''));

	 	debut_droite();
		echo "<br />";
		debut_cadre_formulaire();
		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		echo "<tr width='100%'>";
		echo "<td>";
		if ($new)
			icone(_T('icone_retour'), generer_url_ecrire("formulaires_tous"), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/formulaire-24.png', "rien.gif");
		else
			icone(_T('icone_retour'), generer_url_ecrire("formulaires", "id_formulaire=".$formulaire->id_formulaire), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/formulaire-24.png', "rien.gif");

		echo "</td>";
		echo "<td>". http_img_pack('rien.gif', " ", "width='10'") . "</td>\n";
		echo "<td width='100%'>";
		echo _T('formulairesprive:editer_formulaire');
		gros_titre($formulaire->titre);
		echo "</td></tr></table>";

		echo "<P><HR></P>";

		echo generer_url_post_ecrire("formulaires_edit", ($formulaire->id_formulaire ? "id_formulaire=".$formulaire->id_formulaire : ""), 'formulaire');

		echo "<P><B>"._T('formulairesprive:titre')."</B>";
		echo "<BR><INPUT TYPE='text' NAME='titre' style='font-weight: bold; font-size: 13px;' CLASS='formo' VALUE=\"".$formulaire->titre."\" SIZE='40' $onfocus><P><BR>";

		$selecteur_rubrique = charger_fonction('chercher_rubrique', 'inc');
		debut_cadre_couleur("rubrique-24.gif", false, "", _T('titre_cadre_interieur_rubrique'));
		echo $selecteur_rubrique($formulaire->id_rubrique, 'formulaire', true);
		fin_cadre_couleur();

		echo "<P><B>"._T('formulairesprive:descriptif')."</B>";
		echo "<TEXTAREA NAME='descriptif' CLASS='forml' ROWS='3' COLS='40' wrap=soft>";
		echo $formulaire->descriptif;
		echo "</TEXTAREA></P>\n";

		echo '<div style="float: left; clear: both; margin-top: 5px;">'._T('formulairesprive:type_formulaire')."</div>";
		echo '<div style="float: right; margin-top: 5px;">';
		echo "<select name='type' class='fondl'>";
		echo '<option value="une_seule_page" '; if ($formulaire->type == 'une_seule_page') echo 'selected'; echo '>'._T('formulairesprive:une_seule_page').'</option>';
		echo '<option value="plusieurs_pages" '; if ($formulaire->type == 'plusieurs_pages') echo 'selected'; echo '>'._T('formulairesprive:plusieurs_pages').'</option>';
		echo "</select>\n";
		echo '</div>';

		echo '<div style="float: left; clear: both; margin-top: 5px;">'._T('formulairesprive:limiter_temps')."</div>";
		echo '<div style="float: right; margin-top: 5px;">';
		echo "<select name='limiter_temps' class='fondl'>";
		echo '<option value="oui" '; if ($formulaire->limiter_temps == 'oui') echo 'selected'; echo '>'._T('formulairesprive:oui').'</option>';
		echo '<option value="non" '; if ($formulaire->limiter_temps == 'non') echo 'selected'; echo '>'._T('formulairesprive:non').'</option>';
		echo "</select>\n";
		echo '</div>';

		echo '<div style="float: left; clear: both; margin-top: 5px;">'._T('formulairesprive:limiter_invitation')."</div>";
		echo '<div style="float: right; margin-top: 5px;">';
		echo "<select name='limiter_invitation' class='fondl' onchange='toggle_limiter_invitation(this)'>";
		echo '<option value="oui" '; if ($formulaire->limiter_invitation == 'oui') echo 'selected'; echo '>'._T('formulairesprive:oui').'</option>';
		echo '<option value="non" '; if ($formulaire->limiter_invitation == 'non') echo 'selected'; echo '>'._T('formulairesprive:non').'</option>';
		echo "</select>\n";
		echo '</div>';
		
		echo '<script language="javascript">'."\n";
		echo 'function toggle_limiter_invitation(valeur) {'."\n";
		echo '	if (valeur.value == \'oui\')'."\n";
		echo '		document.getElementById(\'limiter_invitation\').style.display = \'none\';'."\n";
		echo '	else'."\n";
		echo '		document.getElementById(\'limiter_invitation\').style.display = \'block\';'."\n";
		echo '}'."\n";
		echo '</script>'."\n";

		if ($formulaire->limiter_invitation == 'oui')
			$style = 'display: none;';
		else
			$style = 'display: block;';
		echo '<div id="limiter_invitation" style="'.$style.'">';

		echo '<div style="float: left; clear: both; margin-top: 5px;">'._T('formulairesprive:limiter_applicant')."</div>";
		echo '<div style="float: right; margin-top: 5px;">';
		echo "<select name='limiter_applicant' class='fondl'>";
		echo '<option value="oui" '; if ($formulaire->limiter_applicant == 'oui') echo 'selected'; echo '>'._T('formulairesprive:oui').'</option>';
		echo '<option value="non" '; if ($formulaire->limiter_applicant == 'non') echo 'selected'; echo '>'._T('formulairesprive:non').'</option>';
		echo "</select>\n";
		echo '</div>';
		echo '<div style="clear: both;">';
		echo "<em>"._T('formulairesprive:limiter_applicant_note')."</em><br /><br />";
		echo '</div>';
/*
		echo '<div style="float: left; clear: both; margin-top: 5px;">'._T('formulairesprive:notifier_applicant')."</div>";
		echo '<div style="float: right; margin-top: 5px;">';
		echo "<select name='notifier_applicant' class='fondl'>";
		echo '<option value="oui" '; if ($formulaire->notifier_applicant == 'oui') echo 'selected'; echo '>'._T('formulairesprive:oui').'</option>';
		echo '<option value="non" '; if ($formulaire->notifier_applicant == 'non') echo 'selected'; echo '>'._T('formulairesprive:non').'</option>';
		echo "</select>\n";
		echo '</div>';
*/

		echo '<input type="hidden" name="notifier_applicant" value="non" />';

		echo '</div>';

		echo '<div style="float: left; clear: both; margin-top: 5px;">'._T('formulairesprive:notifier_auteurs')."</div>";
		echo '<div style="float: right; margin-top: 5px;">';
		echo "<select name='notifier_auteurs' class='fondl'>";
		echo '<option value="oui" '; if ($formulaire->notifier_auteurs == 'oui') echo 'selected'; echo '>'._T('formulairesprive:oui').'</option>';
		echo '<option value="non" '; if ($formulaire->notifier_auteurs == 'non') echo 'selected'; echo '>'._T('formulairesprive:non').'</option>';
		echo "</select>\n";
		echo '</div>';
		
		echo "<p style='clear: both;'><B>"._T('formulairesprive:chapo')."</B>";
		echo "<TEXTAREA NAME='chapo' CLASS='forml' ROWS='5' COLS='40' wrap=soft>";
		echo $formulaire->chapo;
		echo "</TEXTAREA></P>\n";

		echo "<p style='clear: both;'><B>"._T('formulairesprive:texte')."</B>";
		echo "<br>"._T('texte_enrichir_mise_a_jour');
		echo aide("raccourcis");
		echo afficher_barre('document.formulaire.texte');
		echo "<TEXTAREA id='text_area' NAME='texte' ".$GLOBALS['browser_caret']." CLASS='formo' ROWS='20' COLS='40' wrap=soft>";
		echo $formulaire->texte;
		echo "</TEXTAREA></p>\n";

		echo "<p><b>" . _T('info_post_scriptum') ."</b><br />" . "<textarea name='ps' class='forml' rows='5' cols='40' wrap=soft>" . $formulaire->ps . "</textarea></p>\n";

		echo "<DIV ALIGN='right'>";
		echo "<INPUT CLASS='fondo' TYPE='submit' NAME='enregistrer' VALUE='"._T('formulairesprive:enregistrer')."'>";
		echo "</DIV></FORM>";	 	
	 		 	
	 	fin_cadre_formulaire();
	 	
		echo fin_gauche();

		echo fin_page();
	 	
	}
	
	
	
?>