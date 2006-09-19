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
	include_spip('inc/date');


	/**
	 * exec_lettres_envoi
	 *
	 * Vue permettant d'envoyer une lettre
	 *
	 * @author Pierre Basson
	 **/
	function exec_lettres_envoi() {
		global $dir_lang, $spip_lang_right;

		lettres_verifier_droits();


		if (empty($_GET['id_lettre'])) {
			$url = generer_url_ecrire('lettres', '', '&');
			lettres_rediriger_javascript($url);
		}

		$id_lettre	= $_GET['id_lettre'];
		$url_lettre = generer_url_ecrire('lettres_envoi', 'id_lettre='.$id_lettre, '&');

		if (!empty($_POST['changer_statut'])) {
			$statut		= $_POST['statut'];
			if ($statut == 'publie') {
				$id_archive = lettres_recuperer_dernier_id_archive($id_lettre);

				$requete_nb_emails_envoyes = 'SELECT id_abonne FROM spip_abonnes_archives WHERE id_archive="'.$id_archive.'" AND statut="envoye"';
				$nb_emails_envoyes = intval(@spip_num_rows(spip_query($requete_nb_emails_envoyes)));
				$requete_nb_emails_non_envoyes = 'SELECT id_abonne FROM spip_abonnes_archives WHERE id_archive="'.$id_archive.'" AND statut="a_envoyer"';
				$nb_emails_non_envoyes = intval(@spip_num_rows(spip_query($requete_nb_emails_non_envoyes)));
				$requete_nb_emails_echec = 'SELECT id_abonne FROM spip_abonnes_archives WHERE id_archive="'.$id_archive.'" AND statut="echec"';
				$nb_emails_echec = intval(@spip_num_rows(spip_query($requete_nb_emails_echec)));

				$requete_nb_emails_html = 'SELECT id_abonne FROM spip_abonnes_archives WHERE id_archive="'.$id_archive.'" AND format="html"';
				$nb_emails_html = intval(@spip_num_rows(spip_query($requete_nb_emails_html)));
				$requete_nb_emails_texte = 'SELECT id_abonne FROM spip_abonnes_archives WHERE id_archive="'.$id_archive.'" AND format="texte"';
				$nb_emails_texte = intval(@spip_num_rows(spip_query($requete_nb_emails_texte)));
				$requete_nb_emails_mixte = 'SELECT id_abonne FROM spip_abonnes_archives WHERE id_archive="'.$id_archive.'" AND format="mixte"';
				$nb_emails_mixte = intval(@spip_num_rows(spip_query($requete_nb_emails_mixte)));

				$modification_archives = 'UPDATE spip_archives 
											SET date_fin_envoi=NOW(),
											 	nb_emails_envoyes="'.$nb_emails_envoyes.'",
											 	nb_emails_non_envoyes="'.$nb_emails_non_envoyes.'",
												nb_emails_echec="'.$nb_emails_echec.'",
											 	nb_emails_html="'.$nb_emails_html.'",
											 	nb_emails_texte="'.$nb_emails_texte.'",
												nb_emails_mixte="'.$nb_emails_mixte.'" 
											WHERE id_archive="'.$id_archive.'" LIMIT 1';
				spip_query($modification_archives);

				$modification = 'UPDATE spip_lettres SET statut="publie", maj=NOW() WHERE id_lettre="'.$id_lettre.'" LIMIT 1';
				spip_query($modification);
				$url = generer_url_ecrire('lettres_visualisation', 'id_lettre='.$id_lettre, '&');
				lettres_rediriger_javascript($url);
			}
		}


		$requete_lettre = 'SELECT titre, descriptif, texte, lang, statut, date FROM spip_lettres WHERE id_lettre="'.$id_lettre.'" LIMIT 1';
		$resultat_lettre = spip_query($requete_lettre);
		list($titre, $descriptif, $texte, $lang, $statut, $date) = spip_fetch_array($resultat_lettre,SPIP_NUM);
		$titre		= entites_html($titre);
		$descriptif	= propre($descriptif);
		$texte		= propre($texte);
		$onfocus	= '';
		if ($statut != 'envoi_en_cours') {
			$url = generer_url_ecrire('lettres');
			lettres_rediriger_javascript($url);
		}
		$id_archive = lettres_recuperer_dernier_id_archive($id_lettre);
		
		
		$requete_a_envoyer	= 'SELECT id_abonne 
								FROM spip_abonnes_archives 
								WHERE id_archive="'.$id_archive.'"
									AND statut="a_envoyer"';
		$resultat_a_envoyer = spip_query($requete_a_envoyer);
		$a_envoyer = @spip_num_rows($resultat_a_envoyer);
		$requete_total 	= 'SELECT id_abonne 
							FROM spip_abonnes_archives 
							WHERE id_archive="'.$id_archive.'"';
		$resultat_total = spip_query($requete_total);
		$total = @spip_num_rows($resultat_total);


		debut_page($titre, "lettres", "lettres");


		debut_gauche();

		lettres_afficher_numero_lettre($id_lettre);
		lettres_afficher_statistiques_envoi_en_cours($titre, $id_archive);

		debut_raccourcis();
		lettres_afficher_raccourci_liste_lettres(_T('lettres:raccourci_retour_liste_lettres'));
		if ($a_envoyer != 0) {
			if ($a_envoyer == $total)
				lettres_afficher_raccourci_envoyer_lettre($id_lettre);
			else
				lettres_afficher_raccourci_reprendre_envoi($id_lettre);
		}
		fin_raccourcis();

    	debut_droite();
		debut_cadre_relief('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre-envoi-en-cours.png');

		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		switch ($statut) {
			case 'brouillon':
				$logo_statut = "puce-blanche.gif";
				break;
			case 'publie':
				$logo_statut = "puce-verte.gif";
				break;
			case 'envoi_en_cours':
				$logo_statut = "puce-orange.gif";
				break;
		}
		echo "<tr width='100%'><td width='100%' valign='top'>";
		gros_titre($titre, $logo_statut);
		echo "</td>";
		echo "<td>", http_img_pack("rien.gif", ' ', "width='5'") ."</td>\n";
		echo "<td  align='$spip_lang_right' valign='top'>";
		icone(_T('lettres:rafraichir'), generer_url_ecrire("lettres_envoi","id_lettre=$id_lettre", '&'), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/rafraichir.png');
		echo "</td>";
		echo "</tr>\n";
		if (strlen($descriptif) > 1) {
			echo "<tr><td><br />\n";
			echo "<div align='$spip_lang_left' style='padding: 5px; border: 1px dashed #aaaaaa;'>";
			echo "<font size=2 face='Verdana,Arial,Sans,sans-serif'>";
			echo $descriptif;
			echo "</font>";
			echo "</div></td></tr>\n";
		}
		echo "</table>\n";

		echo "<div>&nbsp;</div>";


		echo generer_url_post_ecrire("lettres_envoi", "id_lettre=$id_lettre", 'formulaire');

		lettres_afficher_date($date);
		lettres_afficher_auteurs($id_lettre);
		lettres_afficher_mots_cles($id_lettre);
		lettres_afficher_langue($lang);

		echo "<br />";
		debut_cadre_relief();
		echo "<center><B>"._T('lettres:statut_envoi')."</B>&nbsp;";
		echo "<SELECT NAME='statut' SIZE='1' CLASS='fondl'>\n";
		if ($a_envoyer == 0)
			echo '	<OPTION VALUE="publie"'.(($statut == 'publie') ? ' SELECTED' : '').'>'._T('lettres:action_termine').'</OPTION>'."\n";
		else
			echo '	<OPTION VALUE="publie"'.(($statut == 'publie') ? ' SELECTED' : '').'>'._T('lettres:action_arreter').'</OPTION>'."\n";
		echo '	<OPTION VALUE="envoi_en_cours"'.(($statut == 'envoi_en_cours') ? ' SELECTED' : '').'>'._T('lettres:action_en_cours').'</OPTION>'."\n";
		echo "</SELECT>";
		echo "&nbsp;&nbsp;<INPUT TYPE='submit' NAME='changer_statut' CLASS='fondo' VALUE='"._T('lettres:changer')."' STYLE='font-size:10px'>";
		echo '</center>';
		fin_cadre_relief();

		if ($a_envoyer == 0) {
			echo '<br />';
			echo '<br />';
			echo '<center><b>';
			echo _T('lettres:envoi_termine');
			echo '</b></center>';
			echo '<br />';
		}

		echo '</form>';

		fin_page();

	}

?>