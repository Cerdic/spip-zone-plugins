<?php

	include_spip('inc/lettres_fonctions');
	include_spip('inc/lettres_admin');
 	include_spip('inc/presentation');
	include_spip('inc/date');


	/**
	 * exec_archives_visualisation
	 *
	 * @author Pierre Basson
	 **/
	function exec_archives_visualisation() {
		global $dir_lang, $spip_lang_right;

		lettres_verifier_droits();

		if (empty($_GET['id_archive'])) {
			$url = generer_url_ecrire('lettres', '', '&');
			lettres_rediriger_javascript($url);
		}

		if (!empty($_POST['changer_action'])) {
			$id_archive	= intval($_GET['id_archive']);
			if ($_POST['action'] == 'poubelle') {
				$id_lettre = lettres_recuperer_id_lettre_depuis_id_archive($id_archive);
				spip_query('DELETE FROM spip_archives WHERE id_archive="'.$id_archive.'"');
				spip_query('DELETE FROM spip_archives_statistiques WHERE id_archive="'.$id_archive.'"');
				spip_query('DELETE FROM spip_abonnes_archives WHERE id_archive="'.$id_archive.'"');
				$url = generer_url_ecrire('lettres_visualisation', "id_lettre=$id_lettre", '&');
				lettres_rediriger_javascript($url);
			}
		}

		$id_archive	= intval($_GET['id_archive']);
		$requete_archive = 'SELECT id_lettre, titre, message_html, message_texte, date, date_debut_envoi, date_fin_envoi FROM spip_archives WHERE id_archive="'.$id_archive.'" LIMIT 1';
		$resultat_archive = spip_query($requete_archive);
		list($id_lettre, $titre, $message_html, $message_texte, $date, $date_debut_envoi, $date_fin_envoi) = @spip_fetch_array($resultat_archive);

		debut_page($titre, "lettres", "lettres");


		debut_gauche();

		lettres_afficher_numero_archive($id_archive);
		lettres_afficher_statistiques_archive($titre, $id_archive);

		debut_raccourcis();
		lettres_afficher_raccourci_retourner_lettre($id_lettre);
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