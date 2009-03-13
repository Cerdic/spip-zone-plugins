<?php


	/**
	 * SPIP-Formulaires
	 *
	 * @copyright 2006-2007 Artégo
	 **/


 	include_spip('inc/presentation');
	include_spip('inc/config');
	include_spip('formulaires_fonctions');
	include_spip('inc/headers');


	/**
	 * exec_questions_edit
	 *
	 * Page d'édition d'une question
	 *
	 * @author Pierre Basson
	 **/
	function exec_questions_edit() {
	 	
		if ($GLOBALS['connect_statut'] != "0minirezo") {
			echo _T('avis_non_acces_page');
			echo fin_page();
			exit;
		}

		$id_formulaire	= intval($_GET['id_formulaire']);
		$id_bloc		= intval($_REQUEST['id_bloc']);
		$id_question	= intval($_GET['id_question']);
		
		if (!empty($_POST['enregistrer'])) {
			$question = new question($id_formulaire, $id_bloc, $id_question);

			$question->titre 		= addslashes($_POST['titre']);
			$question->descriptif	= addslashes($_POST['descriptif']);
			$question->type			= $_POST['type'];
			if ($question->type == 'email_applicant' or $question->type == 'auteurs')
				$question->obligatoire = 1;
			else
				$question->obligatoire = intval($_POST['obligatoire']);
			$question->controle		= $_POST['controle'];

			$question->enregistrer();
			$question->changer_ordre($_POST['position']);
			
			$url = generer_url_ecrire('formulaires', 'id_formulaire='.$question->bloc->formulaire->id_formulaire, true);
			header('Location: ' . $url);
			exit();
		}

		if ($id_formulaire AND $id_bloc AND $id_question) {
			$question = new question($id_formulaire, $id_bloc, $id_question);
		} else {
			$new		= true;
			$question	= new question($id_formulaire, $id_bloc);
			$onfocus	= " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
		}

		pipeline('exec_init',array('args'=>array('exec'=>'questions_edit','id_bloc'=>$question->bloc->id_bloc,'id_question'=>$question->id_question),'data'=>''));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('formulairesprive:formulaires'), "naviguer", "formulaires_tous");

	 	debut_gauche();

	 	debut_droite();
		echo "<br />";
		debut_cadre_formulaire();
		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		echo "<tr width='100%'>";
		echo "<td>";
		icone(_T('icone_retour'), generer_url_ecrire("formulaires", "id_formulaire=".$question->bloc->formulaire->id_formulaire), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/question.png', "rien.gif");

		echo "</td>";
		echo "<td>". http_img_pack('rien.gif', " ", "width='10'") . "</td>\n";
		echo "<td width='100%'>";
		echo _T('formulairesprive:editer_question');
		gros_titre($question->titre);
		echo "</td></tr></table>";

		echo "<P><HR></P>";

		echo generer_url_post_ecrire("questions_edit", "id_formulaire=".$question->bloc->formulaire->id_formulaire."&id_question=".$question->id_question, 'formulaire');

		echo "<P><B>"._T('formulairesprive:titre')."</B>";
		echo "<BR><INPUT TYPE='text' NAME='titre' style='font-weight: bold; font-size: 13px;' CLASS='formo' VALUE=\"".$question->titre."\" SIZE='40' $onfocus>";

		echo "<P><B>"._T('formulairesprive:bloc_parent')."</B><br />";
		echo '<select id="select-1" name="id_bloc" CLASS="fondl">';
		$blocs = $question->bloc->formulaire->recuperer_blocs();
		foreach ($blocs as $id_bloc) {
			$bloc = new bloc($question->bloc->formulaire->id_formulaire, $id_bloc);
			echo '<option value="'.$bloc->id_bloc.'" ';
			if ($question->bloc->id_bloc == $bloc->id_bloc) echo 'selected';
			echo '>'.propre($bloc->titre).'</option>';
		}
		echo "</select></P>\n";

		echo "<P><B>"._T('formulairesprive:position')."</B><br />";
		echo '<select id="select-2" name="position" CLASS="fondl">';
		$i = 0;
		echo '<option class="'.$question->bloc->id_bloc.'" value="'.$i++.'" ';
		if ($question->ordre == 0) echo 'selected';
		echo '>'._T('formulairesprive:en_premier').'</option>';
		$questions = $question->recuperer_autres_questions();
		foreach ($questions as $indice) {
			$autre_question = new question($question->bloc->formulaire->id_formulaire, $question->bloc->id_bloc, $indice);
			echo '<option class="'.$question->bloc->id_bloc.'" value="'.$i.'" ';
			if ($question->ordre == $i) echo 'selected';
			echo '>'._T('formulairesprive:apres').'&nbsp;'.propre($autre_question->titre).'</option>';
			$i++;
		}
		$autres_blocs = $question->bloc->recuperer_autres_blocs();
		foreach ($autres_blocs as $id_bloc) {
			$i = 0;
			$bloc = new bloc($question->bloc->formulaire->id_formulaire, $id_bloc);
			$questions = $bloc->recuperer_questions();
			echo '<option class="'.$bloc->id_bloc.'" value="'.$i++.'">'._T('formulairesprive:en_premier').'</option>';
			foreach ($questions as $indice) {
				$autre_question = new question($question->bloc->formulaire->id_formulaire, $bloc->id_bloc, $indice);
				echo '<option class="'.$bloc->id_bloc.'" value="'.$i.'">'._T('formulairesprive:apres').'&nbsp;'.propre($autre_question->titre).'</option>';
				$i++;
			}
		}
		echo "</select></P>\n";

		echo '<script language="javascript">'."\n";
		echo 'function dynamicSelect(id1, id2) {'."\n";
		echo '	// Feature test to see if there is enough W3C DOM support'."\n";
		echo '	if (document.getElementById && document.getElementsByTagName) {'."\n";
		echo '		// Obtain references to both select boxes'."\n";
		echo '		var sel1 = document.getElementById(id1);'."\n";
		echo '		var sel2 = document.getElementById(id2);'."\n";
		echo '		// Clone the dynamic select box'."\n";
		echo '		var clone = sel2.cloneNode(true);'."\n";
		echo '		// Obtain references to all cloned options '."\n";
		echo '		var clonedOptions = clone.getElementsByTagName("option");'."\n";
		echo '		// Onload init: call a generic function to display the related options in the dynamic select box'."\n";
		echo '		refreshDynamicSelectOptions(sel1, sel2, clonedOptions);'."\n";
		echo '		// Onchange of the main select box: call a generic function to display the related options in the dynamic select box'."\n";
		echo '		sel1.onchange = function() {'."\n";
		echo '			refreshDynamicSelectOptions(sel1, sel2, clonedOptions);'."\n";
		echo '		};'."\n";
		echo '	}'."\n";
		echo '}'."\n";
		echo 'function refreshDynamicSelectOptions(sel1, sel2, clonedOptions) {'."\n";
		echo '	// Delete all options of the dynamic select box'."\n";
		echo '	while (sel2.options.length) {'."\n";
		echo '		sel2.remove(0);'."\n";
		echo '	}'."\n";
		echo '	// Create regular expression objects for "select" and the value of the selected option of the main select box as class names'."\n";
		echo '	var pattern1 = /( |^)(select)( |$)/;'."\n";
		echo '	var pattern2 = new RegExp("( |^)(" + sel1.options[sel1.selectedIndex].value + ")( |$)");'."\n";
		echo '	// Iterate through all cloned options'."\n";
		echo '	for (var i = 0; i < clonedOptions.length; i++) {'."\n";
		echo '		// If the classname of a cloned option either equals "select" or equals the value of the selected option of the main select box'."\n";
		echo '		if (clonedOptions[i].className.match(pattern1) || clonedOptions[i].className.match(pattern2)) {'."\n";
		echo '			// Clone the option from the hidden option pool and append it to the dynamic select box'."\n";
		echo '			sel2.appendChild(clonedOptions[i].cloneNode(true));'."\n";
		echo '		}'."\n";
		echo '	}'."\n";
		echo '}'."\n";
		echo 'dynamicSelect("select-1", "select-2");'."\n";
		echo '</script>'."\n";

		if ($question->type == 'email_applicant') {

			echo "<P><B>"._T('formulairesprive:type_question')."</B><br />";
			echo bouton_radio("type", "email_applicant", _T('formulairesprive:email_applicant'), true, "");
			echo '<br />';
			echo '<input type="hidden" name="obligatoire" value="1" />';
			echo '<input type="hidden" name="controle" value="email_applicant" />';

		} else {

			echo "<P><B>"._T('formulairesprive:type_question')."</B><br />";
			echo bouton_radio("type", "champ_texte", _T('formulairesprive:champ_texte'), $question->type == 'champ_texte', "changeVisible(this.checked, 'crtl_texte', 'block', 'none');changeVisible(this.checked, 'obli', 'block', 'none');");
			echo '<br />';
			echo bouton_radio("type", "zone_texte", _T('formulairesprive:zone_texte'), $question->type == 'zone_texte', "changeVisible(this.checked, 'crtl_texte', 'block', 'none');changeVisible(this.checked, 'obli', 'block', 'none');");
			echo '<br />';
			echo bouton_radio("type", "boutons_radio", _T('formulairesprive:boutons_radio'), $question->type == 'boutons_radio', "changeVisible(this.checked, 'crtl_texte', 'none', 'none');changeVisible(this.checked, 'obli', 'block', 'none');");
			echo '<br />';
			echo bouton_radio("type", "cases_a_cocher", _T('formulairesprive:cases_a_cocher'), $question->type == 'cases_a_cocher', "changeVisible(this.checked, 'crtl_texte', 'none', 'none');changeVisible(this.checked, 'obli', 'block', 'none');");
			echo '<br />';
			echo bouton_radio("type", "liste", _T('formulairesprive:liste'), $question->type == 'liste', "changeVisible(this.checked, 'crtl_texte', 'none', 'none');changeVisible(this.checked, 'obli', 'block', 'none');");
			echo '<br />';
			echo bouton_radio("type", "liste_multiple", _T('formulairesprive:liste_multiple'), $question->type == 'liste_multiple', "changeVisible(this.checked, 'crtl_texte', 'none', 'none');changeVisible(this.checked, 'obli', 'block', 'none');");
			echo '<br />';
			echo bouton_radio("type", "fichier", _T('formulairesprive:fichier'), $question->type == 'fichier', "changeVisible(this.checked, 'crtl_texte', 'none', 'none');changeVisible(this.checked, 'obli', 'block', 'none');");
			echo '<br />';
			if ($question->bloc->formulaire->limiter_applicant == 'oui' or $question->bloc->formulaire->notifier_applicant == 'oui') {
				echo bouton_radio("type", "abonnements", _T('formulairesprive:abonnements'), $question->type == 'abonnements', "changeVisible(this.checked, 'crtl_texte', 'none', 'none');changeVisible(this.checked, 'obli', 'block', 'none');");
				echo '<br />';
				echo bouton_radio("type", "nom_applicant", _T('formulairesprive:nom_applicant'), $question->type == 'nom_applicant', "changeVisible(this.checked, 'crtl_texte', 'none', 'none');changeVisible(this.checked, 'obli', 'block', 'none');");
				echo '<br />';
			}
			if ($question->bloc->formulaire->notifier_auteurs == 'oui') {
				echo bouton_radio("type", "auteurs", _T('formulairesprive:question_type_auteurs'), $question->type == 'auteurs', "changeVisible(this.checked, 'crtl_texte', 'none', 'none');changeVisible(this.checked, 'obli', 'none', 'none');");
				echo '<br />';
			}
			echo "</P>\n";

			if ($question->type == 'auteurs' OR $question->type == 'email_applicant')
				$style = "display: none;";
			else
				$style = "display: block;";
			echo "<div id='obli' style='$style'>";
			echo "<P><B>"._T('formulairesprive:obligatoire')."</B><br />";
			echo bouton_radio("obligatoire", "0", _T('formulairesprive:non'), !$question->obligatoire, "changeVisible(this.checked, 'crtl', 'none', 'block');");
			echo '<br />';
			echo bouton_radio("obligatoire", "1", _T('formulairesprive:oui'), $question->obligatoire, "changeVisible(this.checked, 'crtl', 'block', 'none');");
			echo "</P>";
			echo '</div>';
			
			if ($question->type == 'champ_texte' OR $question->type == 'zone_texte')
				$style_texte = "display: block;";
			else
				$style_texte = "display: none;";
			if ($question->obligatoire)
				$style = "display: block;";
			else
				$style = "display: none;";
			echo "<div id='crtl' style='$style'>";
			echo "<div id='crtl_texte' style='$style_texte'>";
			echo "<P><B>"._T('formulairesprive:controle')."</B><br />";
			echo "<select name='controle' CLASS='fondl'>";		
			echo '<option value="non_vide" '; if ($question->controle == 'non_vide') echo 'selected'; echo '>'._T('formulairesprive:non_vide').'</option>';
			echo '<option value="email" '; if ($question->controle == 'email') echo 'selected'; echo '>'._T('formulairesprive:email').'</option>';
			echo '<option value="url" '; if ($question->controle == 'url') echo 'selected'; echo '>'._T('formulairesprive:url').'</option>';
			echo '<option value="nombre" '; if ($question->controle == 'nombre') echo 'selected'; echo '>'._T('formulairesprive:nombre').'</option>';
			echo '<option value="date" '; if ($question->controle == 'date') echo 'selected'; echo '>'._T('formulairesprive:controle_date').'</option>';
			echo "</select></P>\n";
			echo "</div>";
			echo "</div>";

		}

		echo "<P><B>"._T('formulairesprive:descriptif')."</B>";
		echo "<TEXTAREA NAME='descriptif' CLASS='forml' ROWS='3' COLS='40' wrap=soft>";
		echo $question->descriptif;
		echo "</TEXTAREA></P>\n";

		echo "<DIV ALIGN='right'>";
		echo "<INPUT CLASS='fondo' TYPE='submit' NAME='enregistrer' VALUE='"._T('formulairesprive:enregistrer')."'>";
		echo "</DIV></FORM>";	 	
	 		 	
	 	fin_cadre_formulaire();
	 	
		echo fin_gauche();

		echo fin_page();

	}
	
	
	
?>