<?php


	/**
	 * SPIP-Lettres
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
	include_spip('lettres_fonctions');


	function exec_lettres_edit() {

		if (!autoriser('editer', 'lettres')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		pipeline('exec_init', array('args' => array('exec' => 'lettres_edit', 'id_lettre' => $_GET['id_lettre']), 'data' => ''));

		if (!empty($_POST['enregistrer'])) {
			$lettre = new lettre($_GET['id_lettre']);
			$lettre->titre			= $_POST['titre'];
			$lettre->id_rubrique	= $_POST['id_parent'];
			$lettre->descriptif		= $_POST['descriptif'];
			$lettre->chapo			= $_POST['chapo'];
			$lettre->texte			= $_POST['texte'];
			$lettre->ps				= $_POST['ps'];
/*
TODO
			if ($champs_extra)
				$lettre->extra		= extra_recup_saisie("lettres");
*/

			$lettre->enregistrer();
			$lettre->enregistrer_auteur($GLOBALS['auteur_session']['id_auteur']);

			$url = generer_url_ecrire('lettres', 'id_lettre='.$lettre->id_lettre, true);
			header('Location: ' . $url);
			exit();
		}

		if (!empty($_GET['id_lettre'])) {
			$lettre = new lettre($_GET['id_lettre']);
			if ($lettre->statut == 'envoi_en_cours' or $lettre->statut == 'envoyee') {
				include_spip('inc/minipres');
				echo minipres();
				exit;
			}
		} else {
			$id_rubrique	= intval($_GET['id_rubrique']);
			if (!$id_rubrique) $id_rubrique = sql_getfetsel('id_rubrique', 'spip_rubriques', 'statut="publie"', 'id_rubrique', '1');
			if (!$id_rubrique) $id_rubrique = sql_getfetsel('id_rubrique', 'spip_rubriques', '', 'id_rubrique', '1');
			$lettre = new lettre();
			$lettre->titre			= _T('lettresprive:nouvelle_lettre');
			$lettre->id_rubrique	= $id_rubrique;
		}
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page($lettre->titre, "naviguer", "lettres_tous");

		echo debut_grand_cadre(true);
		echo afficher_hierarchie($lettre->id_rubrique);
		echo fin_grand_cadre(true);

		echo debut_gauche("",true);

		if ($lettre->existe){
			echo afficher_documents_colonne($lettre->id_lettre, 'lettre');
		} else {
			# ICI GROS HACK
			# -------------
			echo afficher_documents_colonne(0-$GLOBALS['visiteur_session']['id_auteur'], 'lettre');
		}

		echo pipeline('affiche_gauche', array('args' => array('exec' => 'lettres_edit', 'id_lettre' => $lettre->id_lettre), 'data' => ''));
		echo creer_colonne_droite("",true);
		echo pipeline('affiche_droite', array('args' => array('exec' => 'lettres_edit', 'id_lettre' => $lettre->id_lettre), 'data' => ''));
		echo debut_droite("",true);

