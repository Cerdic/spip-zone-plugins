<?php

/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007
 * Inspire de Spip-Listes
*/


include_spip('inc/abomailmans');
	include_spip('inc/barre');



function exec_abomailmans_envoyer(){

	

	//
	// Affichage de la page
	//

	debut_page(_T("abomailmans:envoyer_mailmans"), "documents", "abomailmans","");

	debut_gauche();
	debut_boite_info();
		icone_horizontale (_T("abomailmans:icone_envoyer_mail_liste"), generer_url_ecrire("abomailmans_envoyer",""), "../"._DIR_PLUGIN_ABOMAILMANS."/img_pack/configure_mail.png", "");
	fin_boite_info();

	debut_droite();

	debut_cadre_formulaire();

		$liste_templates = find_all_in_path("templates/","[.]html$");
		echo "<div id=\"ajax-loader\" align=\"right\"><img src=\""._DIR_PLUGIN_ABOMAILMANS. "/img_pack/ajax_indicator.gif\" /></div>";
		echo "<div class='verdana2' id='envoyer'>";
	
		echo "<form method='POST' action='?exec=abomailmans_affiche_template' style='border: 0px; margin: 0px;' id='template' name='template'>";
		echo "<br/><strong><label for='template'>"._T("abomailmans:template")."</label></strong><br/>";
		echo "<select name='template'  CLASS='formo'>";
		foreach($liste_templates as $titre_option) {
			$titre_option = basename($titre_option,".html");		
			echo "<option value='".$titre_option."'>".$titre_option."</option>\n";
		}
		echo "</select><br />";
			
	
 		echo "<label for=\"date\">Contenu a partir de cette date</label><br />\n";
		echo "<input name=\"date\" id=\"date\" class=\"date-picker\"  /><br /><br /><br />\n";

		echo "<strong><label for='sujet'>"._T("abomailmans:rubrique")."</label></strong>";
		echo "<br />";
		
		echo "<select name=\"id_rubrique\"  CLASS='formo'>";
		echo "<option value=\"\"></option>";
		echo abomailman_arbo_rubriques(0);
		echo "</select><br />";


		echo "<strong><label for='sujet'>"._T("abomailmans:mot")."</label></strong>";
		echo "<br />";
		
		echo "<select name=\"id_mot\"  CLASS='formo'>";
		echo "<option value=\"\"></option>";
		$rqt_gmc = spip_query ("SELECT id_groupe, titre FROM spip_groupes_mots WHERE articles='oui'");
		while ($row = spip_fetch_array($rqt_gmc)) {
		$id_groupe = $row['id_groupe'];
		$titre = $row['titre'];
			echo "<option value='' disabled=\"disabled\">". supprimer_numero (typo($titre)) . "</option>";

			$rqt_mc = spip_query ("SELECT id_mot, titre FROM spip_mots WHERE id_groupe='".$id_groupe."'");

			while ($row = spip_fetch_array($rqt_mc)) {
				$id_mot = $row['id_mot'];
				$titre = $row['titre'];
				echo "<option value='".$id_mot ."'>--". supprimer_numero (typo($titre)) . "</option>";
			}
		
		}
		echo "</select><br />";

		echo "<strong><label for='sujet'>"._T("abomailmans:sujet")."</label></strong> "._T('info_obligatoire_02');
		echo "<br />";
		
		echo "<input type='text' name='sujet' id='sujet' CLASS='formo' value=\"\" size='40'$js_titre /><br />\n";

		echo "<strong><label for='message'>"._T("abomailmans:message")."</label></strong>";
		echo aide ("raccourcis");
		echo "<br />";
		echo afficher_barre('document.template.message');
		echo "<textarea id='text_area' name='message' ".$GLOBALS['browser_caret']." class='formo' rows='5' cols='40' wrap=soft>";
		echo $texte;
		echo "</textarea>\n";
		echo "<div align='right'>";
		echo "<input type='submit' name='Valider' value='"._T('abomailmans:envoi_apercu')."' class='fondo'></div>\n";
		echo "</form>";
		echo "</div>\n";
	

	fin_cadre_formulaire();
		
		echo "<div id='apercu'></div>";

	fin_page();
}
?>
