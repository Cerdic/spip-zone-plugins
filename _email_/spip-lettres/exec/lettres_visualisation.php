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
	include_spip('inc/logos');
	include_spip('inc/extra');
	include_spip('inc/distant');


	/**
	 * exec_lettres_visualisation
	 *
	 * Edition d'une nouvelle lettre
	 *
	 * @author Pierre Basson
	 **/
	function exec_lettres_visualisation() {
		global $dir_lang, $spip_lang_right, $champs_extra;

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
			if ($champs_extra)
				$champs_extra = extra_recup_saisie("lettres");

			if ($id_lettre == -1) {
				$insertion = 'INSERT INTO spip_lettres (titre, descriptif, texte, lang, statut, date, maj'.($champs_extra ? ", extra" : '').') VALUES ("'.$titre.'", "'.$descriptif.'", "'.$texte.'", "'.$lang.'", "'.$statut.'", NOW(), NOW()'.($champs_extra ? (', "'.addslashes($champs_extra).'"') : '').')';
				if (spip_query($insertion)) {
					$id_lettre = spip_insert_id();
					spip_query('INSERT INTO spip_auteurs_lettres (id_auteur, id_lettre) VALUES ("'.$GLOBALS['auteur_session']['id_auteur'].'", "'.$id_lettre.'")');
					$url_lettre = generer_url_ecrire('lettres_visualisation', 'id_lettre='.$id_lettre, '&');
					lettres_rediriger_javascript($url_lettre);
				}
			} else {
				$modification = 'UPDATE spip_lettres SET titre="'.$titre.'", descriptif="'.$descriptif.'", texte="'.$texte.'", maj=NOW()'.($champs_extra ? (", extra='".addslashes($champs_extra)."'") : '').' WHERE id_lettre="'.$id_lettre.'"';
				spip_query($modification);
				$url_lettre = generer_url_ecrire('lettres_visualisation', 'id_lettre='.$id_lettre, '&');
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
				$suppression_statistiques = 'DELETE FROM spip_lettres_statistiques WHERE id_lettre="'.$id_lettre.'"';
				spip_query($suppression_statistiques);
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
				// suppression logos
				$logo_on = array();
				$logo_on = cherche_logo($id_lettre, 'let', 'on');
				if (!empty($logo_on))
					unlink($logo_on[0]);
				$logo_off = array();
				$logo_off = cherche_logo($id_lettre, 'let', 'off');
				if (!empty($logo_off))
					unlink($logo_off[0]);
				// suppression documents et vignettes
				$documents = spip_query('SELECT D.id_document, 
												D.id_vignette, 
												D.fichier
											FROM spip_documents_lettres AS DL 
											INNER JOIN spip_documents AS D ON D.id_document=DL.id_document 
											WHERE DL.id_lettre="'.$id_lettre.'"');
				while ($arr = spip_fetch_array($documents)) {
					if ($arr['id_vignette'] != 0) {
						$vignette = spip_query('SELECT fichier FROM spip_documents WHERE id_document="'.$arr['id_vignette'].'"');
						list($fichier_vignette) = spip_fetch_array($vignette);
						unlink('../'.$fichier_vignette);
						spip_query('DELETE FROM spip_documents WHERE id_document="'.$arr['id_vignette'].'"');
					}
					unlink('../'.$arr['fichier']);
					spip_query('DELETE FROM spip_documents WHERE id_document="'.$arr['id_document'].'"');
				}
				$url = generer_url_ecrire('lettres');
				lettres_rediriger_javascript($url);
			} else if ($statut == 'purger') {
				$abonnes = spip_query('SELECT id_abonne FROM spip_abonnes_lettres WHERE id_lettre="'.$id_lettre.'" AND statut="valide"');
				while ($arr = spip_fetch_array($abonnes)) {
					lettres_ajouter_statistique_suppression($id_lettre);
				}
				$suppression_abonnes = 'DELETE FROM spip_abonnes_lettres WHERE id_lettre="'.$id_lettre.'"';
				spip_query($suppression_abonnes);
				$requete_archives = 'SELECT id_archive FROM spip_archives WHERE id_lettre="'.$id_lettre.'"';
				$resultat_archives = spip_query($requete_archives);
				while ($arr = @spip_fetch_array($resultat_archives)) {
					$suppression_abonnes_archives = 'DELETE FROM spip_abonnes_archives WHERE id_archive="'.$arr['id_archive'].'"';
					spip_query($suppression_abonnes_archives);
				}
				lettres_rediriger_javascript($url_lettre);
			} else {
				$modification = 'UPDATE spip_lettres SET statut="'.$statut.'" WHERE id_lettre="'.$id_lettre.'"';
				spip_query($modification);
				if ($statut == 'envoi_en_cours') {
					// Récupération des infos essentielles
					$requete_lettre = 'SELECT titre, date FROM spip_lettres WHERE id_lettre="'.$id_lettre.'" LIMIT 1';
					$resultat_lettre = spip_query($requete_lettre);
					list($titre, $date) = spip_fetch_array($resultat_lettre);

					// Création de l'archive
					$fond_message_html	= $GLOBALS['meta']['fond_message_html'];
					$fond_message_texte	= $GLOBALS['meta']['fond_message_texte'];
					$url_message_html	= generer_url_public($fond_message_html, 'id_lettre='.$id_lettre, '&');
					$url_message_texte	= generer_url_public($fond_message_texte, 'id_lettre='.$id_lettre, '&');
					$message_html	= recuperer_page($url_message_html);
					$message_texte	= recuperer_page($url_message_texte);
					$message_html	= addslashes($message_html);
					$message_texte	= addslashes($message_texte);

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

		$requete_lettre = 'SELECT titre, descriptif, texte, lang, statut, date, extra FROM spip_lettres WHERE id_lettre="'.$id_lettre.'" LIMIT 1';
		$resultat_lettre = spip_query($requete_lettre);
		list($titre, $descriptif, $texte, $lang, $statut, $date, $extra) = spip_fetch_array($resultat_lettre);
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

		if ($statut != 'brouillon') 
			lettres_afficher_numero_lettre($id_lettre, true, true);
		else
			lettres_afficher_numero_lettre($id_lettre, true, false);

		global $table_logos;
		$table_logos['id_lettre'] = 'let';

	  	afficher_boite_logo('id_lettre', $id_lettre, _T('lettres:logo_lettre'), _T('logo_survol'), 'lettres_visualisation');
		lettres_afficher_statistiques_lettre_publiee($titre, $id_lettre);

		debut_raccourcis();
		lettres_afficher_raccourci_liste_lettres(_T('lettres:raccourci_retour_liste_lettres'));
		lettres_afficher_raccourci_ajouter_abonne($id_lettre);
		if (@spip_num_rows(spip_query('SELECT * FROM spip_auteurs_lettres WHERE id_lettre="'.$id_lettre.'"')) > 0 AND $statut != 'brouillon')
			lettres_afficher_raccourci_tester_envoi($id_lettre);
		lettres_afficher_raccourci_import_csv($id_lettre);
		if (lettres_verifier_existence_abonnes($id_lettre))
			lettres_afficher_raccourci_export_csv($id_lettre);
		if (lettres_verifier_existence_abonnes($id_lettre) AND lettres_verifier_existence_plusieurs_lettres())
			lettres_afficher_raccourci_transfert($id_lettre);
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
		echo "<tr><td>\n";
		if (strlen($descriptif) > 1) {
			echo "<div align='$spip_lang_left' style='padding: 5px; border: 1px dashed #aaaaaa;'>";
			echo "<font size=2 face='Verdana,Arial,Sans,sans-serif'>";
			echo $descriptif;
			echo "</font>";
			echo "</div>";
		}
		if ($champs_extra AND $extra) {
			echo "<br />\n";
			extra_affichage($extra, "lettres");
		}
		echo "</td></tr>\n";
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
		if (lettres_verifier_existence_abonnes($id_lettre)) {
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