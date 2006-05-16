<?php

	include_spip('inc/lettres_fonctions');
	include_spip('inc/lettres_admin');
 	include_spip('inc/presentation');
	include_spip('inc/date');
	include_spip('inc/logos');


	/**
	 * exec_lettres_visualisation
	 *
	 * Edition d'une nouvelle lettre
	 *
	 * @author Pierre Basson
	 **/
	function exec_lettres_visualisation() {
		global $dir_lang, $spip_lang_right;

		lettres_verifier_droits();

		if (empty($_GET['id_lettre'])) {
			$url = generer_url_ecrire('lettres', '', '&');
			lettres_rediriger_javascript($url);
		}

		if (!empty($_POST['enregistrer'])) {
			$id_lettre	= $_GET['id_lettre'];
			$titre		= addslashes($_POST['titre']);
			$descriptif	= addslashes($_POST['descriptif']);
			$texte		= addslashes($_POST['texte']);
			$lang		= addslashes($_POST['lang']);
			$statut		= addslashes($_POST['statut']);
			if ($id_lettre == -1) {
				$insertion = 'INSERT INTO spip_lettres (titre, descriptif, texte, lang, statut, date, maj) VALUES ("'.$titre.'", "'.$descriptif.'", "'.$texte.'", "'.$lang.'", "'.$statut.'", NOW(), NOW())';
				if (spip_query($insertion)) {
					$id_lettre = spip_insert_id();
					spip_query('INSERT INTO spip_auteurs_lettres (id_auteur, id_lettre) VALUES ("'.$GLOBALS['auteur_session']['id_auteur'].'", "'.$id_lettre.'")');
					$url_lettre = generer_url_ecrire('lettres_visualisation', 'id_lettre='.$id_lettre, '&');
					lettres_rediriger_javascript($url_lettre);
				}
			} else {
				$modification = 'UPDATE spip_lettres SET titre="'.$titre.'", descriptif="'.$descriptif.'", texte="'.$texte.'", maj=NOW() WHERE id_lettre="'.$id_lettre.'"';
				spip_query($modification);
				lettres_rediriger_javascript($url_lettre);
			}
		}

		$id_lettre	= $_GET['id_lettre'];
		$url_lettre = generer_url_ecrire('lettres_visualisation', 'id_lettre='.$id_lettre, '&');

		if (!empty($_GET['supprimer_auteur'])) {
			$id_auteur = intval($_GET['supprimer_auteur']);
			$suppression = 'DELETE FROM spip_auteurs_lettres WHERE id_auteur="'.$id_auteur.'" AND id_lettre="'.$id_lettre.'" LIMIT 1';
			spip_query($suppression);
			lettres_rediriger_javascript($url_lettre);
		}

		if (!empty($_POST['changer_auteur'])) {
			$id_auteur = intval($_POST['id_auteur']);
			$insertion = 'INSERT INTO spip_auteurs_lettres (id_auteur, id_lettre) VALUES ("'.$id_auteur.'", "'.$id_lettre.'")';
			@spip_query($insertion);
			lettres_rediriger_javascript($url_lettre);
		}

		if (!empty($_GET['supprimer_mot'])) {
			$id_mot = intval($_GET['supprimer_mot']);
			$suppression = 'DELETE FROM spip_mots_lettres WHERE id_mot="'.$id_mot.'" AND id_lettre="'.$id_lettre.'" LIMIT 1';
			spip_query($suppression);
			lettres_rediriger_javascript($url_lettre);
		}

		if (!empty($_POST['changer_mot'])) {
			$id_mot = intval($_POST['id_mot']);
			$insertion = 'INSERT INTO spip_mots_lettres (id_mot, id_lettre) VALUES ("'.$id_mot.'", "'.$id_lettre.'")';
			@spip_query($insertion);
			lettres_rediriger_javascript($url_lettre);
		}

		if (!empty($_POST['changer_date'])) {
			$annee		= $_POST['annee'];
			$mois		= $_POST['mois'];
			$jour		= $_POST['jour'];
			$date		= $annee.'-'.$mois.'-'.$jour.' 12:00:00';
			$modification = 'UPDATE spip_lettres SET date="'.$date.'", maj=NOW() WHERE id_lettre="'.$id_lettre.'"';
			spip_query($modification);
			lettres_rediriger_javascript($url_lettre);
		}

		if (!empty($_POST['changer_langue'])) {
			$lang		= addslashes($_POST['lang']);
			$modification = 'UPDATE spip_lettres SET lang="'.$lang.'", maj=NOW() WHERE id_lettre="'.$id_lettre.'"';
			spip_query($modification);
			lettres_rediriger_javascript($url_lettre);
		}

		if (!empty($_POST['changer_statut'])) {
			$statut		= $_POST['statut'];
			if ($statut == 'poubelle') {
				$suppression = 'DELETE FROM spip_lettres WHERE id_lettre="'.$id_lettre.'" LIMIT 1';
				spip_query($suppression);
				$suppression_mots = 'DELETE FROM spip_mots_lettres WHERE id_lettre="'.$id_lettre.'"';
				spip_query($suppression_mots);
				$suppression_abonnes = 'DELETE FROM spip_abonnes_lettres WHERE id_lettre="'.$id_lettre.'"';
				spip_query($suppression_abonnes);
				$requete_archives = 'SELECT id_archive FROM spip_archives WHERE id_lettre="'.$id_lettre.'"';
				$resultat_archives = spip_query($requete_archives);
				while ($arr = @spip_fetch_array($resultat_archives)) {
					$suppression_abonnes_archives = 'DELETE FROM spip_abonnes_archives WHERE id_archive="'.$arr['id_archive'].'"';
					spip_query($suppression_abonnes_archives);
					$suppression_archives_statistiques = 'DELETE FROM spip_archives_statistiques WHERE id_archive="'.$arr['id_archive'].'"';
					spip_query($suppression_abonnes_archives);
				}
				$suppression_archives = 'DELETE FROM spip_archives WHERE id_lettre="'.$id_lettre.'"';
				spip_query($suppression_archives);
				$url = generer_url_ecrire('lettres');
				lettres_rediriger_javascript($url);
			} else if ($statut == 'purger') {
				$suppression_abonnes = 'DELETE FROM spip_abonnes_lettres WHERE id_lettre="'.$id_lettre.'"';
				spip_query($suppression_abonnes);
				$requete_archives = 'SELECT id_archive FROM spip_archives WHERE id_lettre="'.$id_lettre.'"';
				$resultat_archives = spip_query($requete_archives);
				while ($arr = @spip_fetch_array($resultat_archives)) {
					$suppression_abonnes_archives = 'DELETE FROM spip_abonnes_archives WHERE id_archive="'.$arr['id_archive'].'"';
					spip_query($suppression_abonnes_archives);
				}
			} else {
				$modification = 'UPDATE spip_lettres SET statut="'.$statut.'" WHERE id_lettre="'.$id_lettre.'"';
				spip_query($modification);
				if ($statut == 'envoi_en_cours') {
					// Récupération des infos essentielles
					$requete_lettre = 'SELECT titre, date FROM spip_lettres WHERE id_lettre="'.$id_lettre.'" LIMIT 1';
					$resultat_lettre = spip_query($requete_lettre);
					list($titre, $date) = spip_fetch_array($resultat_lettre);

					// Création de l'archive
					$fond_message_html	= lettres_recuperer_meta('fond_message_html');
					$fond_message_texte	= lettres_recuperer_meta('fond_message_texte');
					$f = charger_fonction('assembler', 'public');
					$page_html	= $f($fond_message_html, array('id_lettre' => $id_lettre));
					$page_texte	= $f($fond_message_texte, array('id_lettre' => $id_lettre));
					$message_html	= addslashes($page_html['texte']);
					$message_texte	= addslashes($page_texte['texte']);

					$creation_archive = 'INSERT INTO spip_archives (id_lettre,
																	titre,
																	message_html,
																	message_texte,
																	date,
																	date_debut_envoi)
															VALUES ("'.$id_lettre.'",
																	"'.$titre.'",
																	"'.$message_html.'",
																	"'.$message_texte.'",
																	"'.$date.'",
																	NOW())';
					$resultat_creation_archive = spip_query($creation_archive);
					$id_archive = spip_insert_id();

					// Copie des abonnés à cette lettre et affectation à l'archive
					$abonnes = 'SELECT A.id_abonne, 
										A.format 
									FROM spip_abonnes_lettres AS AL
									INNER JOIN spip_abonnes AS A ON A.id_abonne=AL.id_abonne
									WHERE AL.id_lettre="'.$id_lettre.'" 
										AND AL.statut="valide"';
					$resultat_abonnes = spip_query($abonnes);
					while ($arr = @spip_fetch_array($resultat_abonnes)) {
						$id_abonne	= $arr['id_abonne'];
						$format		= $arr['format'];
						$requete_copie = 'INSERT INTO spip_abonnes_archives (id_abonne,
																			id_archive,
																			statut,
																			format,
																			maj)
																	VALUES	("'.$id_abonne.'",
																			"'.$id_archive.'",
																			"a_envoyer",
																			"'.$format.'",
																			NOW())';
						$resultat_copie = spip_query($requete_copie);
					}
				}
				lettres_rediriger_javascript($url_lettre);
			}
		}

		$requete_lettre = 'SELECT titre, descriptif, texte, lang, statut, date FROM spip_lettres WHERE id_lettre="'.$id_lettre.'" LIMIT 1';
		$resultat_lettre = spip_query($requete_lettre);
		list($titre, $descriptif, $texte, $lang, $statut, $date) = spip_fetch_array($resultat_lettre);
		$titre		= entites_html($titre);
		$descriptif	= propre($descriptif);
		$texte		= propre($texte);
		$onfocus	= '';
		if ($statut == 'envoi_en_cours') {
			$url = generer_url_ecrire('lettres_envoi', 'id_lettre='.$id_lettre, '&');
			lettres_rediriger_javascript($url);
		}
		
		debut_page($titre, "lettres", "lettres");


		debut_gauche();

		lettres_afficher_numero_lettre($id_lettre, true);
	  	afficher_boite_logo('let', 'id_lettre', $id_lettre, _T('lettres:logo_lettre'), _T('logo_survol'), 'lettres_visualisation');
		lettres_afficher_statistiques_lettre_publiee($titre, $id_lettre);

		debut_raccourcis();
		lettres_afficher_raccourci_liste_lettres(_T('lettres:raccourci_retour_liste_lettres'));
		lettres_afficher_raccourci_ajouter_abonne($id_lettre);
		if (@spip_num_rows(spip_query('SELECT * FROM spip_auteurs_lettres WHERE id_lettre="'.$id_lettre.'"')) > 0)
			lettres_afficher_raccourci_tester_envoi($id_lettre);
		lettres_afficher_raccourci_import_csv($id_lettre);
		lettres_afficher_raccourci_export_csv($id_lettre);
		lettres_afficher_raccourci_formulaire_inscription();
		fin_raccourcis();


    	debut_droite();
		debut_cadre_relief('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre-24.png');

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
		icone(_T('lettres:modifier_lettre'), generer_url_ecrire("lettres_edition","id_lettre=$id_lettre"), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre-24.png', "edit.gif");
		echo "</td>";
		echo "</tr>\n";
		if (strlen($descriptif) > 1) {
			echo "<tr><td>\n";
			echo "<div align='$spip_lang_left' style='padding: 5px; border: 1px dashed #aaaaaa;'>";
			echo "<font size=2 face='Verdana,Arial,Sans,sans-serif'>";
			echo $descriptif;
			echo "</font>";
			echo "</div></td></tr>\n";
		}
		echo "</table>\n";

		echo "<div>&nbsp;</div>";


		echo generer_url_post_ecrire("lettres_visualisation", "id_lettre=$id_lettre", 'formulaire');

		lettres_afficher_date($date, true);
		lettres_afficher_auteurs($id_lettre, true);
		lettres_afficher_mots_cles($id_lettre, true);
		lettres_afficher_langue($lang, true);
		
		echo "<br />";
		debut_cadre_relief();
		echo "<center><B>"._T('lettres:etat')."</B>&nbsp;";
		echo "<SELECT NAME='statut' SIZE='1' CLASS='fondl'>\n";
		echo '	<OPTION VALUE="brouillon"'.(($statut == 'brouillon') ? ' SELECTED' : '').'>'._T('lettres:action_brouillon').'</OPTION>'."\n";
		echo '	<OPTION VALUE="publie"'.(($statut == 'publie') ? ' SELECTED' : '').'>'._T('lettres:action_publie').'</OPTION>'."\n";
		$resultat_nb_abonnes = spip_query('SELECT id_abonne FROM spip_abonnes_lettres WHERE id_lettre="'.$id_lettre.'" AND statut="valide"');
		if (@spip_num_rows($resultat_nb_abonnes) > 0) {
			echo '	<OPTION VALUE="envoi_en_cours"'.(($statut == 'envoi_en_cours') ? ' SELECTED' : '').'>'._T('lettres:action_a_envoyer').'</OPTION>'."\n";
			echo '	<OPTION VALUE="purger">'._T('lettres:action_purger').'</OPTION>'."\n";
		}
		echo '	<OPTION VALUE="poubelle">'._T('lettres:action_poubelle').'</OPTION>'."\n";
		echo "</SELECT>";
		echo "&nbsp;&nbsp;<INPUT TYPE='submit' NAME='changer_statut' CLASS='fondo' VALUE='"._T('lettres:changer')."' STYLE='font-size:10px'>";
		echo '</center>';
		fin_cadre_relief();

		echo '</form>';


		echo "<div align='justify' style='padding: 10px;'>";
		echo "<div $dir_lang>";
		echo $texte;
		echo "\n\n<div align='$spip_lang_right'><br />";
		icone(_T('lettres:modifier_lettre'), generer_url_ecrire("lettres_edition","id_lettre=$id_lettre"), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre-24.png', "edit.gif");
		echo "</div>";
		echo "<br clear='both' />";
		echo "</div>";
		echo "</div>";
		fin_cadre_relief();

		echo '<br/>';

		echo lettres_afficher_abonnes(_T('lettres:abonnes'), _DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonnes.png', '= "valide"', '', $id_lettre, 'lettres_visualisation', '&id_lettre='.$id_lettre, 'position_abonnes');

		echo lettres_afficher_archives(_T('lettres:archives'), _DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/archives.png', $id_lettre);

		fin_page();

	}

?>