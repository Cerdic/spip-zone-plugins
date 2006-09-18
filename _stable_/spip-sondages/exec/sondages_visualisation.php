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


 	include_spip('inc/presentation');
	include_spip('inc/date');
	include_spip('inc/logos');
	include_spip('inc/extra');
	include_spip('sondages_fonctions');


	/**
	 * exec_sondages_visualisation
	 *
	 * Visualisation d'un sondage
	 *
	 * @author Pierre Basson
	 **/
	function exec_sondages_visualisation() {
		global $dir_lang, $spip_lang_right, $champs_extra;

		if ($GLOBALS['connect_statut'] != "0minirezo") {
			$url = generer_url_ecrire('accueil');
			echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
			exit();
		}

		if (empty($_GET['id_sondage'])) {
			$url = generer_url_ecrire('sondages', '', '&');
			echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
			exit();
		}

		if (!empty($_POST['enregistrer'])) {
			$id_sondage		= $_GET['id_sondage'];
			$titre			= addslashes($_POST['titre']);
			$id_rubrique	= addslashes($_POST['id_parent']);
			$type			= addslashes($_POST['type']);
			$texte			= addslashes($_POST['texte']);
			$lang			= addslashes($_POST['lang']);
			if ($champs_extra)
				$champs_extra = extra_recup_saisie("sondages");

			if ($id_sondage == -1) {
				$insertion = 'INSERT INTO spip_sondages (titre, 
														id_rubrique, 
														type, 
														texte, 
														lang, 
														en_ligne, 
														statut,
														date_debut,
														date_fin, 
														maj'.($champs_extra ? ", extra" : '').') 
												VALUES ("'.$titre.'", 
														"'.$id_rubrique.'", 
														"'.$type.'", 
														"'.$texte.'", 
														"'.$lang.'", 
														"non",
														"en_attente", 
														NOW(), 
														NOW(), 
														NOW()'.($champs_extra ? (', "'.addslashes($champs_extra).'"') : '').')';
				if (spip_query($insertion)) {
					$id_sondage = spip_insert_id();
					spip_query('INSERT INTO spip_auteurs_sondages (id_auteur, id_sondage) VALUES ("'.$GLOBALS['auteur_session']['id_auteur'].'", "'.$id_sondage.'")');
					$url_sondage = generer_url_ecrire('sondages_visualisation', 'id_sondage='.$id_sondage, '&');
					echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url_sondage.'");</script>';
					exit();
				}
			} else {
				$modification = 'UPDATE spip_sondages SET titre="'.$titre.'", 
														id_rubrique="'.$id_rubrique.'", 
														type="'.$type.'", 
														texte="'.$texte.'", 
														maj=NOW()'.($champs_extra ? (", extra='".addslashes($champs_extra)."'") : '').'
													WHERE id_sondage="'.$id_sondage.'"';
				spip_query($modification);
				$url_sondage = generer_url_ecrire('sondages_visualisation', 'id_sondage='.$id_sondage, '&');
				echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url_sondage.'");</script>';
				exit();
			}
		}

		if (!empty($_POST['enregistrer_choix'])) {
			$id_sondage		= $_GET['id_sondage'];
			$id_choix		= $_GET['id_choix'];
			$titre			= addslashes($_POST['titre']);
			$position		= intval($_POST['position']);

			if ($id_choix == -1) {
				$resultat_nb_choix = spip_query('SELECT id_choix FROM spip_choix WHERE id_sondage="'.$id_sondage.'"');
				$ordre = intval(spip_num_rows($resultat_nb_choix));
				$insertion = 'INSERT INTO spip_choix (titre, 
														id_sondage, 
														ordre) 
												VALUES ("'.$titre.'", 
														"'.$id_sondage.'", 
														"'.$ordre.'")';
				if (spip_query($insertion)) {
					$id_choix = spip_insert_id();
					sondages_modifier_ordre_choix($id_sondage, $id_choix, $position);
				}
			} else {
				$modification = 'UPDATE spip_choix 
								SET titre="'.$titre.'" 
								WHERE id_sondage="'.$id_sondage.'"
									AND id_choix="'.$id_choix.'"';
				spip_query($modification);
				sondages_modifier_ordre_choix($id_sondage, $id_choix, $position);
			}
			$url = generer_url_ecrire('sondages_visualisation', 'id_sondage='.$id_sondage, '&');
			echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
			exit();
		}

		$id_sondage	= $_GET['id_sondage'];
		$url = generer_url_ecrire('sondages_visualisation', 'id_sondage='.$id_sondage, '&');

		if (!empty($_GET['supprimer_auteur'])) {
			$id_auteur = intval($_GET['supprimer_auteur']);
			$suppression = 'DELETE FROM spip_auteurs_sondages WHERE id_auteur="'.$id_auteur.'" AND id_sondage="'.$id_sondage.'" LIMIT 1';
			spip_query($suppression);
			echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
			exit();
		}

		if (!empty($_POST['changer_auteur'])) {
			$id_auteur = intval($_POST['id_auteur']);
			$insertion = 'INSERT INTO spip_auteurs_sondages (id_auteur, id_sondage) VALUES ("'.$id_auteur.'", "'.$id_sondage.'")';
			@spip_query($insertion);
			echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
			exit();
		}

		if (!empty($_GET['supprimer_mot'])) {
			$id_mot = intval($_GET['supprimer_mot']);
			$suppression = 'DELETE FROM spip_mots_sondages WHERE id_mot="'.$id_mot.'" AND id_sondage="'.$id_sondage.'" LIMIT 1';
			spip_query($suppression);
			echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
			exit();
		}

		if (!empty($_POST['changer_mot'])) {
			$id_mot = intval($_POST['id_mot']);
			$insertion = 'INSERT INTO spip_mots_sondages (id_mot, id_sondage) VALUES ("'.$id_mot.'", "'.$id_sondage.'")';
			@spip_query($insertion);
			echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
			exit();
		}

		if (!empty($_POST['changer_dates'])) {
			$annee_debut	= $_POST['annee_debut'];
			$mois_debut		= $_POST['mois_debut'];
			$jour_debut		= $_POST['jour_debut'];
			$date_debut		= $annee_debut.'-'.$mois_debut.'-'.$jour_debut.' 00:00:00';
			$modification = 'UPDATE spip_sondages SET date_debut="'.$date_debut.'", maj=NOW() WHERE id_sondage="'.$id_sondage.'"';
			spip_query($modification);

			$annee_fin	= $_POST['annee_fin'];
			$mois_fin	= $_POST['mois_fin'];
			$jour_fin	= $_POST['jour_fin'];
			$date_fin	= $annee_fin.'-'.$mois_fin.'-'.$jour_fin.' 23:59:00';
			$modification = 'UPDATE spip_sondages SET date_fin="'.$date_fin.'", maj=NOW() WHERE id_sondage="'.$id_sondage.'"';
			spip_query($modification);

			sondages_mettre_a_jour_sondage($id_sondage);
			echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
			exit();
		}

		if (!empty($_POST['changer_langue'])) {
			$lang	= addslashes($_POST['lang']);
			$modification = 'UPDATE spip_sondages SET lang="'.$lang.'", maj=NOW() WHERE id_sondage="'.$id_sondage.'"';
			spip_query($modification);
			echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
			exit();
		}

		if (!empty($_POST['changer_en_ligne'])) {
			$en_ligne = $_POST['en_ligne'];
			if ($en_ligne == 'purger_avis') {
				$requete_sondes = 'SELECT id_sonde FROM spip_sondes WHERE id_sondage="'.$id_sondage.'"';
				$resultat_sondes = spip_query($requete_sondes);
				while ($arr = spip_fetch_array($resultat_sondes)) {
					spip_query('DELETE FROM spip_avis WHERE id_sonde="'.$arr['id_sonde'].'"');
				}
				$suppression = 'DELETE FROM spip_sondes WHERE id_sondage="'.$id_sondage.'"';
				spip_query($suppression);
			} else if ($en_ligne == 'poubelle') {
				$suppression = 'DELETE FROM spip_sondages WHERE id_sondage="'.$id_sondage.'" LIMIT 1';
				spip_query($suppression);
				$suppression_mots = 'DELETE FROM spip_mots_sondages WHERE id_sondage="'.$id_sondage.'"';
				spip_query($suppression_mots);
				$suppression_auteurs = 'DELETE FROM spip_auteurs_sondages WHERE id_sondage="'.$id_sondage.'"';
				spip_query($suppression_auteurs);
				$requete_sondes = 'SELECT id_sonde FROM spip_sondes WHERE id_sondage="'.$id_sondage.'"';
				$resultat_sondes = spip_query($requete_sondes);
				while ($arr = spip_fetch_array($resultat_sondes)) {
					spip_query('DELETE FROM spip_avis WHERE id_sonde="'.$arr['id_sonde'].'"');
				}
				$suppression = 'DELETE FROM spip_sondes WHERE id_sondage="'.$id_sondage.'"';
				spip_query($suppression);
				$suppression = 'DELETE FROM spip_choix WHERE id_sondage="'.$id_sondage.'"';
				spip_query($suppression);
				// suppression logos
				$logo_on = array();
				$logo_on = cherche_logo($id_sondage, 'son', 'on');
				if (!empty($logo_on))
					unlink($logo_on[0]);
				$logo_off = array();
				$logo_off = cherche_logo($id_sondage, 'son', 'off');
				if (!empty($logo_off))
					unlink($logo_off[0]);
				// suppression documents et vignettes
				$documents = spip_query('SELECT D.id_document, 
												D.id_vignette, 
												D.fichier
											FROM spip_documents_sondages AS DS 
											INNER JOIN spip_documents AS D ON D.id_document=DS.id_document 
											WHERE DS.id_sondage="'.$id_sondage.'"');
				while ($arr = spip_fetch_array($documents)) {
					if ($arr['id_vignette'] != 0) {
						$vignette = spip_query('SELECT fichier FROM spip_documents WHERE id_document="'.$arr['id_vignette'].'"');
						list($fichier_vignette) = spip_fetch_array($vignette,SPIP_NUM);
						unlink('../'.$fichier_vignette);
						spip_query('DELETE FROM spip_documents WHERE id_document="'.$arr['id_vignette'].'"');
					}
					unlink('../'.$arr['fichier']);
					spip_query('DELETE FROM spip_documents WHERE id_document="'.$arr['id_document'].'"');
				}
				$url = generer_url_ecrire('sondages');
				echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
				exit();
			} else {
				spip_query('UPDATE spip_sondages SET en_ligne="'.$en_ligne.'" WHERE id_sondage="'.$id_sondage.'"');
				sondages_mettre_a_jour_sondage($id_sondage);
				echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
				exit();
			}
		}
		
		if (isset($_GET['position'])) {
			$id_sondage		= $_GET['id_sondage'];
			$id_choix		= $_GET['id_choix'];
			$position		= intval($_GET['position']);
			sondages_modifier_ordre_choix($id_sondage, $id_choix, $position);
			echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
			exit();
		}

		if (!empty($_GET['supprimer_choix'])) {
			$id_choix	= intval($_GET['supprimer_choix']);
			$id_sondage	= $_GET['id_sondage'];
			sondages_modifier_ordre_choix($id_sondage, $id_choix, 'dernier');
			$suppression = 'DELETE FROM spip_avis WHERE id_choix="'.$id_choix.'"';
			spip_query($suppression);
			$suppression = 'DELETE FROM spip_choix WHERE id_choix="'.$id_choix.'" AND id_sondage="'.$id_sondage.'" LIMIT 1';
			spip_query($suppression);
			echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
			exit();
		}

		$requete_sondage = 'SELECT titre, id_rubrique, type, texte, lang, en_ligne, statut, date_debut, date_fin, extra FROM spip_sondages WHERE id_sondage="'.$id_sondage.'" LIMIT 1';
		$resultat_sondage = spip_query($requete_sondage);
		list($titre, $id_rubrique, $type, $texte, $lang, $en_ligne, $statut, $date_debut, $date_fin, $extra) = spip_fetch_array($resultat_sondage,SPIP_NUM);
		$titre		= entites_html($titre);
		$texte		= propre($texte);
		
		
		debut_page($titre, "naviguer", "sondages");


		debut_grand_cadre();
		afficher_hierarchie($id_rubrique);
		fin_grand_cadre();


		debut_gauche();
		sondages_afficher_numero_sondage($id_sondage, true);
		global $table_logos;
		$table_logos['id_sondage'] = 'son';
	  	afficher_boite_logo('id_sondage', $id_sondage, _T('sondages:logo_sondage'), _T('logo_survol'), 'sondages_visualisation');

		debut_raccourcis();
		sondages_afficher_raccourci_liste_sondages();
		fin_raccourcis();


    	debut_droite();
		debut_cadre_relief('../'._DIR_PLUGIN_SONDAGES.'/img_pack/sondages-24.png');

		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		switch ($en_ligne) {
			case 'non':
				$logo_statut = "puce-blanche.gif";
				break;
			case 'oui':
				switch ($statut) {
					case 'en_attente' :
						$logo_statut = "puce-orange.gif";
						break;
					case 'publie' :
						$logo_statut = "puce-verte.gif";
						break;
					case 'termine' :
						$logo_statut = "puce-poubelle.gif";
						break;
				}
				break;
		}
		echo "<tr width='100%'><td width='100%' valign='top'>";
		gros_titre($titre, $logo_statut);
		echo "</td>";
		echo "<td>", http_img_pack("rien.gif", ' ', "width='5'") ."</td>\n";
		echo "<td  align='$spip_lang_right' valign='top'>";
		icone(_T('sondages:modifier_sondage'), generer_url_ecrire("sondages_edition","id_sondage=$id_sondage"), '../'._DIR_PLUGIN_SONDAGES.'/img_pack/sondages-24.png', "edit.gif");
		echo "</td>";
		echo "</tr>\n";
		echo "<tr><td>\n";
		echo "<div align='$spip_lang_left' style='padding: 5px; border: 1px dashed #aaaaaa;'>";
		echo "<font size=2 face='Verdana,Arial,Sans,sans-serif'>";
		echo _T('sondages:type')." : <B>"._T('sondages:'.$type)."</B><br />";
		echo "</font>";
		echo "</div>";
		if ($champs_extra AND $extra) {
			echo "<br />\n";
			extra_affichage($extra, "sondages");
		}
		echo "</td></tr>\n";
		echo "</table>\n";

		echo "<div>&nbsp;</div>";


		echo generer_url_post_ecrire("sondages_visualisation", "id_sondage=$id_sondage", 'formulaire');

		sondages_afficher_dates($date_debut, $date_fin, true);

		debut_cadre_enfonce('../'._DIR_PLUGIN_SONDAGES.'/img_pack/choix.png', false, '', _T('sondages:choix'));
		$requete_choix = 'SELECT * FROM spip_choix WHERE id_sondage="'.$id_sondage.'" ORDER BY ordre';
		$resultat_choix = spip_query($requete_choix);
		$total = spip_num_rows($resultat_choix);
		if ($type == 'multiple')
			$icone = "checkbox";
		else
			$icone = "radio";
		if ($total != 0) {
			$position = 0;
			echo "<div class='liste'>\n";
			echo "<table width='100%' cellpadding='3' cellspacing='0' border='0' background=''>\n";
			while ($arr = spip_fetch_array($resultat_choix)) {
				echo "<tr class='tr_liste'>\n";
				echo "<td width='20' class='arial11'>\n";
				echo "<img src='"._DIR_PLUGIN_SONDAGES."/img_pack/".$icone.".png' alt='radio' width='16' height='16' border='0' />\n";
				echo "</td>\n";
				echo "<td class='arial2' width='135'>\n";
				echo "<A HREF='".generer_url_ecrire("choix_edition","id_sondage=$id_sondage&id_choix=".$arr['id_choix'])."'>\n";
				echo $arr['titre'];
				echo "</A>\n";
				echo "</td>\n";
				echo "<td class='arial1' width='50' align='right'>\n";
				$requete_nb_votes = 'SELECT id_avis FROM spip_avis WHERE id_choix="'.$arr['id_choix'].'"';
				$resultat_nb_votes = spip_query($requete_nb_votes);
				$nb_votes = intval(spip_num_rows($resultat_nb_votes));
				echo $nb_votes.'&nbsp;'._T('sondages:votes');
				echo "</td>\n";
				echo "<td class='arial1' width='140'>\n";
				$pourcentage = sondages_calculer_pourcentage($id_sondage, $arr['id_choix']);
				echo "<img src='img_pack/jauge-vert.gif' alt='monter' title='".$pourcentage."%' width='".$pourcentage."' height='8' border='0' />&nbsp;".$pourcentage."%\n";
				echo "</td>\n";
				echo "<td class='arial2' width='24'>\n";
				if ($position == 0) {
					echo "&nbsp;";
				} else {
					echo "<A HREF='".generer_url_ecrire("sondages_visualisation","id_sondage=$id_sondage&id_choix=".$arr['id_choix']."&position=".($position-1))."'>\n";
					echo "<img src='img_pack/monter-16.png' alt='monter' width='16' height='16' border='0' />\n";
					echo "</A>\n";
				}
				echo "</td>\n";
				echo "<td class='arial2' width='24'>\n";
				if ($position == ($total-1)) {
					echo '&nbsp;';
				} else {
					echo "<A HREF='".generer_url_ecrire("sondages_visualisation","id_sondage=$id_sondage&id_choix=".$arr['id_choix']."&position=".($position+1))."'>\n";
					echo "<img src='img_pack/descendre-16.png' alt='descendre' width='16' height='16' border='0' />\n";
					echo "</A>\n";
				}
				echo "</td>\n";
				echo "<td class='arial1' width='24'>\n";
				echo "<A HREF='".generer_url_ecrire("sondages_visualisation","id_sondage=$id_sondage&supprimer_choix=".$arr['id_choix'])."'>\n";
				echo "<img src='"._DIR_PLUGIN_SONDAGES."/img_pack/poubelle.png' alt='X' width='16' height='16' border='0' align='middle' />\n";
				echo "</A>\n";
				echo "</td>\n";
				echo "</tr>\n";
				$position++;
			}
			echo "</table>\n";
			echo "</div>\n";

			echo "\n<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
			echo "<tr>";
			echo "<td>";
			echo "<div align='$spip_lang_right'>";
			icone(_T('sondages:ajouter_un_choix'), generer_url_ecrire("choix_edition","id_sondage=$id_sondage&new=oui"), '../'._DIR_PLUGIN_SONDAGES.'/img_pack/choix.png', "creer.gif");
			echo "</div>";
			echo "</td></tr></table>";
		}
		fin_cadre_enfonce();

		sondages_afficher_auteurs($id_sondage, true);
		sondages_afficher_mots_cles($id_sondage, true);
		sondages_afficher_langue($lang, true);
		
		echo "<br />";
		debut_cadre_relief();
		echo "<center><B>"._T('sondages:action')."</B>&nbsp;";
		echo "<SELECT NAME='en_ligne' SIZE='1' CLASS='fondl'>\n";
		echo '	<OPTION VALUE="non"'.(($en_ligne == 'non') ? ' SELECTED' : '').'>'._T('sondages:action_hors_ligne').'</OPTION>'."\n";
		echo '	<OPTION VALUE="oui"'.(($en_ligne == 'oui') ? ' SELECTED' : '').'>'._T('sondages:action_en_ligne').'</OPTION>'."\n";
		echo '	<OPTION VALUE="purger_avis">'._T('sondages:action_purger_avis').'</OPTION>'."\n";
		echo '	<OPTION VALUE="poubelle">'._T('sondages:action_poubelle').'</OPTION>'."\n";
		echo "</SELECT>";
		echo "&nbsp;&nbsp;<INPUT TYPE='submit' NAME='changer_en_ligne' CLASS='fondo' VALUE='"._T('sondages:changer')."' STYLE='font-size:10px'>";
		echo '</center>';
		fin_cadre_relief();
		echo '</form>';

		echo "<div align='justify' style='padding: 10px;'>";
		echo "<div $dir_lang>";
		echo $texte;
		echo "\n\n<div align='$spip_lang_right'><br />";
		icone(_T('sondages:modifier_sondage'), generer_url_ecrire("sondages_edition","id_sondage=$id_sondage"), '../'._DIR_PLUGIN_SONDAGES.'/img_pack/sondages-24.png', "edit.gif");
		echo "</div>";
		echo "<br clear='both' />";
		echo "</div>";
		echo "</div>";

		fin_cadre_relief();

		fin_page();

	}

?>