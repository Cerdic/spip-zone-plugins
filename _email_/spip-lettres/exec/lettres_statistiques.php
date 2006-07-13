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
	 * exec_lettres_statistiques
	 *
	 * Statistiques
	 *
	 * @author Pierre Basson
	 **/
	function exec_lettres_statistiques() {
		global $spip_lang_right, $spip_lang_left;

		lettres_verifier_droits();

		debut_page(_T('lettres:statistiques'), "lettres", "lettres_statistiques");

		if (empty($_GET['id_lettre'])) {

			debut_gauche();

			lettres_afficher_statistiques_globales();

			$lettres = spip_query('SELECT id_lettre, titre, statut FROM spip_lettres WHERE statut IN ("publie", "envoi_en_cours") ORDER BY date DESC');
			if (@spip_num_rows($lettres) > 0) {
				echo "<div class='bandeau_rubriques' style='z-index: 1;'>";
				bandeau_titre_boite2(_T('lettres:statistiques_par_lettre'), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/statistiques-lettres.png');
				echo "<div class='plan-articles'>";
				while($row = spip_fetch_array($lettres)) {
					if ($row['statut'] == 'publie')	$ze_statut = 'publie';
					if ($row['statut'] == 'envoi_en_cours')	$ze_statut = 'prop';
					$numero = "<div class='arial1' style='float: $spip_lang_right; color: black; padding-$spip_lang_left: 4px;'><b>"._T('info_numero_abbreviation').$row['id_lettre']."</b></div>";
					echo "<a class='$ze_statut' style='font-size: 10px;' href='" . generer_url_ecrire("lettres_statistiques",'id_lettre='.$row['id_lettre']) . "'>".$numero.$row['titre'].'</a>';
				}
				echo "</div>";
				echo "</div>";
			}


			debut_raccourcis();
			lettres_afficher_raccourci_liste_lettres(_T('lettres:aller_liste_lettres'));
			lettres_afficher_raccourci_liste_abonnes(_T('lettres:aller_liste_abonnes'));
			lettres_afficher_raccourci_formulaire_inscription();
			lettres_afficher_raccourci_configurer_plugin();
			fin_raccourcis();

			debut_droite();

			$tableau_inscriptions_7j = array();
			$tableau_desinscriptions_7j = array();
			for ($i = 1; $i <= 7; $i++) {
				$timestamp = mktime(0, 0, 0, date("m"), date("d") - (7 - $i), date("Y"));
				$date_mysql = date('Y-m-d', $timestamp);
				$nom_jour = nom_jour(date('Y-m-d h:i:s', $timestamp));
				list($nb_inscriptions) = spip_fetch_array(spip_query('SELECT COUNT(id_lettre) FROM spip_lettres_statistiques WHERE date LIKE "'.$date_mysql.'%" AND type="inscription"'));
				$tableau_inscriptions_7j[$nom_jour] = $nb_inscriptions;
				list($nb_desinscriptions) = spip_fetch_array(spip_query('SELECT COUNT(id_lettre) FROM spip_lettres_statistiques WHERE date LIKE "'.$date_mysql.'%" AND type="desinscription"'));
				$tableau_desinscriptions_7j[$nom_jour] = $nb_desinscriptions;
			}
			
			$tableau_inscriptions_12m = array();
			$tableau_desinscriptions_12m = array();
			for ($i = 1; $i <= 12; $i++) {
				$timestamp = mktime(0, 0, 0, date("m") - (12 - $i), date("d"), date("Y"));
				$date_mysql = date('Y-m', $timestamp);
				$nom_mois = nom_mois(date('Y-m-d h:i:s', $timestamp));
				list($nb_inscriptions) = spip_fetch_array(spip_query('SELECT COUNT(id_lettre) FROM spip_lettres_statistiques WHERE date LIKE "'.$date_mysql.'%" AND type="inscription"'));
				$tableau_inscriptions_12m[$nom_mois] = $nb_inscriptions;
				list($nb_desinscriptions) = spip_fetch_array(spip_query('SELECT COUNT(id_lettre) FROM spip_lettres_statistiques WHERE date LIKE "'.$date_mysql.'%" AND type="desinscription"'));
				$tableau_desinscriptions_12m[$nom_mois] = $nb_desinscriptions;
			}
			
			debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/statistiques-inscriptions.png', false, "", bouton_block_invisible('inscriptions')._T('lettres:statistiques_inscription'));
			echo debut_block_invisible('inscriptions');
			lettres_afficher_histogramme(_T('lettres:derniere_semaine'), $tableau_inscriptions_7j, true);
			lettres_afficher_histogramme(_T('lettres:derniers_mois'), $tableau_inscriptions_12m, true);
			echo fin_block();
			fin_cadre_enfonce();

			debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/statistiques-desinscriptions.png', false, "", bouton_block_invisible('desinscriptions')._T('lettres:statistiques_desinscription'));
			echo debut_block_invisible('desinscriptions');
			lettres_afficher_histogramme(_T('lettres:derniere_semaine'), $tableau_desinscriptions_7j, true);
			lettres_afficher_histogramme(_T('lettres:derniers_mois'), $tableau_desinscriptions_12m, true);
			echo fin_block();
			fin_cadre_enfonce();

			$tableau_archives = array();
			$archives = spip_query('SELECT titre, id_archive, date, nb_emails_envoyes FROM spip_archives ORDER BY id_archive');
			if (@spip_num_rows($archives) > 0) {
				while ($arr = spip_fetch_array($archives)) {
					$date_archive = affdate($arr['date'], 'd/m/Y');
					$legende = '#'.$arr['id_archive'].' - '.$arr['titre'].' - '.$date_archive;
					$tableau_archives[$legende] = $arr['nb_emails_envoyes'];
				}
				debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/statistiques-archives.png', false, "", bouton_block_invisible('archives')._T('lettres:statistiques_archives'));
				echo debut_block_invisible('archives');
				lettres_afficher_histogramme(_T('lettres:au_fil_des_envois'), $tableau_archives, false);
				echo fin_block();
				fin_cadre_enfonce();
			}


		} else {

			$id_lettre = intval($_GET['id_lettre']);
			$requete_lettre = 'SELECT titre, descriptif, texte, lang, statut, date FROM spip_lettres WHERE id_lettre="'.$id_lettre.'" LIMIT 1';
			$resultat_lettre = spip_query($requete_lettre);
			list($titre, $descriptif, $texte, $lang, $statut, $date) = spip_fetch_array($resultat_lettre);
			$titre		= entites_html($titre);
			$descriptif	= propre($descriptif);
			$texte		= propre($texte);
			$onfocus	= '';

			debut_gauche();

			lettres_afficher_numero_lettre($id_lettre, false, false);
			lettres_afficher_statistiques_lettre_publiee($titre, $id_lettre);

			debut_raccourcis();
			lettres_afficher_raccourci_retourner_lettre($id_lettre);
			lettres_afficher_raccourci_liste_lettres(_T('lettres:aller_liste_lettres'));
			lettres_afficher_raccourci_statistiques();
			fin_raccourcis();

			debut_droite();
			debut_cadre_relief('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/statistiques-lettres.png');

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
			icone(_T('lettres:raccourci_retourner_lettre'), generer_url_ecrire("lettres_visualisation","id_lettre=$id_lettre"), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre-24.png');
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

			$tableau_inscriptions_7j = array();
			$tableau_desinscriptions_7j = array();
			for ($i = 1; $i <= 7; $i++) {
				$timestamp = mktime(0, 0, 0, date("m"), date("d") - (7 - $i), date("Y"));
				$date_mysql = date('Y-m-d', $timestamp);
				$nom_jour = nom_jour(date('Y-m-d h:i:s', $timestamp));
				list($nb_inscriptions) = spip_fetch_array(spip_query('SELECT COUNT(id_lettre) FROM spip_lettres_statistiques WHERE id_lettre="'.$id_lettre.'" AND date LIKE "'.$date_mysql.'%" AND type="inscription"'));
				$tableau_inscriptions_7j[$nom_jour] = $nb_inscriptions;
				list($nb_desinscriptions) = spip_fetch_array(spip_query('SELECT COUNT(id_lettre) FROM spip_lettres_statistiques WHERE id_lettre="'.$id_lettre.'" AND date LIKE "'.$date_mysql.'%" AND type="desinscription"'));
				$tableau_desinscriptions_7j[$nom_jour] = $nb_desinscriptions;
			}
			
			$tableau_inscriptions_12m = array();
			$tableau_desinscriptions_12m = array();
			for ($i = 1; $i <= 12; $i++) {
				$timestamp = mktime(0, 0, 0, date("m") - (12 - $i), date("d"), date("Y"));
				$date_mysql = date('Y-m', $timestamp);
				$nom_mois = nom_mois(date('Y-m-d h:i:s', $timestamp));
				list($nb_inscriptions) = spip_fetch_array(spip_query('SELECT COUNT(id_lettre) FROM spip_lettres_statistiques WHERE id_lettre="'.$id_lettre.'" AND date LIKE "'.$date_mysql.'%" AND type="inscription"'));
				$tableau_inscriptions_12m[$nom_mois] = $nb_inscriptions;
				list($nb_desinscriptions) = spip_fetch_array(spip_query('SELECT COUNT(id_lettre) FROM spip_lettres_statistiques WHERE id_lettre="'.$id_lettre.'" AND date LIKE "'.$date_mysql.'%" AND type="desinscription"'));
				$tableau_desinscriptions_12m[$nom_mois] = $nb_desinscriptions;
			}
			
			debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/statistiques-inscriptions.png', false, "", bouton_block_invisible('inscriptions')._T('lettres:statistiques_inscription'));
			echo debut_block_invisible('inscriptions');
			lettres_afficher_histogramme(_T('lettres:derniere_semaine'), $tableau_inscriptions_7j, true);
			lettres_afficher_histogramme(_T('lettres:derniers_mois'), $tableau_inscriptions_12m, true);
			echo fin_block();
			fin_cadre_enfonce();

			debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/statistiques-desinscriptions.png', false, "", bouton_block_invisible('desinscriptions')._T('lettres:statistiques_desinscription'));
			echo debut_block_invisible('desinscriptions');
			lettres_afficher_histogramme(_T('lettres:derniere_semaine'), $tableau_desinscriptions_7j, true);
			lettres_afficher_histogramme(_T('lettres:derniers_mois'), $tableau_desinscriptions_12m, true);
			echo fin_block();
			fin_cadre_enfonce();

			$tableau_archives = array();
			$archives = spip_query('SELECT titre, id_archive, date, nb_emails_envoyes FROM spip_archives WHERE id_lettre="'.$id_lettre.'" ORDER BY id_archive');
			if (@spip_num_rows($archives) > 0) {
				while ($arr = spip_fetch_array($archives)) {
					$date_archive = affdate($arr['date'], 'd/m/Y');
					$legende = '#'.$arr['id_archive'].' - '.$arr['titre'].' - '.$date_archive;
					$tableau_archives[$legende] = $arr['nb_emails_envoyes'];
				}
				debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/statistiques-archives.png', false, "", bouton_block_invisible('archives')._T('lettres:statistiques_archives'));
				echo debut_block_invisible('archives');
				lettres_afficher_histogramme(_T('lettres:au_fil_des_envois'), $tableau_archives, false);
				echo fin_block();
				fin_cadre_enfonce();
			}

			echo "<div>&nbsp;</div>";
			fin_cadre_relief();

		}

		fin_page();

	}


?>