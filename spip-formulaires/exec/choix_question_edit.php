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


	function exec_choix_question_edit() {
	 	
	 	$id_formulaire		= intval($_REQUEST['id_formulaire']);
	 	if (!autoriser('editer', 'formulaires', $id_formulaire)) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		if (function_exists('calculer_url_lettre'))
			$spip_lettres_actif = true;
		else
			$spip_lettres_actif = false;

		$id_bloc			= intval($_REQUEST['id_bloc']);
		$id_question		= intval($_REQUEST['id_question']);
		$id_choix_question	= intval($_REQUEST['id_choix_question']);
		
		if (!empty($_POST['enregistrer'])) {
			$choix_question = new choix_question($id_formulaire, $id_bloc, $id_question, $id_choix_question);

			$choix_question->titre = $_POST['titre'];

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
			
			$url = generer_url_ecrire('formulaires', 'id_formulaire='.$id_formulaire, true);
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

		pipeline('exec_init', array('args' => array('exec' => 'choix_question_edit', 'id_formulaire' => $choix_question->question->bloc->formulaire->id_formulaire, 'id_bloc' => $choix_question->question->bloc->id_bloc, 'id_question' => $choix_question->question->id_question, 'id_choix_question' => $choix_question->id_choix_question), 'data' => ''));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('formulairesprive:formulaires'), "naviguer", "formulaires_tous");

		echo debut_gauche("",true);

		echo pipeline('affiche_gauche', array('args' => array('exec' => 'choix_question_edit', 'id_formulaire' => $choix_question->question->bloc->formulaire->id_formulaire, 'id_bloc' => $choix_question->question->bloc->id_bloc, 'id_question' => $choix_question->question->id_question, 'id_choix_question' => $choix_question->id_choix_question), 'data' => ''));

		echo creer_colonne_droite("",true);
		echo pipeline('affiche_droite', array('args' => array('exec' => 'choix_question_edit', 'id_formulaire' => $choix_question->question->bloc->formulaire->id_formulaire, 'id_bloc' => $choix_question->question->bloc->id_bloc, 'id_question' => $choix_question->question->id_question, 'id_choix_question' => $choix_question->id_choix_question), 'data' => ''));
		echo debut_droite("",true);


		echo '<div class="cadre-formulaire-editer">';
		echo '<div class="entete-formulaire">';
		echo icone_inline(_T('icone_retour'), generer_url_ecrire('formulaires', 'id_formulaire='.$choix_question->question->bloc->formulaire->id_formulaire), _DIR_PLUGIN_FORMULAIRES.'/prive/images/formulaire-24.png', "rien.gif", $GLOBALS['spip_lang_left']);
		echo _T('formulairesprive:edition');
		echo '<h1>'.$choix_question->titre.'</h1>';
		echo '</div>';

		echo '<div class="formulaire_spip formulaire_editer">';
		echo '<form method="post" action="'.generer_url_ecrire('choix_question_edit', "id_formulaire=".$choix_question->question->bloc->formulaire->id_formulaire."&id_bloc=".$choix_question->question->bloc->id_bloc.'&id_choix_question='.$choix_question->id_choix_question).'">';
		echo '<div>';

	  	echo '<ul>';

	    echo '<li class="obligatoire">';
		echo '<label for="titre">'._T('formulairesprive:titre').'</label>';
		echo '<input type="text" class="text" name="titre" id="titre" value="'.$choix_question->titre.'" '.($choix_question->id_choix_question == -1 ? 'onfocus="if(!antifocus){this.value=\'\';antifocus=true;}" ' : '').'/>';
		echo '</li>';

	    echo '<li class="obligatoire">';
		echo '<label for="select-1">'._T('formulairesprive:question_parente').'</label>';
		echo '<select name="id_question" id="select-1" class="fondl">';		
		$questions = $choix_question->question->bloc->recuperer_questions(true);
		foreach ($questions as $id_question) {
			$question = new question($choix_question->question->bloc->formulaire->id_formulaire, $choix_question->question->bloc->id_bloc, $id_question);
			echo '<option value="'.$question->id_question.'" ';
			if ($choix_question->question->id_question == $question->id_question) echo 'selected';
			echo '>'.typo($question->titre).'</option>';
		}
		echo '</select>';
		echo '</li>';

	    echo '<li class="obligatoire">';
		echo '<label for="select-2">'._T('formulairesprive:position').'</label>';
		echo '<select name="position" id="select-2" class="fondl">';		
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
			echo '>'._T('formulairesprive:apres').'&nbsp;'.typo($autre_choix_question->titre).'</option>';
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
				echo '<option class="'.$question->id_question.'" value="'.$j.'">'._T('formulairesprive:apres').'&nbsp;'.typo($autre_choix_question->titre).'</option>';
				$j++;
			}
		}
		echo '</select>';
		echo '</li>';

		if ($spip_lettres_actif) {
			if ($choix_question->question->type == 'abonnements')
				$style = "display: block;";
			else
				$style = "display: none;";
			$themes = sql_select('*', 'spip_themes');
			if (sql_count($themes) > 0) {
			    echo '<li id="abonnements" class="obligatoire" style="'.$style.'">';
				echo '<label for="id_rubrique">'._T('formulairesprive:choisissez_un_abonnement').'</label>';
				echo '<select name="id_rubrique" id="id_rubrique" class="fondl">';		
				while ($arr = sql_fetch($themes)) {
					echo '<option value="'.$arr['id_rubrique'].'"';
					if ($choix_question->id_rubrique == $arr['id_rubrique']) echo ' selected="selected"';
					echo '>'.$arr['titre'].'</option>';
				}
				echo '</select>';
				echo '</li>';
			}
		}

		if ($choix_question->question->type == 'auteurs')
			$style = "display: block;";
		else
			$style = "display: none;";
		$auteurs = sql_select('A.*', 'spip_auteurs AS A INNER JOIN spip_auteurs_formulaires AS AF ON AF.id_auteur=A.id_auteur', 'A.email!="" AND AF.id_formulaire='.intval($choix_question->question->bloc->formulaire->id_formulaire), '', 'A.email');
		if (sql_count($auteurs) > 0) {
		    echo '<li id="auteurs" class="obligatoire" style="'.$style.'">';
			echo '<label for="id_auteur">'._T('formulairesprive:choisissez_un_auteur').'</label>';
			echo '<select name="id_auteur" id="id_auteur" class="fondl">';		
			while ($arr = sql_fetch($auteurs)) {
				echo '<option value="'.$arr['id_auteur'].'"';
				if ($choix_question->id_auteur == $arr['id_auteur']) echo ' selected="selected"';
				echo '>'.$arr['email'].'</option>';
			}
			echo '</select>';
			echo '</li>';
		}

		echo '</ul>';

	  	echo '<p class="boutons"><input type="submit" class="submit" name="enregistrer" value="'._T('formulairesprive:enregistrer').'" /></p>';

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
		if ($spip_lettres_actif)
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

		if ($spip_lettres_actif) {
			echo '<script language="javascript">'."\n";
			echo 'function toggle_abonnements(valeur) {'."\n";
			$questions_de_type_abonnements = $choix_question->question->bloc->recuperer_questions_de_type_abonnements();
			foreach ($questions_de_type_abonnements as $id_question) {
				echo '	if (valeur == '.$id_question.') $("#abonnements").css("display","block");'."\n";
			}
			$questions_de_type_autres = $choix_question->question->bloc->recuperer_questions_de_type_abonnements(true);
			foreach ($questions_de_type_autres as $id_question) {
				echo '	if (valeur == '.$id_question.') $("#abonnements").css("display","none");'."\n";
			}
			echo '}'."\n";
			echo '</script>'."\n";
		}

		echo '<script language="javascript">'."\n";
		echo 'function toggle_auteurs(valeur) {'."\n";
		$questions_de_type_auteurs = $choix_question->question->bloc->recuperer_questions_de_type_auteurs();
		foreach ($questions_de_type_auteurs as $id_question) {
			echo '	if (valeur == '.$id_question.') $("#auteurs").css("display","block");'."\n";
		}
		$questions_de_type_autres = $choix_question->question->bloc->recuperer_questions_de_type_auteurs(true);
		foreach ($questions_de_type_autres as $id_question) {
			echo '	if (valeur == '.$id_question.') $("#auteurs").css("display","none");'."\n";
		}
		echo '}'."\n";
		echo '</script>'."\n";

		echo '</div>';

		echo '</form>';

		echo '</div>';
		echo '</div>';
	 	
		echo fin_gauche();

		echo fin_page();

	}
	

?>