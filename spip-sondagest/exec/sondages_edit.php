<?php


	/**
	 * SPIP-Sondages
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
	include_spip('sondages_fonctions');


	function exec_sondages_edit() {

		if (!autoriser('editer', 'sondages')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		pipeline('exec_init', array('args' => array('exec' => 'sondages_edit', 'id_sondage' => $_GET['id_sondage']), 'data' => ''));

		if (!empty($_POST['enregistrer'])) {
			$sondage = new sondage($_GET['id_sondage']);
			$sondage->titre			= $_POST['titre'];
			$sondage->id_rubrique	= $_POST['id_parent'];
			$sondage->texte			= $_POST['texte'];
			$sondage->enregistrer();
			$url = generer_url_ecrire('sondages', 'id_sondage='.$sondage->id_sondage, true);
			header('Location: ' . $url);
			exit();
		}

		if (!empty($_GET['id_sondage'])) {
			$sondage = new sondage($_GET['id_sondage']);
		} else {
			$id_rubrique = intval($_GET['id_rubrique']);
			if (!$id_rubrique) $id_rubrique = sql_getfetsel('id_rubrique', 'spip_rubriques', 'statut="publie"', 'id_rubrique', '1');
			if (!$id_rubrique) $id_rubrique = sql_getfetsel('id_rubrique', 'spip_rubriques', '', 'id_rubrique', '1');
			$sondage = new sondage();
			$sondage->titre			= _T('sondagesprive:nouveau_sondage');
			$sondage->id_rubrique	= $id_rubrique;
		}
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page($sondage->titre, "naviguer", "sondages_tous");

		echo debut_grand_cadre(true);
		echo afficher_hierarchie($sondage->id_rubrique);
		echo fin_grand_cadre(true);

		echo debut_gauche("",true);

		if ($sondage->existe){
			echo afficher_documents_colonne($sondage->id_sondage, 'sondage');
		} else {
			# ICI GROS HACK
			# -------------
			echo afficher_documents_colonne(0-$GLOBALS['visiteur_session']['id_auteur'], 'sondage');
		}

		echo pipeline('affiche_gauche', array('args' => array('exec' => 'sondages_edit', 'id_sondage' => $sondage->id_sondage), 'data' => ''));
		echo creer_colonne_droite("",true);
		echo pipeline('affiche_droite', array('args' => array('exec' => 'sondages_edit', 'id_sondage' => $sondage->id_sondage), 'data' => ''));
		echo debut_droite("",true);

		echo '<div class="cadre-formulaire-editer">';
		echo '<div class="entete-formulaire">';
		if ($sondage->existe) {
			echo icone_inline(_T('icone_retour'), generer_url_ecrire('sondages', 'id_sondage='.$sondage->id_sondage), _DIR_PLUGIN_SONDAGES.'/prive/images/sondage-24.png', "rien.gif", $GLOBALS['spip_lang_left']);
		} else {
			if ($sondage->id_rubrique)
				echo icone_inline(_T('icone_retour'), generer_url_ecrire('naviguer', 'id_rubrique='.$sondage->id_rubrique), _DIR_PLUGIN_SONDAGES.'/prive/images/rubrique-24.png', "rien.gif", $GLOBALS['spip_lang_left']);
			else
				echo icone_inline(_T('icone_retour'), generer_url_ecrire('sondages_tous'), _DIR_PLUGIN_SONDAGES.'/prive/images/sondage-24.png', "rien.gif", $GLOBALS['spip_lang_left']);
		}
		echo _T('sondagesprive:modifier_sondage');
		echo '<h1>'.$sondage->titre.'</h1>';
		echo '</div>';

		echo '<div class="formulaire_spip formulaire_editer">';
		echo '<form method="post" action="'.generer_url_ecrire('sondages_edit', ($sondage->id_sondage ? 'id_sondage='.$sondage->id_sondage : '')).'">';
		echo '<div>';

	  	echo '<ul>';

	    echo '<li class="obligatoire">';
		echo '<label for="titre">'._T('sondagesprive:titre').'</label>';
		echo '<input type="text" class="text" name="titre" id="titre" value="'.$sondage->titre.'" '.($sondage->id_sondage == -1 ? 'onfocus="if(!antifocus){this.value=\'\';antifocus=true;}" ' : '').'/>';
		echo '</li>';

	    echo '<li class="editer_parent">';
		echo '<label for="id_parent">'._T('titre_cadre_interieur_rubrique').'</label>';
		$selecteur_rubrique = charger_fonction('chercher_rubrique', 'inc');
		echo $selecteur_rubrique($sondage->id_rubrique, 'sondage', false);
		echo '</li>';
	
	    echo '<li class="editer_texte">';
		echo '<label for="text_area">'._T('sondagesprive:texte').'</label>';
		echo '<div class="explication">'._T('texte_enrichir_mise_a_jour').'<em>'.aide('raccourcis').'</em></div>';
		echo '<textarea name="texte" id="text_area" rows="20" cols="40" class="barre_inserer" '.$GLOBALS['browser_caret'].'>'.$sondage->texte.'</textarea>';
		echo '</li>';

		echo '</ul>';

	  	echo '<p class="boutons"><input type="submit" class="submit" name="enregistrer" value="'._T('sondagesprive:enregistrer').'" /></p>';

		echo '</div>';

		echo '</form>';

		echo '</div>';
		echo '</div>';
	 	
		echo fin_gauche();

		echo fin_page();

	}

?>