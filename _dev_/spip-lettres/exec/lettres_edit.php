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
	include_spip('inc/barre');
	include_spip('inc/documents');
	include_spip('inc/headers');
	include_spip('inc/extra');
	include_spip('lettres_fonctions');


	function exec_lettres_edit() {
		global $dir_lang, $spip_lang_right, $champs_extra, $options, $spip_display;
		global $cherche_mot, $select_groupe;

		if (!autoriser('editer', 'lettres')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		pipeline('exec_init',array('args'=>array('exec'=>'lettres_edit','id_lettre'=>$_GET['id_lettre']),'data'=>''));

		if (!empty($_POST['enregistrer'])) {
			$lettre = new lettre($_GET['id_lettre']);
			$lettre->titre			= $_POST['titre'];
			$lettre->id_rubrique	= $_POST['id_parent'];
			$lettre->descriptif		= $_POST['descriptif'];
			$lettre->texte			= $_POST['texte'];
			$lettre->ps				= $_POST['ps'];
			if ($champs_extra)
				$lettre->extra		= extra_recup_saisie("lettres");

			$lettre->enregistrer();
			$lettre->enregistrer_auteur($GLOBALS['auteur_session']['id_auteur']);

			$url = generer_url_ecrire('lettres', 'id_lettre='.$lettre->id_lettre, true);
			header('Location: ' . $url);
			exit();
		}

		if (!empty($_GET['id_lettre'])) {
			$lettre = new lettre($_GET['id_lettre']);
			if ($lettre->statut == 'envoi_en_cours' or $lettre->statut == 'envoyee') {
				echo _T('avis_non_acces_page');
				echo fin_page();
				exit;
			}
		} else {
			$id_rubrique	= intval($_GET['id_rubrique']);
			if (!$id_rubrique) list($id_rubrique) = spip_fetch_array(spip_query('SELECT id_rubrique FROM spip_rubriques WHERE statut="publie" ORDER BY id_rubrique LIMIT 1'), SPIP_NUM);
			$onfocus		= " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
			$lettre = new lettre();
			$lettre->titre			= _T('lettresprive:nouvelle_lettre');
			$lettre->id_rubrique	= $id_rubrique;
		}
		

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page($lettre->titre, "naviguer", "lettres_tous");


		debut_grand_cadre();
		echo afficher_hierarchie($lettre->id_rubrique);
		fin_grand_cadre();


		debut_gauche();
		if ($lettre->existe)
			echo afficher_documents_colonne($lettre->id_lettre, "lettre");

		echo pipeline('affiche_gauche', array('args' => array('exec' => 'lettres_edit', 'id_lettre' => $lettre->id_lettre), 'data' => ''));

		creer_colonne_droite();
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'lettres_edit','id_lettre'=>$lettre->id_lettre),'data'=>''));

		$s = "";
		$s.= debut_cadre_relief("../"._DIR_PLUGIN_LETTRE_INFORMATION."/img_pack/preferences.png", true);
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

    	debut_droite();
		echo "<br />";
		debut_cadre_formulaire();
		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		echo "<tr width='100%'>";
		echo "<td>";
		if ($lettre->existe) {
			icone(_T('icone_retour'), generer_url_ecrire('lettres', 'id_lettre='.$lettre->id_lettre), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre-24.png', "rien.gif");
		} else {
			if ($lettre->id_rubrique)
				icone(_T('icone_retour'), generer_url_ecrire('naviguer', 'id_rubrique='.$lettre->id_rubrique), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/rubrique-24.png', "rien.gif");
			else
				icone(_T('icone_retour'), generer_url_ecrire('lettres_tous'), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre-24.png', "rien.gif");
		}

		echo "</td>";
		echo "<td>". http_img_pack('rien.gif', " ", "width='10'") . "</td>\n";
		echo "<td width='100%'>";
		echo _T('lettresprive:modifier_lettre');
		gros_titre($lettre->titre);
		echo "</td></tr></table>";

		echo "<P><HR></P>";

		echo generer_url_post_ecrire("lettres_edit", ($lettre->id_lettre ? 'id_lettre='.$lettre->id_lettre : ''), 'formulaire');

		echo _T('lettresprive:titre');
		echo '<br /><input type="text" name="titre" style="font-weight: bold; font-size: 13px;" class="formo" value="'.$lettre->titre.'" size="40" '.$onfocus.'/><br/>';

		$selecteur_rubrique = charger_fonction('chercher_rubrique', 'inc');
		debut_cadre_couleur("rubrique-24.gif", false, "", _T('titre_cadre_interieur_rubrique'));
		echo $selecteur_rubrique($lettre->id_rubrique, 'lettre', false);
		fin_cadre_couleur();

		echo "<P><B>"._T('lettresprive:descriptif')."</B>";
		echo "<TEXTAREA NAME='descriptif' CLASS='forml' ROWS='2' COLS='40' wrap=soft>";
		echo $lettre->descriptif;
		echo "</TEXTAREA></P>\n";

		echo "<p><B>"._T('lettresprive:texte')."</B>";
		echo "<br>"._T('texte_enrichir_mise_a_jour');
		echo aide("raccourcis");
		echo afficher_barre('document.formulaire.texte');
		echo "<TEXTAREA id='text_area' NAME='texte' ".$GLOBALS['browser_caret']." CLASS='formo' ROWS='20' COLS='40' wrap=soft>";
		echo $lettre->texte;
		echo "</TEXTAREA></p>\n";

		if ($GLOBALS['meta']['spip_lettres_utiliser_ps'] == 'oui') {
			echo "<p><B>"._T('lettresprive:ps')."</B>";
			echo "<TEXTAREA NAME='ps' CLASS='forml' ROWS='3' COLS='40' wrap=soft>";
			echo $lettre->ps;
			echo "</TEXTAREA></p>\n";
		}

		if ($champs_extra) {
			include_spip('inc/extra');
			echo extra_saisie($lettre->extra, 'lettres');
		}

		echo "<DIV ALIGN='right'>";
		echo "<INPUT CLASS='fondo' TYPE='submit' NAME='enregistrer' VALUE='"._T('lettresprive:enregistrer')."'>";
		echo "</DIV></FORM>";

		fin_cadre_formulaire();

		echo fin_gauche();

		echo fin_page();

	}

?>