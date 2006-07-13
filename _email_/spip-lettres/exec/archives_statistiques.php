<?php


	/**
	 * SPIP-Lettres : plugin de gestion de lettres d'information
	 *
	 * Copyright (c) 2006
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/


	include_spip('inc/lettres_fonctions');
	include_spip('inc/lettres_admin');
 	include_spip('inc/presentation');


	/**
	 * exec_archives_statistiques
	 *
	 * Statistiques
	 *
	 * @author Pierre Basson
	 **/
	function exec_archives_statistiques() {

		lettres_verifier_droits();

		debut_page(_T('lettres:statistiques'), "lettres", "lettres_statistiques");

		debut_gauche();

		lettres_afficher_numero_archive($id_archive, true, true);
		lettres_afficher_statistiques_archive($titre, $id_archive);

		debut_raccourcis();
#		lettres_afficher_raccourci_retourner_archive($id_archive);
		lettres_afficher_raccourci_retourner_lettre($id_lettre);

		lettres_afficher_raccourci_liste_abonnes(_T('lettres:aller_liste_abonnes'));
		lettres_afficher_raccourci_liste_lettres(_T('lettres:aller_liste_lettres'));
		fin_raccourcis();

    	debut_droite();
		debut_cadre_relief('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/archives.png');

		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		echo "<tr width='100%'><td width='100%' valign='top'>";
		gros_titre($titre);
		echo "</td>";
		echo "<td>", http_img_pack("rien.gif", ' ', "width='5'") ."</td>\n";
		echo "<td  align='$spip_lang_right' valign='top'>";
		icone(_T('lettres:raccourci_retourner_lettre'), generer_url_ecrire("lettres_visualisation","id_lettre=$id_lettre", '&'), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre-24.png');
		echo "</td>";
		echo "</tr>\n";
		echo "</table>\n";
		echo "<div>&nbsp;</div>";

		lettres_afficher_dates_archive($date, $date_debut_envoi, $date_fin_envoi);

		echo generer_url_post_ecrire("archives_visualisation", "id_archive=$id_archive", 'formulaire');
		echo '<br />';
		debut_cadre_relief();
		echo "<center><B>"._T('lettres:action_archive')."</B>&nbsp;";
		echo "<SELECT NAME='action' SIZE='1' CLASS='fondl'>\n";
		echo '	<OPTION VALUE="aucune" SELECTED>'._T('lettres:action_archive_aucune').'</OPTION>'."\n";
		echo '	<OPTION VALUE="poubelle">'._T('lettres:action_archive_poubelle').'</OPTION>'."\n";
		echo "</SELECT>";
		echo "&nbsp;&nbsp;<INPUT TYPE='submit' NAME='changer_action' CLASS='fondo' VALUE='"._T('lettres:changer')."' STYLE='font-size:10px'>";
		echo '</center>';
		fin_cadre_relief();
		echo '<br />';
		echo '</form>';
		
		fin_cadre_relief();

		echo "<br/>\n";

	 	echo lettres_afficher_abonnes_archive(_T('lettres:abonnes_archives'), _DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonnes.png', $id_archive, 'archives_visualisation', "id_archive=$id_archive", 'position_abonnes_archive');

		fin_page();
	}


?>