/*
TODO
		$s = "";
		$s.= debut_cadre_relief("../"._DIR_PLUGIN_LETTRES."img_pack/preferences.png", true);
		$s.= "<div style='padding: 2px; background-color: $couleur_claire; text-align: center; color: black;'>";
		$s.= "<strong class='verdana3' style='text-transform: uppercase;'>"._T("lettresprive:personnaliser_lettre")."</strong>";
		$s.= "</div>\n";
		$s.= "<br />\n";
		$s.= "<div class='verdana2'>";
		$s.= _T("lettresprive:texte_personnaliser_lettre");
		$s.= "</div>";
		$s.= "<br />\n";
		$s.= "<div class='bandeau_rubriques' style='z-index: 1;'>";
		$s.= "<div class='plan-articles'>";
		// email
		if ($GLOBALS['browser_barre'])
			$onclick = " ondblclick='barre_inserer(\" %%EMAIL%% \", document.formulaire.texte);' title=\"". entites_html(_T('lettresprive:double_clic_inserer_champ'))."\"";
		$s.= "<div align='center'$onclick>%%EMAIL%%</div>\n";
		// nom
		$titre_sinon = '%%NOM|sinon%%';
		$titre = '%%NOM%%';
		if ($GLOBALS['browser_barre']) {
			$onclick_sinon = " ondblclick='barre_inserer(\" ".$titre_sinon." \", document.formulaire.texte);' title=\"". entites_html(_T('lettresprive:double_clic_inserer_champ'))."\"";
			$onclick = " ondblclick='barre_inserer(\" ".$titre." \", document.formulaire.texte);' title=\"". entites_html(_T('lettresprive:double_clic_inserer_champ'))."\"";
		}
		$s.= "<br />";
		$s.= "<div align='center'$onclick>".$titre."</div>\n";
		$s.= "<div align='center'$onclick_sinon>".$titre_sinon."</div>\n";
		if ($champs_extra['abonnes']) {
			$s.= "<br />";
			foreach ($champs_extra['abonnes'] as $cle => $valeur) {
				$titre_sinon = '%%'.strtoupper($cle).'|sinon%%';
				$titre = '%%'.strtoupper($cle).'%%';
				if ($GLOBALS['browser_barre']) {
					$onclick_sinon = " ondblclick='barre_inserer(\" ".$titre_sinon." \", document.formulaire.texte);' title=\"". entites_html(_T('lettresprive:double_clic_inserer_champ'))."\"";
					$onclick = " ondblclick='barre_inserer(\" ".$titre." \", document.formulaire.texte);' title=\"". entites_html(_T('lettresprive:double_clic_inserer_champ'))."\"";
				}
				$s.= "<div align='center'$onclick>".$titre."</div>\n";
				$s.= "<div align='center'$onclick_sinon>".$titre_sinon."</div>\n";
				$s.= "<br />";
			}
		}
		$s.= "</div>";
		$s.= "</div>";
		$s.= "<br />";
		$s.= fin_cadre_relief(true);
		echo $s;
*/

		echo '<div class="cadre-formulaire-editer">';
		echo '<div class="entete-formulaire">';
		if ($lettre->existe) {
			echo icone_inline(_T('icone_retour'), generer_url_ecrire('lettres', 'id_lettre='.$lettre->id_lettre), _DIR_PLUGIN_LETTRES.'prive/images/lettre-24.png', "rien.gif", $GLOBALS['spip_lang_left']);
		} else {
			if ($lettre->id_rubrique)
				echo icone_inline(_T('icone_retour'), generer_url_ecrire('naviguer', 'id_rubrique='.$lettre->id_rubrique), _DIR_PLUGIN_LETTRES.'prive/images/rubrique-24.png', "rien.gif", $GLOBALS['spip_lang_left']);
			else
				echo icone_inline(_T('icone_retour'), generer_url_ecrire('lettres_tous'), _DIR_PLUGIN_LETTRES.'prive/images/lettre-24.png', "rien.gif", $GLOBALS['spip_lang_left']);
		}
		echo _T('lettresprive:modifier_lettre');
		echo '<h1>'.$lettre->titre.'</h1>';
		echo '</div>';

		echo '<div class="formulaire_spip formulaire_editer">';
		echo '<form method="post" action="'.generer_url_ecrire('lettres_edit', ($lettre->id_lettre ? 'id_lettre='.$lettre->id_lettre : '')).'">';
		echo '<div>';

	  	echo '<ul>';

	    echo '<li class="obligatoire">';
		echo '<label for="titre">'._T('lettresprive:titre').'</label>';
		echo '<input type="text" class="text" name="titre" id="titre" value="'.$lettre->titre.'" '.($lettre->id_lettre == -1 ? 'onfocus="if(!antifocus){this.value=\'\';antifocus=true;}" ' : '').'/>';
		echo '</li>';

	    echo '<li class="editer_parent">';
		echo '<label for="id_parent">'._T('titre_cadre_interieur_rubrique').'</label>';
		$selecteur_rubrique = charger_fonction('chercher_rubrique', 'inc');
		echo $selecteur_rubrique($lettre->id_rubrique, 'lettre', false);
		echo '</li>';
	
		if ($GLOBALS['meta']['spip_lettres_utiliser_descriptif'] == 'oui') {
		    echo '<li class="editer_descriptif">';
			echo '<label for="descriptif">'._T('lettresprive:descriptif').'</label>';
			echo '<textarea name="descriptif" id="descriptif" rows="2" cols="40">'.$lettre->descriptif.'</textarea>';
			echo '</li>';
		}

		if ($GLOBALS['meta']['spip_lettres_utiliser_chapo'] == 'oui') {
		    echo '<li class="editer_chapo">';
			echo '<label for="chapo">'._T('lettresprive:chapo').'</label>';
			echo '<textarea name="chapo" id="chapo" rows="8" cols="40">'.$lettre->chapo.'</textarea>';
			echo '</li>';
		}

	    echo '<li class="editer_texte">';
		echo '<label for="text_area">'._T('lettresprive:texte').'</label>';
		echo '<div class="explication">'._T('texte_enrichir_mise_a_jour').'<em>'.aide('raccourcis').'</em></div>';
		echo '<textarea name="texte" id="text_area" rows="20" cols="40" class="barre_inserer" '.$GLOBALS['browser_caret'].'>'.$lettre->texte.'</textarea>';
		echo '</li>';

		if ($GLOBALS['meta']['spip_lettres_utiliser_ps'] == 'oui') {
		    echo '<li class="editer_ps">';
			echo '<label for="ps">'._T('lettresprive:ps').'</label>';
			echo '<textarea name="ps" id="ps" rows="3" cols="40">'.$lettre->ps.'</textarea>';
			echo '</li>';
		}

		echo '</ul>';

	  	echo '<p class="boutons"><input type="submit" class="submit" name="enregistrer" value="'._T('lettresprive:enregistrer').'" /></p>';

		echo '</div>';

		echo '</form>';

		echo '</div>';
		echo '</div>';
	 	
		echo fin_gauche();

		echo fin_page();

	}

?>