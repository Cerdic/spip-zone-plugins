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


	function exec_questions_edit() {
	 	
		$id_formulaire	= intval($_GET['id_formulaire']);
		if (!autoriser('editer', 'formulaires', $id_formulaire)) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		if (function_exists('calculer_url_lettre'))
			$spip_lettres_actif = true;
		else
			$spip_lettres_actif = false;

		$id_bloc		= intval($_REQUEST['id_bloc']);
		$id_question	= intval($_GET['id_question']);
		
		if (!empty($_POST['enregistrer'])) {
			$question = new question($id_formulaire, $id_bloc, $id_question);

			$question->titre 		= $_POST['titre'];
			$question->descriptif	= $_POST['descriptif'];
			$question->type			= $_POST['type'];
			$question->mime			= serialize($_POST['mime']);
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

		echo debut_gauche("",true);

		echo pipeline('affiche_gauche',array('args' => array('exec' => 'blocs_edit', 'id_formulaire' => $bloc->formulaire->id_formulaire, 'id_bloc' => $bloc->id_bloc), 'data' => ''));

		echo creer_colonne_droite("",true);
		echo debut_droite("",true);
		echo pipeline('affiche_droite',array('args' => array('exec' => 'blocs_edit', 'id_formulaire' => $bloc->formulaire->id_formulaire, 'id_bloc' => $bloc->id_bloc), 'data' => ''));


		echo '<div class="cadre-formulaire-editer">';
		echo '<div class="entete-formulaire">';
		echo icone_inline(_T('icone_retour'), generer_url_ecrire('formulaires', 'id_formulaire='.$question->bloc->formulaire->id_formulaire), _DIR_PLUGIN_FORMULAIRES.'/prive/images/formulaire-24.png', "rien.gif", $GLOBALS['spip_lang_left']);
		echo _T('formulairesprive:edition');
		echo '<h1>'.$question->titre.'</h1>';
		echo '</div>';

		echo '<div class="formulaire_spip formulaire_editer">';
		echo '<form method="post" action="'.generer_url_ecrire('questions_edit', "id_formulaire=".$question->bloc->formulaire->id_formulaire."&id_bloc=".$question->bloc->id_bloc."&id_question=".$question->id_question).'">';
		echo '<div>';

	  	echo '<ul>';

	    echo '<li class="obligatoire">';
		echo '<label for="titre">'._T('formulairesprive:titre').'</label>';
		echo '<input type="text" class="text" name="titre" id="titre" value="'.$question->titre.'" '.($question->id_question == -1 ? 'onfocus="if(!antifocus){this.value=\'\';antifocus=true;}" ' : '').'/>';
		echo '</li>';

	    echo '<li>';
		echo '<label for="select-1">'._T('formulairesprive:bloc_parent').'</label>';
		echo '<select name="id_bloc" id="select-1" class="fondl">';		
		$blocs = $question->bloc->formulaire->recuperer_blocs();
		foreach ($blocs as $id_bloc) {
			$bloc = new bloc($question->bloc->formulaire->id_formulaire, $id_bloc);
			echo '<option value="'.$bloc->id_bloc.'" ';
			if ($question->bloc->id_bloc == $bloc->id_bloc) echo 'selected';
			echo '>'.typo($bloc->titre).'</option>';
		}
		echo '</select>';
		echo '</li>';

	    echo '<li>';
		echo '<label for="select-2">'._T('formulairesprive:position').'</label>';
		echo '<select name="position" id="select-2" class="fondl">';		
		$i = 0;
		echo '<option class="'.$question->bloc->id_bloc.'" value="'.$i++.'" ';
		if ($question->ordre == 0) echo 'selected';
		echo '>'._T('formulairesprive:en_premier').'</option>';
		$questions = $question->recuperer_autres_questions();
		foreach ($questions as $indice) {
			$autre_question = new question($question->bloc->formulaire->id_formulaire, $question->bloc->id_bloc, $indice);
			echo '<option class="'.$question->bloc->id_bloc.'" value="'.$i.'" ';
			if ($question->ordre == $i) echo 'selected';
			echo '>'._T('formulairesprive:apres').'&nbsp;'.typo($autre_question->titre).'</option>';
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
				echo '<option class="'.$bloc->id_bloc.'" value="'.$i.'">'._T('formulairesprive:apres').'&nbsp;'.typo($autre_question->titre).'</option>';
				$i++;
			}
		}
		echo '</select>';
		echo '</li>';

		if ($question->type == 'email_applicant') {
		    echo '<li>';
			echo '<label for="type">'._T('formulairesprive:type_question').'</label>';
			echo '<select name="type" id="type" class="fondl">';		
			echo '<option value="email_applicant">'._T('formulairesprive:email_applicant').'</option>';
			echo '</select>';
			echo '<input type="hidden" name="obligatoire" value="1" />';
			echo '<input type="hidden" name="controle" value="email_applicant" />';
			echo '</li>';
		} else {
		    echo '<li class="obligatoire">';
			echo '<label for="type">'._T('formulairesprive:type_question').'</label>';
			echo '<select name="type" id="type" class="fondl" onchange="toggle_type()">';		
			echo '<option value="champ_texte"'.($question->type == 'champ_texte' ? ' selected="selected"' : '').'>'._T('formulairesprive:champ_texte').'</option>';
			echo '<option value="zone_texte"'.($question->type == 'zone_texte' ? ' selected="selected"' : '').'>'._T('formulairesprive:zone_texte').'</option>';
			echo '<option value="boutons_radio"'.($question->type == 'boutons_radio' ? ' selected="selected"' : '').'>'._T('formulairesprive:boutons_radio').'</option>';
			echo '<option value="cases_a_cocher"'.($question->type == 'cases_a_cocher' ? ' selected="selected"' : '').'>'._T('formulairesprive:cases_a_cocher').'</option>';
			echo '<option value="liste"'.($question->type == 'liste' ? ' selected="selected"' : '').'>'._T('formulairesprive:liste').'</option>';
			echo '<option value="liste_multiple"'.($question->type == 'liste_multiple' ? ' selected="selected"' : '').'>'._T('formulairesprive:liste_multiple').'</option>';
			echo '<option value="fichier"'.($question->type == 'fichier' ? ' selected="selected"' : '').'>'._T('formulairesprive:fichier').'</option>';
			if ($question->bloc->formulaire->limiter_invitation == 'oui') {
				if ($spip_lettres_actif) {
					if (sql_countsel('spip_themes'))
						echo '<option value="abonnements"'.($question->type == 'abonnements' ? ' selected="selected"' : '').'>'._T('formulairesprive:abonnements').'</option>';
				}
				echo '<option value="nom_applicant"'.($question->type == 'nom_applicant' ? ' selected="selected"' : '').'>'._T('formulairesprive:nom_applicant').'</option>';
			}
			if ($question->bloc->formulaire->notifier_auteurs == 'oui') {
				echo '<option value="auteurs"'.($question->type == 'auteurs' ? ' selected="selected"' : '').'>'._T('formulairesprive:auteurs').'</option>';
			}
			echo '</select>';
			echo '</li>';

			if ($question->type == 'auteurs' OR $question->type == 'email_applicant')
				$style = "display: none;";
			else
				$style = "display: block;";
		    echo '<li id="obli" style="'.$style.'">';
			echo '<label for="obligatoire">'._T('formulairesprive:obligatoire').'</label>';
			echo '<select name="obligatoire" id="obligatoire" class="fondl" onchange="toggle_controle_obligatoire()">';		
			echo '<option value="0"'.(!$question->obligatoire ? ' selected="selected"' : '').'>'._T('formulairesprive:non').'</option>';
			echo '<option value="1"'.($question->obligatoire ? ' selected="selected"' : '').'>'._T('formulairesprive:oui').'</option>';
			echo '</select>';
			echo '</li>';

			if ($question->type == 'fichier')
				$style = "display: block;";
			else
				$style = "display: none;";
		    echo '<li id="mimes" style="'.$style.'">';
			echo '<label for="mime">'._T('formulairesprive:types_fichier_autorises').'</label>';
			echo '<select name="mime[]" id="mime" class="fondl" size="20" multiple="multiple">';
			$res = sql_select('*', 'spip_types_documents');
			while ($arr = sql_fetch($res)) {
				echo '<option value="'.$arr['mime_type'].'"'.(in_array($arr['mime_type'], $question->mimes_type) ? ' selected="selected"' : '').'>'.$arr['titre'].' (.'.$arr['extension'].')</option>';
			}
			echo '</select>';
			echo '</li>';

			if ($question->type == 'champ_texte' OR $question->type == 'zone_texte')
				$style_texte = "display: block;";
			else
				$style_texte = "display: none;";
			if ($question->obligatoire)
				$style = "display: block;";
			else
				$style = "display: none;";
		    echo '<li id="crtl" style="'.$style.'">';
			echo '<div id="crtl_texte" style="'.$style_texte.'">';
			echo '<label for="controle">'._T('formulairesprive:controle').'</label>';
			echo '<select name="controle" id="controle" class="fondl">';		
			echo '<option value="non_vide" '; if ($question->controle == 'non_vide') echo 'selected'; echo '>'._T('formulairesprive:non_vide').'</option>';
			echo '<option value="email" '; if ($question->controle == 'email') echo 'selected'; echo '>'._T('formulairesprive:email').'</option>';
			echo '<option value="url" '; if ($question->controle == 'url') echo 'selected'; echo '>'._T('formulairesprive:url').'</option>';
			echo '<option value="nombre" '; if ($question->controle == 'nombre') echo 'selected'; echo '>'._T('formulairesprive:nombre').'</option>';
			echo '<option value="date" '; if ($question->controle == 'date') echo 'selected'; echo '>'._T('formulairesprive:controle_date').'</option>';
			echo '</select>';
			echo '</div>';
			echo '</li>';

		}

		if ($GLOBALS['meta']['spip_formulaires_utiliser_descriptif'] == 'oui') {
		    echo '<li class="editer_descriptif">';
			echo '<label for="descriptif">'._T('formulairesprive:descriptif').'</label>';
			echo '<textarea name="descriptif" id="descriptif" rows="2" cols="40">'.$question->descriptif.'</textarea>';
			echo '</li>';
		}

		echo '</ul>';

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

		echo '<script language="javascript">'."\n";
		echo 'function toggle_controle_obligatoire() {'."\n";
		echo '	if ($("#obligatoire").val() == 1)'."\n";
		echo '		$("#crtl").css("display","block");'."\n";
		echo '	else'."\n";
		echo '		$("#crtl").css("display","none");'."\n";
		echo '}'."\n";
		echo 'function toggle_type() {'."\n";
		echo '	if ($("#type").val() == "fichier")'."\n";
		echo '		$("#mimes").css("display","block");'."\n";
		echo '	else'."\n";
		echo '		$("#mimes").css("display","none");'."\n";
		echo '	if ($("#type").val() == "champ_texte")'."\n";
		echo '		$("#crtl_texte").css("display","block");'."\n";
		echo '	else if ($("#type").val() == "zone_texte")'."\n";
		echo '		$("#crtl_texte").css("display","block");'."\n";
		echo '	else'."\n";
		echo '		$("#crtl_texte").css("display","none");'."\n";
		echo '}'."\n";
		echo '</script>'."\n";

	  	echo '<p class="boutons"><input type="submit" class="submit" name="enregistrer" value="'._T('formulairesprive:enregistrer').'" /></p>';

		echo '</div>';

		echo '</form>';

		echo '</div>';
		echo '</div>';
	 	
		echo fin_gauche();

		echo fin_page();

	}

	
?>