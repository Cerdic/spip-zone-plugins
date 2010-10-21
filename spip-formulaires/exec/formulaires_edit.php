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
	include_spip('inc/documents');
	include_spip('formulaires_fonctions');


	function exec_formulaires_edit() {

		$id_formulaire	= $_GET['id_formulaire'];
		$id_rubrique	= $_GET['id_rubrique'];
		$opt['id_rubrique'] = ($id_rubrique==NULL) ? $_POST['id_parent'] : $id_rubrique;
		if (!autoriser('editer', 'formulaires', $id_formulaire, NULL, $opt)) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		pipeline('exec_init', array('args' => array('exec' => 'formulaires_edit', 'id_formulaire' => $id_formulaire), 'data' => ''));

		if (!empty($_POST['enregistrer'])) {
			$formulaire = new formulaire($id_formulaire);

			$formulaire->titre = $_POST['titre'];
			$formulaire->id_rubrique = $_POST['id_parent'];
			if ($GLOBALS['meta']['spip_formulaires_utiliser_descriptif'] == 'oui')
				$formulaire->descriptif = $_POST['descriptif'];
			if ($GLOBALS['meta']['spip_formulaires_utiliser_chapo'] == 'oui')
				$formulaire->chapo = $_POST['chapo'];
			$formulaire->type = $_POST['type'];
			$formulaire->limiter_invitation	= $_POST['limiter_invitation'];
			if ($formulaire->limiter_invitation	== 'oui') {
				$formulaire->limiter_applicant = 'oui';
			} else {
				$formulaire->limiter_applicant = $_POST['limiter_applicant'];
			}
			$formulaire->notifier_applicant = $_POST['notifier_applicant'];
			$formulaire->notifier_auteurs = $_POST['notifier_auteurs'];
			$formulaire->texte = $_POST['texte'];
			$formulaire->merci = $_POST['merci'];
			if ($GLOBALS['meta']['spip_formulaires_utiliser_ps'] == 'oui')
				$formulaire->ps = $_POST['ps'];

			$formulaire->enregistrer();
			
			$url = generer_url_ecrire('formulaires', 'id_formulaire='.$formulaire->id_formulaire, true);
			header('Location: ' . $url);
			exit();
		}

		pipeline('exec_init',array('args'=>array('exec'=>'formulaires_edit','id_formulaire'=>$id_formulaire),'data'=>''));

		if (!empty($_GET['id_formulaire'])) {
			$formulaire = new formulaire($_GET['id_formulaire']);
		} else {
			$new						= true;
			$formulaire					= new formulaire();
			$formulaire->id_rubrique	= $id_rubrique;
			$onfocus					= " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
		}

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('formulairesprive:formulaires'), "naviguer", "formulaires_tous");

		echo debut_grand_cadre(true);
		echo afficher_hierarchie($formulaire->id_rubrique);
		echo fin_grand_cadre(true);

		echo debut_gauche("",true);

		if ($formulaire->existe){
			echo afficher_documents_colonne($formulaire->id_formulaire, 'formulaire');
		} else {
			echo afficher_documents_colonne(0-$GLOBALS['visiteur_session']['id_auteur'], 'formulaire');
		}
		
		echo pipeline('affiche_gauche',array('args' => array('exec' => 'formulaires_edit', 'id_formulaire' => $formulaire->id_formulaire), 'data' => ''));

		echo creer_colonne_droite("",true);
		echo debut_droite("",true);
		echo pipeline('affiche_droite',array('args' => array('exec' => 'formulaires_edit', 'id_formulaire' => $formulaire->id_formulaire), 'data' => ''));


		echo '<div class="cadre-formulaire-editer">';
		echo '<div class="entete-formulaire">';
		if ($formulaire->existe) {
			echo icone_inline(_T('icone_retour'), generer_url_ecrire('formulaires', 'id_formulaire='.$formulaire->id_formulaire), _DIR_PLUGIN_FORMULAIRES.'/prive/images/formulaire-24.png', "rien.gif", $GLOBALS['spip_lang_left']);
		} else {
			if ($formulaire->id_rubrique)
				echo icone_inline(_T('icone_retour'), generer_url_ecrire('naviguer', 'id_rubrique='.$formulaire->id_rubrique), _DIR_PLUGIN_FORMULAIRES.'/prive/images/rubrique-24.png', "rien.gif", $GLOBALS['spip_lang_left']);
			else
				echo icone_inline(_T('icone_retour'), generer_url_ecrire('formulaires_tous'), _DIR_PLUGIN_FORMULAIRES.'/prive/images/formulaire-24.png', "rien.gif", $GLOBALS['spip_lang_left']);
		}
		echo _T('formulairesprive:edition');
		echo '<h1>'.$formulaire->titre.'</h1>';
		echo '</div>';

		echo '<div class="formulaire_spip formulaire_editer">';
		echo '<form method="post" action="'.generer_url_ecrire('formulaires_edit', ($formulaire->id_formulaire ? 'id_formulaire='.$formulaire->id_formulaire : '')).'">';
		echo '<div>';

		echo '<script language="javascript">'."\n";
		echo 'function toggle_dummy() {'."\n";
		echo '	if ($("#limiter_invitation").val() == "oui")'."\n";
		echo '		$(".limiter_applicant").css("display","none");'."\n";
		echo '	else'."\n";
		echo '		$(".limiter_applicant").css("display","block");'."\n";
		echo '}'."\n";
		echo '</script>'."\n";

	  	echo '<ul>';

	    echo '<li class="obligatoire">';
		echo '<label for="titre">'._T('formulairesprive:titre').'</label>';
		echo '<input type="text" class="text" name="titre" id="titre" value="'.$formulaire->titre.'" '.($formulaire->id_formulaire == -1 ? 'onfocus="if(!antifocus){this.value=\'\';antifocus=true;}" ' : '').'/>';
		echo '</li>';

	    echo '<li class="editer_parent">';
		echo '<label for="id_parent">'._T('titre_cadre_interieur_rubrique').'</label>';
		$selecteur_rubrique = charger_fonction('chercher_rubrique', 'inc');
		echo $selecteur_rubrique($formulaire->id_rubrique, 'formulaire', false);
		echo '</li>';
	
	    echo '<li class="obligatoire">';
		echo '<label for="type">'._T('formulairesprive:type_formulaire').'</label>';
		echo '<select name="type" id="type" class="fondl">';		
		echo '<option value="une_seule_page"'.(($formulaire->type == 'une_seule_page') ? ' selected="selected"' : '' ).'>'._T('formulairesprive:une_seule_page').'</option>';
		echo '<option value="plusieurs_pages"'.(($formulaire->type == 'plusieurs_pages') ? ' selected="selected"' : '' ).'>'._T('formulairesprive:plusieurs_pages').'</option>';
		echo '</select>';
		echo '</li>';

	    echo '<li class="obligatoire">';
		echo '<label for="notifier_auteurs">'._T('formulairesprive:notifier_auteurs').'</label>';
		echo '<select name="notifier_auteurs" id="notifier_auteurs" class="fondl">';		
		echo '<option value="non"'.(($formulaire->notifier_auteurs == 'non') ? ' selected="selected"' : '' ).'>'._T('formulairesprive:non').'</option>';
		echo '<option value="oui"'.(($formulaire->notifier_auteurs == 'oui') ? ' selected="selected"' : '' ).'>'._T('formulairesprive:oui').'</option>';
		echo '</select>';
		echo '</li>';
		
		echo '<li class="obligatoire">';
		echo '<label for="notifier_applicant">'._T('formulairesprive:notifier_applicant').'</label>';
		echo '<select name="notifier_applicant" id="notifier_applicant" class="fondl">';		
		echo '<option value="non"'.(($formulaire->notifier_applicant == 'non') ? ' selected="selected"' : '' ).'>'._T('formulairesprive:non').'</option>';
		echo '<option value="oui"'.(($formulaire->notifier_applicant == 'oui') ? ' selected="selected"' : '' ).'>'._T('formulairesprive:oui').'</option>';
		echo '</select>';
		echo '</li>';

	    echo '<li class="obligatoire">';
		echo '<label for="limiter_invitation">'._T('formulairesprive:limiter_invitation').'</label>';
		echo '<select name="limiter_invitation" id="limiter_invitation" class="fondl" onchange="toggle_dummy()">';		
		echo '<option value="non"'.(($formulaire->limiter_invitation == 'non') ? ' selected="selected"' : '' ).'>'._T('formulairesprive:non').'</option>';
		echo '<option value="oui"'.(($formulaire->limiter_invitation == 'oui') ? ' selected="selected"' : '' ).'>'._T('formulairesprive:oui').'</option>';
		echo '</select>';
		echo '</li>';

		if ($formulaire->limiter_invitation == 'oui')
			$style = 'display: none;';
		else
			$style = 'display: block;';
	    echo '<li class="obligatoire limiter_applicant" style="'.$style.'">';
		echo '<label for="limiter_applicant">'._T('formulairesprive:limiter_applicant').'</label>';
		echo '<select name="limiter_applicant" id="limiter_applicant" class="fondl">';		
		echo '<option value="non"'.(($formulaire->limiter_applicant == 'non') ? ' selected="selected"' : '' ).'>'._T('formulairesprive:non').'</option>';
		echo '<option value="oui"'.(($formulaire->limiter_applicant == 'oui') ? ' selected="selected"' : '' ).'>'._T('formulairesprive:oui').'</option>';
		echo '</select>';
		echo '</li>';

		if ($GLOBALS['meta']['spip_formulaires_utiliser_descriptif'] == 'oui') {
		    echo '<li class="editer_descriptif">';
			echo '<label for="descriptif">'._T('formulairesprive:descriptif').'</label>';
			echo '<textarea name="descriptif" id="descriptif" rows="2" cols="40">'.$formulaire->descriptif.'</textarea>';
			echo '</li>';
		}

		if ($GLOBALS['meta']['spip_formulaires_utiliser_chapo'] == 'oui') {
		    echo '<li class="editer_chapo">';
			echo '<label for="chapo">'._T('formulairesprive:chapo').'</label>';
			echo '<textarea name="chapo" id="chapo" rows="8" cols="40">'.$formulaire->chapo.'</textarea>';
			echo '</li>';
		}

	    echo '<li class="editer_texte">';
		echo '<label for="text_area">'._T('formulairesprive:texte').'</label>';
		echo '<div class="explication">'._T('texte_enrichir_mise_a_jour').'<em>'.aide('raccourcis').'</em></div>';
		echo '<textarea name="texte" id="text_area" rows="20" cols="40" class="barre_inserer" '.$GLOBALS['browser_caret'].'>'.$formulaire->texte.'</textarea>';
		echo '</li>';

	    echo '<li class="editer_texte">';
		echo '<label for="merci">'._T('formulairesprive:message_apres_validation').'</label>';
		echo '<textarea name="merci" id="text_area" rows="10" cols="40">'.$formulaire->merci.'</textarea>';
		echo '</li>';

		if ($GLOBALS['meta']['spip_formulaires_utiliser_ps'] == 'oui') {
		    echo '<li class="editer_ps">';
			echo '<label for="ps">'._T('formulairesprive:ps').'</label>';
			echo '<textarea name="ps" id="ps" rows="3" cols="40">'.$formulaire->ps.'</textarea>';
			echo '</li>';
		}

		echo '</ul>';

	  	echo '<p class="boutons"><input type="submit" class="submit" name="enregistrer" value="'._T('formulairesprive:enregistrer').'" /></p>';

		echo '</div>';

		echo '</form>';

		echo '</div>';
		echo '</div>';
	 	
		echo fin_gauche();

		echo fin_page();

	}

?>