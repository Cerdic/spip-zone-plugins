<?php


	/**
	 * SPIP-Sondages : plugin de gestion de sondages
	 *
	 * Copyright (c) 2006
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/


	include_spip('inc/sondages_fonctions');
	include_spip('inc/sondages_admin');
 	include_spip('inc/presentation');
	include_spip('inc/barre');
	include_spip('inc/documents');
	include_spip('inc/rubriques');


	function exec_sondages_edition() {
		global $champs_extra;
	 	
		sondages_verifier_droits();

		$id_sondage = intval($_GET['id_sondage']);
		
		if (!empty($_GET['id_sondage'])) {
			$result = spip_query('SELECT * FROM spip_sondages WHERE id_sondage="'.$id_sondage.'"');
			if (!$sondage = spip_fetch_array($result)) die('erreur');	
			$id_rubrique	= $sondage['id_rubrique'];
			$titre			= $sondage['titre'];
			$texte			= $sondage['texte'];
			$type			= $sondage['type'];
			$extra			= $sondage['extra'];
		} else {
			$new			= true;
			$id_sondage		= -1;
			$titre			= _T('sondages:nouveau_sondage');
			$onfocus		= " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
			$lang			= $GLOBALS['meta']['langue_site'];
		}

	 	debut_page(_T('sondages:sondages'), "naviguer", "sondages");

	 	debut_gauche();
		if (!$new) {
			maj_documents($id_sondage, 'sondage');
			afficher_documents_colonne($id_sondage, "sondage", true);
		}
		

	 	debut_droite();
		echo "<br />";
		debut_cadre_formulaire();
		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		echo "<tr width='100%'>";
		echo "<td>";
		if ($new)
			icone(_T('icone_retour'), generer_url_ecrire("sondages"), '../'._DIR_PLUGIN_SONDAGES.'/img_pack/sondages-24.png', "rien.gif");
		else
			icone(_T('icone_retour'), generer_url_ecrire("sondages_visualisation", "id_sondage=$id_sondage"), '../'._DIR_PLUGIN_SONDAGES.'/img_pack/sondages-24.png', "rien.gif");

		echo "</td>";
		echo "<td>". http_img_pack('rien.gif', " ", "width='10'") . "</td>\n";
		echo "<td width='100%'>";
		echo _T('sondages:editer_sondage');
		gros_titre($titre);
		echo "</td></tr></table>";

		echo "<P><HR></P>";

		echo generer_url_post_ecrire("sondages_visualisation", ($id_sondage ? "id_sondage=$id_sondage" : ""), 'formulaire');

		echo "<INPUT TYPE='hidden' NAME='lang' VALUE='$lang'>";

		echo "<P><B>"._T('sondages:titre')."</B>";
		echo "<BR><INPUT TYPE='text' NAME='titre' style='font-weight: bold; font-size: 13px;' CLASS='formo' VALUE=\"$titre\" SIZE='40' $onfocus><P><BR>";

		debut_cadre_couleur("rubrique-24.gif", false, "", _T('titre_cadre_interieur_rubrique'));
		echo selecteur_rubrique($id_rubrique, 'sondage', 1);
		fin_cadre_couleur();

		echo "<P>"._T('sondages:type')."<br />";
		echo "<select name='type' CLASS='fondl'>";		
		echo '<option value="simple" '; if ($type == 'simple') echo 'selected'; echo '>'._T('sondages:simple').'</option>';
		echo '<option value="multiple" '; if ($type == 'multiple') echo 'selected'; echo '>'._T('sondages:multiple').'</option>';
		echo "</select></P>\n";

		echo "<p><B>"._T('sondages:texte')."</B>";
		echo "<br>"._T('texte_enrichir_mise_a_jour');
		echo aide("raccourcis");
		echo afficher_barre('document.formulaire.texte');
		echo "<TEXTAREA id='text_area' NAME='texte' ".$GLOBALS['browser_caret']." CLASS='formo' ROWS='20' COLS='40' wrap=soft>";
		echo $texte;
		echo "</TEXTAREA></p>\n";

		if ($champs_extra) {
			include_spip('inc/extra');
			extra_saisie($extra, 'sondages');
		}

		echo "<DIV ALIGN='right'>";
		echo "<INPUT CLASS='fondo' TYPE='submit' NAME='enregistrer' VALUE='"._T('sondages:enregistrer')."'>";
		echo "</DIV></FORM>";	 	
	 		 	
	 	fin_cadre_formulaire();
	 	
	 	fin_page();
	 	
	}
	
	
	
?>