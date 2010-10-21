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


	function exec_blocs_edit() {
	 	
		
		$id_formulaire	= intval($_GET['id_formulaire']);
		if (!autoriser('editer', 'formulaires', $id_formulaire)) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		$id_bloc		= intval($_GET['id_bloc']);
		
		if (!empty($_POST['enregistrer'])) {
			$bloc = new bloc($id_formulaire, $id_bloc);

			$bloc->titre 		= $_POST['titre'];
			$bloc->descriptif	= $_POST['descriptif'];
			$bloc->texte		= $_POST['texte'];

			$bloc->enregistrer();
			$bloc->changer_ordre($_POST['position']);
			
			$url = generer_url_ecrire('formulaires', 'id_formulaire='.$bloc->formulaire->id_formulaire, true);
			header('Location: ' . $url);
			exit();
		}

		if ($id_formulaire AND $id_bloc) {
			$bloc = new bloc($id_formulaire, $id_bloc);
		} else {
			$new		= true;
			$bloc		= new bloc($id_formulaire);
			$onfocus	= " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
		}

		pipeline('exec_init', array('args' => array('exec' => 'blocs_edit', 'id_formulaire' => $bloc->formulaire->id_formulaire, 'id_bloc' => $bloc->id_bloc), 'data' => ''));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('formulairesprive:formulaires'), "naviguer", "formulaires_tous");

		echo debut_gauche("",true);

		echo pipeline('affiche_gauche',array('args' => array('exec' => 'blocs_edit', 'id_formulaire' => $bloc->formulaire->id_formulaire, 'id_bloc' => $bloc->id_bloc), 'data' => ''));

		echo creer_colonne_droite("",true);
		echo debut_droite("",true);
		echo pipeline('affiche_droite',array('args' => array('exec' => 'blocs_edit', 'id_formulaire' => $bloc->formulaire->id_formulaire, 'id_bloc' => $bloc->id_bloc), 'data' => ''));


		echo '<div class="cadre-formulaire-editer">';
		echo '<div class="entete-formulaire">';
		echo icone_inline(_T('icone_retour'), generer_url_ecrire('formulaires', 'id_formulaire='.$bloc->formulaire->id_formulaire), _DIR_PLUGIN_FORMULAIRES.'/prive/images/formulaire-24.png', "rien.gif", $GLOBALS['spip_lang_left']);
		echo _T('formulairesprive:edition');
		echo '<h1>'.$bloc->titre.'</h1>';
		echo '</div>';

		echo '<div class="formulaire_spip formulaire_editer">';
		echo '<form method="post" action="'.generer_url_ecrire('blocs_edit', "id_formulaire=".$bloc->formulaire->id_formulaire."&id_bloc=".$bloc->id_bloc).'">';
		echo '<div>';

	  	echo '<ul>';

	    echo '<li class="obligatoire">';
		echo '<label for="titre">'._T('formulairesprive:titre').'</label>';
		echo '<input type="text" class="text" name="titre" id="titre" value="'.$bloc->titre.'" '.($bloc->id_bloc == -1 ? 'onfocus="if(!antifocus){this.value=\'\';antifocus=true;}" ' : '').'/>';
		echo '</li>';

	    echo '<li class="obligatoire">';
		echo '<label for="position">'._T('formulairesprive:position').'</label>';
		echo '<select name="position" id="position" class="fondl">';		
		$i = 0;
		echo '<option value="'.$i++.'" ';
		if ($bloc->ordre == 0) echo 'selected';
		echo '>'._T('formulairesprive:en_premier').'</option>';
		$blocs = $bloc->recuperer_autres_blocs();
		foreach ($blocs as $indice) {
			$autre_bloc = new bloc($bloc->id_formulaire, $indice);
			echo '<option value="'.$i.'" ';
			if ($bloc->ordre == $i) echo 'selected';
			echo '>'._T('formulairesprive:apres').'&nbsp;'.$autre_bloc->titre.'</option>';
			$i++;
		}
		echo '</select>';
		echo '</li>';

		if ($GLOBALS['meta']['spip_formulaires_utiliser_descriptif'] == 'oui') {
		    echo '<li class="editer_descriptif">';
			echo '<label for="descriptif">'._T('formulairesprive:descriptif').'</label>';
			echo '<textarea name="descriptif" id="descriptif" rows="2" cols="40">'.$bloc->descriptif.'</textarea>';
			echo '</li>';
		}

	    echo '<li class="editer_texte">';
		echo '<label for="text_area">'._T('formulairesprive:texte').'</label>';
		echo '<div class="explication">'._T('texte_enrichir_mise_a_jour').'<em>'.aide('raccourcis').'</em></div>';
		echo '<textarea name="texte" id="text_area" rows="10" cols="40" class="barre_inserer" '.$GLOBALS['browser_caret'].'>'.$bloc->texte.'</textarea>';
		echo '</li>';

		echo '</ul>';

		echo '<input type="hidden" name="notifier_applicant" value="non" />';

	  	echo '<p class="boutons"><input type="submit" class="submit" name="enregistrer" value="'._T('formulairesprive:enregistrer').'" /></p>';

		echo '</div>';

		echo '</form>';

		echo '</div>';
		echo '</div>';
	 	
		echo fin_gauche();

		echo fin_page();

	}
	
	
?>