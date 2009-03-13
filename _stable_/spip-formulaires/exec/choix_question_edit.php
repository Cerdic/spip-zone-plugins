<?php


	/**
	 * SPIP-Formulaires
	 *
	 * @copyright 2006-2007 Artégo
	 **/


 	include_spip('inc/presentation');
	include_spip('formulaires_fonctions');
	include_spip('inc/headers');


	/**
	 * exec_choix_question_edit
	 *
	 * Page d'édition d'un choix de question
	 *
	 * @author Pierre Basson
	 **/
	function exec_choix_question_edit() {
	 	
		if ($GLOBALS['connect_statut'] != "0minirezo") {
			echo _T('avis_non_acces_page');
			echo fin_page();
			exit;
		}

		$id_formulaire		= intval($_GET['id_formulaire']);
		$id_bloc			= intval($_GET['id_bloc']);
		$id_question		= intval($_REQUEST['id_question']);
		$id_choix_question	= intval($_GET['id_choix_question']);
		
		if (!empty($_POST['enregistrer'])) {
			$choix_question = new choix_question($id_formulaire, $id_bloc, $id_question, $id_choix_question);

			$choix_question->titre = addslashes($_POST['titre']);

			if ($choix_question->question->type == 'abonnements')
				$choix_question->id_rubrique = intval($_POST['id_rubrique']);
			else
				$choix_question->id_rubrique = 0;

			if ($choix_question->question->type == 'auteurs')
				$choix_question->id_auteur = intval($_POST['id_auteur']);
			else
				$choix_question->id_auteur = 0;

			$choix_question->enregistrer();
			$choix_question->changer_ordre($_POST['position']);
			
			$url = generer_url_ecrire('formulaires', 'id_formulaire='.$choix_question->question->bloc->formulaire->id_formulaire, true);
			header('Location: ' . $url);
			exit();
		}

		if ($id_formulaire and $id_bloc and $id_question and $id_choix_question) {
			$choix_question = new choix_question($id_formulaire, $id_bloc, $id_question, $id_choix_question);
		} else {
			$new			= true;
			$choix_question = new choix_question($id_formulaire, $id_bloc, $id_question);
			$onfocus		= " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
		}

		pipeline('exec_init',array('args'=>array('exec'=>'choix_question_edit','id_formulaire' => $choix_question->question->bloc->formulaire->id_formulaire, 'id_bloc'=>$choix_question->question->bloc->id_bloc,'id_question'=>$choix_question->question->id_question,'id_choix_question'=>$choix_question->id_choix_question),'data'=>''));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('formulairesprive:formulaires'), "naviguer", "formulaires_tous");

	 	debut_gauche();

	 	debut_droite();
		echo "<br />";
		debut_cadre_formulaire();
		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		echo "<tr width='100%'>";
		echo "<td>";
		icone(_T('icone_retour'), generer_url_ecrire("formulaires", "id_formulaire=".$choix_question->question->bloc->formulaire->id_formulaire), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/question.png', "rien.gif");

		echo "</td>";
		echo "<td>". http_img_pack('rien.gif', " ", "width='10'") . "</td>\n";
		echo "<td width='100%'>";
		echo _T('formulairesprive:editer_choix_question');
		gros_titre($choix_question->titre);
		echo "</td></tr></table>";

		echo "<P><HR></P>";

		echo generer_url_post_ecrire("choix_question_edit", "id_formulaire=".$choix_question->question->bloc->formulaire->id_formulaire.'&id_bloc='.$choix_question->question->bloc->id_bloc."&id_choix_question=".$choix_question->id_choix_question, 'formulaire');

		echo "<P><B>"._T('formulairesprive:titre')."</B>";
		echo "<BR><INPUT TYPE='text' NAME='titre' style='font-weight: bold; font-size: 13px;' CLASS='formo' VALUE=\"".$choix_question->titre."\" SIZE='40' $onfocus>";

		echo "<P><B>"._T('formulairesprive:question_parente')."</B><br />";
		echo '<select id="select-1" name="id_question" class="fondl">';
		$questions = $choix_question->question->bloc->recuperer_questions(true);
		foreach ($questions as $id_question) {
			$question = new question($choix_question->question->bloc->formulaire->id_formulaire, $choix_question->question->bloc->id_bloc, $id_question);
			echo '<option value="'.$question->id_question.'" ';
			if ($choix_question->question->id_question == $question->id_question) echo 'selected';
			echo '>'.propre($question->titre).'</option>';
		}
		echo "</select></P>\n";

		echo "<P><B>"._T('formulairesprive:position')."</B><br />";
		echo '<select id="select-2" name="position" class="fondl">';
		$i = 0;
		echo '<option class="'.$choix_question->question->id_question.'" value="'.$i.'" ';
		if ($choix_question->ordre == 0) echo 'selected';
		echo '>'._T('formulairesprive:en_premier').'</option>';
		$i++;
		$autres_choix_question = $choix_question->recuperer_autres_choix_question();
		foreach ($autres_choix_question as $indice) {
			$autre_choix_question = new choix_question($choix_question->question->bloc->formulaire->id_formulaire, $choix_question->question->bloc->id_bloc, $choix_question->question->id_question, $indice);
			echo '<option class="'.$autre_choix_question->question->id_question.'" value="'.$i.'" ';
			if ($choix_question->ordre == $i) echo 'selected';
			echo '>'._T('formulairesprive:apres').'&nbsp;'.propre($autre_choix_question->titre).'</option>';
			$i++;
		}
		$autres_questions = $choix_question->question->recuperer_autres_questions(true);
		foreach ($autres_questions as $id_question) {
			$j = 0;
			$question = new question($choix_question->question->bloc->formulaire->id_formulaire, $choix_question->question->bloc->id_bloc, $id_question);
			$choix_question_autres_questions = $question->recuperer_choix_question();
			echo '<option class="'.$question->id_question.'" value="'.$j++.'">'._T('formulairesprive:en_premier').'</option>';
			foreach ($choix_question_autres_questions as $indice) {
				$autre_choix_question = new choix_question($choix_question->question->bloc->formulaire->id_formulaire, $choix_question->question->bloc->id_bloc, $question->id_question, $indice);
				echo '<option class="'.$question->id_question.'" value="'.$j.'">'._T('formulairesprive:apres').'&nbsp;'.propre($autre_choix_question->titre).'</option>';
				$j++;
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
		echo '			toggle_abonnements(sel1.value);'."\n";
		echo '			toggle_auteurs(sel1.value);'."\n";
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

		echo '<script language="javascript">'."\n";
		echo 'function toggle_abonnements(valeur) {'."\n";
		$questions_de_type_abonnements = $choix_question->question->bloc->recuperer_questions_de_type_abonnements();
		foreach ($questions_de_type_abonnements as $id_question) {
			echo '	if (valeur == '.$id_question.') document.getElementById("abonnements").style.display = "block";'."\n";
		}
		$questions_de_type_autres = $choix_question->question->bloc->recuperer_questions_de_type_abonnements(true);
		foreach ($questions_de_type_autres as $id_question) {
			echo '	if (valeur == '.$id_question.') document.getElementById("abonnements").style.display = "none";'."\n";
		}
		echo '}'."\n";
		echo '</script>'."\n";

		echo '<script language="javascript">'."\n";
		echo 'function toggle_auteurs(valeur) {'."\n";
		$questions_de_type_auteurs = $choix_question->question->bloc->recuperer_questions_de_type_auteurs();
		foreach ($questions_de_type_auteurs as $id_question) {
			echo '	if (valeur == '.$id_question.') document.getElementById("auteurs").style.display = "block";'."\n";
		}
		$questions_de_type_autres = $choix_question->question->bloc->recuperer_questions_de_type_auteurs(true);
		foreach ($questions_de_type_autres as $id_question) {
			echo '	if (valeur == '.$id_question.') document.getElementById("auteurs").style.display = "none";'."\n";
		}
		echo '}'."\n";
		echo '</script>'."\n";

		if ($choix_question->question->type == 'abonnements')
			$style = "display: block;";
		else
			$style = "display: none;";
		echo "<div id='abonnements' style='$style'>";
		$themes = spip_query('SELECT * FROM spip_themes');
		if (spip_num_rows($themes) > 0) {
			echo "<P><B>"._T('formulairesprive:choisissez_un_abonnement')."</B><br />";
			echo "<select name='id_rubrique' CLASS='fondl'>";
			while ($arr = spip_fetch_array($themes)) {
				echo '<option value="'.$arr['id_rubrique'].'"';
				if ($choix_question->id_rubrique == $arr['id_rubrique']) echo ' selected="selected"';
				echo '>'.$arr['titre'].'</option>';
			}
			echo "</select></P>\n";
		}
		echo "</div>";

		if ($choix_question->question->type == 'auteurs')
			$style = "display: block;";
		else
			$style = "display: none;";
		echo "<div id='auteurs' style='$style'>";
		$auteurs = spip_query('SELECT A.* 
								FROM spip_auteurs AS A 
								INNER JOIN spip_auteurs_formulaires AS AF ON AF.id_auteur=A.id_auteur 
								WHERE A.email!="" 
									AND AF.id_formulaire='.$choix_question->question->bloc->formulaire->id_formulaire.'
								ORDER BY A.nom');
		if (spip_num_rows($auteurs) > 0) {
			echo "<P><B>"._T('formulairesprive:choisissez_un_auteur')."</B><br />";
			echo "<select name='id_auteur' CLASS='fondl'>";
			while ($arr = spip_fetch_array($auteurs)) {
				echo '<option value="'.$arr['id_auteur'].'"';
				if ($choix_question->id_auteur == $arr['id_auteur']) echo ' selected="selected"';
				echo '>'.$arr['nom'].' - '.$arr['email'].'</option>';
			}
			echo "</select></P>\n";
		}
		echo "</div>";

		echo "<DIV ALIGN='right'>";
		echo "<INPUT CLASS='fondo' TYPE='submit' NAME='enregistrer' VALUE='"._T('formulairesprive:enregistrer')."'>";
		echo "</DIV></FORM>";	 	
	 		 	
	 	fin_cadre_formulaire();
	 	
		echo fin_gauche();

		echo fin_page();
	 	
	}
	
	
	
?>