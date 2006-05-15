<?php

	include_spip('inc/lettres_fonctions');
	include_spip('inc/plugin');
	global $pas;
	$pas = 20;


	/**
	 * lettres_verifier_droits
	 *
	 * redirige vers l'accueil si l'auteur n'est pas un admin
	 *
	 * @author Pierre Basson
	 **/
	function lettres_verifier_droits() {
		if ($GLOBALS['connect_statut'] != "0minirezo")
			lettres_rediriger_javascript(generer_url_ecrire('accueil')); 
	}
	
	
	/**
	 * lettres_rediriger_ecrire
	 *
	 * redirige vers une url
	 *
	 * @param string url
	 * @author Pierre Basson
	 **/
	function lettres_rediriger_javascript($url) {
		echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
		exit();
	}


	/**
	 * lettres_afficher_lettres
	 *
	 * affiche la la liste des lettres d'information selon un statut donné
	 *
	 * @param string titre
	 * @param string image
	 * @param string statut
	 * @param string recherche
	 * @param string nom_position
	 * @return string la liste des lettres pour le statut demandé
	 * @author Pierre Basson
	 **/
	function lettres_afficher_lettres($titre, $image, $statut, $recherche='', $nom_position='position') {
		global $pas;
		$position = intval($_GET[$nom_position]);

		$clause_where = '';
		if (!empty($recherche)) {
			$recherche = addslashes($recherche);
			$clause_where.= ' AND ( titre LIKE "%'.$recherche.'%"  OR  descriptif LIKE "%'.$recherche.'%"  OR  texte LIKE "%'.$recherche.'%" )';
		}
	
		$lettres = '';

		$requete_lettres = 'SELECT id_lettre,
								titre,
								date,
								lang
							FROM spip_lettres
							WHERE statut="'.$statut.'" '.$clause_where.'
							ORDER BY date DESC
							LIMIT '.$position.','.$pas.'';
		$resultat_lettres = spip_query($requete_lettres);
		if (@spip_num_rows($resultat_lettres) > 0) {

			$lettres.= "<div class='liste'>\n";
			$lettres.= "<div style='position: relative;'>\n";
			$lettres.= "<div style='position: absolute; top: -12px; left: 3px;'>\n";
			$lettres.= "<img src='".$image."'  />\n";
			$lettres.= "</div>\n";
			$lettres.= "<div style='background-color: white; color: black; padding: 3px; padding-left: 30px; border-bottom: 1px solid #444444;' class='verdana2'>\n";
			$lettres.= "<b>\n";
			$lettres.= $titre;
			$lettres.= "</b>\n";
			$lettres.= "</div>\n";
			$lettres.= "</div>\n";
			$lettres.= "<table width='100%' cellpadding='2' cellspacing='0' border='0'>\n";

			while ($lettre = spip_fetch_array($resultat_lettres)) {
				$id_lettre	= $lettre['id_lettre'];
				$titre		= $lettre['titre'];
				$date		= affdate($lettre['date']);
				$nom_langue	= traduire_nom_langue($lettre['lang']);
				$url_lettre	= generer_url_ecrire('lettres_visualisation', 'id_lettre='.$id_lettre);

				$lettres.= "<tr class='tr_liste'>\n";
				$lettres.= "<td width='11'>";
				switch ($statut) {
					case 'brouillon':
						$lettres.= "<img src='img_pack/puce-blanche.gif' alt='puce-blanche' border='0' style='margin: 1px;' />";
						break;
					case 'publie':
						$lettres.= "<img src='img_pack/puce-verte.gif' alt='puce-verte' border='0' style='margin: 1px;' />";
						break;
					case 'envoi_en_cours':
						$lettres.= "<img src='img_pack/puce-orange.gif' alt='puce-orange' border='0' style='margin: 1px;' />";
						break;
				}
				$lettres.= "</td>";
				$lettres.= "<td class='arial2'>\n";
				$lettres.= "<div>\n";
				$lettres.= "<a href=\"".$url_lettre."\" dir='ltr' style='display:block;'>\n";
				$lettres.= $titre;
				if ($GLOBALS['langue_site'] != $lettre['lang']) {
					$lettres.= " <font size='1' color='#666666' dir='ltr'>\n";
					$lettres.= "(".$nom_langue.")\n";
					$lettres.= "</font>\n";
				}
				$lettres.= "</a>\n";
				$lettres.= "</div>\n";
				$lettres.= "</td>\n";
				$lettres.= "<td width='120' class='arial1'>".$date."</td>\n";
				$lettres.= "<td width='50' class='arial1'><b>N&deg;&nbsp;".$id_lettre."</b></td>\n";
				$lettres.= "</tr>\n";

			}
			$lettres.= "</table>\n";
			$requete_total = 'SELECT id_lettre,
									titre,
									date,
									lang
								FROM spip_lettres
								WHERE statut="'.$statut.'" '.$clause_where.'
								ORDER BY date DESC';
			$resultat_total = spip_query($requete_total);
			$total = spip_num_rows($resultat_total);
			$lettres.= lettres_afficher_pagination('lettres', '', $total, $position, $nom_position);
			$lettres.= "</div>\n";
			$lettres.= "<br />\n";
		}
		
		echo $lettres;

	}


	/**
	 * lettres_afficher_archives
	 *
	 * affiche la la liste des archives d'une lettre
	 *
	 * @param string titre
	 * @param string image
	 * @param int id_lettre
	 * @return string la liste des archives pour la lettre demandée
	 * @author Pierre Basson
	 **/
	function lettres_afficher_archives($titre, $image, $id_lettre, $nom_position='position_archives') {
		global $pas;
		$position = intval($_GET[$nom_position]);

		$archives = '';

		$requete_archives = 'SELECT id_archive,
								titre,
								nb_emails_envoyes,
								nb_emails_non_envoyes,
								nb_emails_echec,
								date_fin_envoi
							FROM spip_archives
							WHERE id_lettre="'.$id_lettre.'"
							ORDER BY date_fin_envoi DESC
							LIMIT '.$position.','.$pas.'';
		$resultat_archives = spip_query($requete_archives);
		if (@spip_num_rows($resultat_archives) > 0) {

			$archives.= "<div class='liste'>\n";
			$archives.= "<div style='position: relative;'>\n";
			$archives.= "<div style='position: absolute; top: -12px; left: 3px;'>\n";
			$archives.= "<img src='".$image."'  />\n";
			$archives.= "</div>\n";
			$archives.= "<div style='background-color: white; color: black; padding: 3px; padding-left: 30px; border-bottom: 1px solid #444444;' class='verdana2'>\n";
			$archives.= "<b>\n";
			$archives.= $titre;
			$archives.= "</b>\n";
			$archives.= "</div>\n";
			$archives.= "</div>\n";
			$archives.= "<table width='100%' cellpadding='2' cellspacing='0' border='0'>\n";

			while ($archive = spip_fetch_array($resultat_archives)) {
				$id_archive		= $archive['id_archive'];
				$titre			= $archive['titre'];
				$date			= affdate($archive['date_fin_envoi']);
				$nb_envois		= $archive['nb_emails_envoyes'];
				$url_archive	= generer_url_ecrire('archives_visualisation', 'id_archive='.$id_archive);

				$archives.= "<tr class='tr_liste'>\n";
				$archives.= "<td width='11'>&nbsp;";
				$archives.= "</td>\n";
				$archives.= "<td class='arial2'>\n";
				$archives.= "<div>\n";
				$archives.= "<a href=\"".$url_archive."\" dir='ltr' style='display:block;'>\n";
				$archives.= $titre;
				$archives.= "</a>\n";
				$archives.= "</div>\n";
				$archives.= "</td>\n";
				$archives.= "<td width='80' class='arial1'>".$nb_envois." ";
				if ($nb_envois > 1)	$archives.= _T('lettres:envois');
				else				$archives.= _T('lettres:envoi');
				$archives.= "</td>\n";
				$archives.= "<td width='100' class='arial1'>".$date."</td>\n";
				$archives.= "<td width='50' class='arial1'><b>N&deg;&nbsp;".$id_archive."</b></td>\n";
				$archives.= "</tr>\n";

			}
			$archives.= "</table>\n";

			$requete_archives = 'SELECT id_archive,
									titre,
									date
								FROM spip_archives
								WHERE id_lettre="'.$id_lettre.'"
								ORDER BY date DESC';
			$resultat_total = spip_query($requete_archives);
			$total = spip_num_rows($resultat_total);
			$archives.= lettres_afficher_pagination('lettres_visualisation', '&id_lettre='.$id_lettre, $total, $position, $nom_position);

			$archives.= "</div>\n";
			$archives.= "<br />\n";
		}
		
		return $archives;

	}

	
	/**
	 * lettres_afficher_archives_abonne
	 *
	 * affiche la la liste des archives qu'un abonne a reçues
	 *
	 * @param string titre
	 * @param string image
	 * @param int id_abonne
	 * @return string la liste des archives pour l'abonné
	 * @author Pierre Basson
	 **/
	function lettres_afficher_archives_abonne($titre, $image, $id_abonne, $nom_position='position_abonne') {
		global $pas;
		$position = intval($_GET[$nom_position]);

		$archives = '';

		$requete_archives = 'SELECT DISTINCT(AR.id_archive),
								AR.titre,
								AAR.format,
								AR.date_fin_envoi
							FROM spip_archives AS AR
							INNER JOIN spip_abonnes_archives AS AAR ON AAR.id_archive=AR.id_archive
							WHERE AAR.id_abonne="'.$id_abonne.'"
								AND AAR.statut="envoye"
							ORDER BY AR.date_fin_envoi DESC
							LIMIT '.$position.','.$pas.'';
		$resultat_archives = spip_query($requete_archives);
		if (@spip_num_rows($resultat_archives) > 0) {

			$archives.= "<div class='liste'>\n";
			$archives.= "<div style='position: relative;'>\n";
			$archives.= "<div style='position: absolute; top: -12px; left: 3px;'>\n";
			$archives.= "<img src='".$image."'  />\n";
			$archives.= "</div>\n";
			$archives.= "<div style='background-color: white; color: black; padding: 3px; padding-left: 30px; border-bottom: 1px solid #444444;' class='verdana2'>\n";
			$archives.= "<b>\n";
			$archives.= $titre;
			$archives.= "</b>\n";
			$archives.= "</div>\n";
			$archives.= "</div>\n";
			$archives.= "<table width='100%' cellpadding='2' cellspacing='0' border='0'>\n";

			while ($archive = spip_fetch_array($resultat_archives)) {
				$id_archive		= $archive['id_archive'];
				$titre			= $archive['titre'];
				$format			= $archive['format'];
				$date			= affdate($archive['date_fin_envoi']);
				$url_archive	= generer_url_ecrire('archives_visualisation', 'id_archive='.$id_archive);

				$archives.= "<tr class='tr_liste'>\n";
				$archives.= "<td width='11'>&nbsp;";
				$archives.= "</td>\n";
				$archives.= "<td class='arial2'>\n";
				$archives.= "<div>\n";
				$archives.= "<a href=\"".$url_archive."\" dir='ltr' style='display:block;'>\n";
				$archives.= $titre;
				$archives.= "</a>\n";
				$archives.= "</div>\n";
				$archives.= "</td>\n";
				$archives.= "<td width='100' class='arial1'>".$date."</td>\n";
				$archives.= "<td width='60' class='arial1'>"._T('lettres:format_'.$format)."</td>\n";
				$archives.= "<td width='50' class='arial1'><b>N&deg;&nbsp;".$id_archive."</b></td>\n";
				$archives.= "</tr>\n";

			}
			$archives.= "</table>\n";

			$requete_archives = 'SELECT DISTINCT(AR.id_archive),
									AR.titre,
									AAR.format,
									AR.date_fin_envoi
								FROM spip_archives AS AR
								INNER JOIN spip_abonnes_archives AS AAR ON AAR.id_archive=AR.id_archive
								WHERE AAR.id_abonne="'.$id_abonne.'"
								ORDER BY AR.date_fin_envoi DESC';
			$resultat_total = spip_query($requete_archives);
			$total = spip_num_rows($resultat_total);
			$archives.= lettres_afficher_pagination('abonnes_visualisation', '&id_abonne='.$id_abonne, $total, $position, $nom_position);

			$archives.= "</div>\n";
			$archives.= "<br />\n";
		}
		
		return $archives;

	}

	
	/**
	 * lettres_afficher_abonnes
	 *
	 * affiche la la liste des abonnés d'une lettre
	 *
	 * @param string titre
	 * @param string image
	 * @param string statut_demande
	 * @param string recherche
	 * @param int id_lettre
	 * @param string fond
	 * @param string arguments
	 * @param string nom_position
	 * @return string la liste des abonnés pour toutes les lettres ou une en particulier
	 * @author Quentin LOUPOT :) Pierre Basson
	 **/
	function lettres_afficher_abonnes($titre, $image, $statut_demande, $recherche='', $id_lettre=0, $fond='abonnes', $arguments='', $nom_position) {
		global $pas;
		$position = intval($_GET[$nom_position]);

		$clause_where = '';
		if ($id_lettre != 0)
			$clause_where.= ' AND AL.id_lettre="'.$id_lettre.'" ';
		if (!empty($recherche)) {
			$recherche = addslashes($recherche);
			$clause_where.= ' AND ( A.email LIKE "%'.$recherche.'%"  OR  A.code LIKE "%'.$recherche.'%" )';
		}

		$abonnes = '';

		$requete_abonnes = 'SELECT DISTINCT(A.id_abonne),
								A.email,
								A.format,
								AL.statut
							FROM spip_abonnes AS A
							LEFT JOIN spip_abonnes_lettres AS AL ON AL.id_abonne=A.id_abonne
							WHERE AL.statut '.$statut_demande.'
								'.$clause_where.'
							ORDER BY AL.statut DESC, A.maj DESC
							LIMIT '.$position.','.$pas.'';

		$resultat_abonnes = spip_query($requete_abonnes);
		if (@spip_num_rows($resultat_abonnes) > 0) {

			$abonnes.= "<div class='liste'>\n";
			$abonnes.= "<div style='position: relative;'>\n";
			$abonnes.= "<div style='position: absolute; top: -12px; left: 3px;'>\n";
			$abonnes.= "<img src='".$image."'  />\n";
			$abonnes.= "</div>\n";
			$abonnes.= "<div style='background-color: white; color: black; padding: 3px; padding-left: 30px; border-bottom: 1px solid #444444;' class='verdana2'>\n";
			$abonnes.= "<b>\n";
			$abonnes.= $titre;
			$abonnes.= "</b>\n";
			$abonnes.= "</div>\n";
			$abonnes.= "</div>\n";
			$abonnes.= "<table width='100%' cellpadding='2' cellspacing='0' border='0'>\n";

			while ($abonne = spip_fetch_array($resultat_abonnes)) {
				$id_abonne		= $abonne['id_abonne'];
				$email			= $abonne['email'];
				$format			= $abonne['format'];
				$statut			= $abonne['statut'];
				$url_abonne		= generer_url_ecrire('abonnes_visualisation', 'id_abonne='.$id_abonne);

				$abonnes.= "<tr class='tr_liste'>\n";
				$abonnes.= "<td width='11'>";
				switch ($statut) {
					case 'a_valider':
						$abonnes.= "<img src='img_pack/puce-blanche.gif' alt='puce-blanche' border='0' style='margin: 1px;' />";
						break;
					case 'valide':
						$abonnes.= "<img src='img_pack/puce-verte.gif' alt='puce-verte' border='0' style='margin: 1px;' />";
						break;
					default:
						$abonnes.= "<img src='img_pack/puce-poubelle.gif' alt='puce-poubelle' border='0' style='margin: 1px;' />";
						break;
				}
				$abonnes.= "</td>";
				$abonnes.= "<td class='arial2'>\n";
				$abonnes.= "<div>\n";
				$abonnes.= "<a href=\"".$url_abonne."\" dir='ltr' style='display:block;'>\n";
				$abonnes.= $email;
				$abonnes.= "</a>\n";
				$abonnes.= "</div>\n";
				$abonnes.= "</td>\n";
				if ($statut == 'valide') {
					$resultat_inscriptions = spip_query('SELECT id_abonne FROM spip_abonnes_lettres WHERE id_abonne="'.$id_abonne.'"');
					$total_inscriptions = spip_num_rows($resultat_inscriptions);
					if ($total_inscriptions == 1)
						$abonnes.= "<td width='90' class='arial1'>".$total_inscriptions." "._T('lettres:inscription')."</td>\n";
					else
						$abonnes.= "<td width='90' class='arial1'>".$total_inscriptions." "._T('lettres:inscriptions')."</td>\n";
				}
				$abonnes.= "<td width='60' class='arial1'>"._T('lettres:format_'.$format)."</td>\n";
				$abonnes.= "<td width='50' class='arial1'><b>N&deg;&nbsp;".$id_abonne."</b></td>\n";
				$abonnes.= "</tr>\n";

			}
			$abonnes.= "</table>\n";
			$requete_total = 'SELECT DISTINCT(A.id_abonne),
									A.email,
									A.format,
									AL.statut
								FROM spip_abonnes AS A
								LEFT JOIN spip_abonnes_lettres AS AL ON AL.id_abonne=A.id_abonne
								WHERE AL.statut '.$statut_demande.'
									'.$clause_where.'
								ORDER BY AL.statut DESC, A.maj DESC';
			$resultat_total = spip_query($requete_total);
			$total = spip_num_rows($resultat_total);
			$abonnes.= lettres_afficher_pagination($fond, $arguments, $total, $position, $nom_position);
			$abonnes.= "</div>\n";
			$abonnes.= "<br />\n";
		}
		
		return $abonnes;

	}	
	

	/**
	 * lettres_afficher_abonnes_archive
	 *
	 * affiche la la liste des abonnés ayant reçu l'archive
	 *
	 * @param string titre
	 * @param string image
	 * @param int id_archive
	 * @param string fond
	 * @param string arguments
	 * @param string nom_position
	 * @return string la liste des abonnés pour toutes les lettres ou une en particulier
	 * @author Quentin LOUPOT :) Pierre Basson
	 **/
	function lettres_afficher_abonnes_archive($titre, $image, $id_archive, $fond='abonnes_archives', $arguments='', $nom_position='position_abonnes_archive') {
		global $pas;
		$position = intval($_GET[$nom_position]);

		$abonnes = '';

		$requete_abonnes = 'SELECT DISTINCT(A.id_abonne),
								A.email,
								AL.statut AS statut,
								AA.format AS format,
								AA.statut AS resultat
							FROM spip_abonnes AS A
							INNER JOIN spip_abonnes_archives AS AA ON A.id_abonne=AA.id_abonne
							LEFT JOIN spip_abonnes_lettres AS AL ON AL.id_abonne=A.id_abonne
							WHERE AL.statut="valide"
								AND AA.id_archive="'.$id_archive.'"
							ORDER BY A.maj DESC
							LIMIT '.$position.','.$pas.'';

		$resultat_abonnes = spip_query($requete_abonnes);
		if (@spip_num_rows($resultat_abonnes) > 0) {

			$abonnes.= "<div class='liste'>\n";
			$abonnes.= "<div style='position: relative;'>\n";
			$abonnes.= "<div style='position: absolute; top: -12px; left: 3px;'>\n";
			$abonnes.= "<img src='".$image."'  />\n";
			$abonnes.= "</div>\n";
			$abonnes.= "<div style='background-color: white; color: black; padding: 3px; padding-left: 30px; border-bottom: 1px solid #444444;' class='verdana2'>\n";
			$abonnes.= "<b>\n";
			$abonnes.= $titre;
			$abonnes.= "</b>\n";
			$abonnes.= "</div>\n";
			$abonnes.= "</div>\n";
			$abonnes.= "<table width='100%' cellpadding='2' cellspacing='0' border='0'>\n";

			while ($abonne = spip_fetch_array($resultat_abonnes)) {
				$id_abonne		= $abonne['id_abonne'];
				$email			= $abonne['email'];
				$format			= $abonne['format'];
				$statut			= $abonne['statut'];
				$resultat		= $abonne['resultat'];
				$url_abonne		= generer_url_ecrire('abonnes_visualisation', 'id_abonne='.$id_abonne);

				$abonnes.= "<tr class='tr_liste'>\n";
				$abonnes.= "<td width='11'>";
				switch ($resultat) {
					case 'a_envoyer':
						$abonnes.= "<img src='img_pack/puce-blanche.gif' alt='puce-blanche' border='0' style='margin: 1px;' />";
						break;
					case 'envoye':
						$abonnes.= "<img src='img_pack/puce-verte.gif' alt='puce-verte' border='0' style='margin: 1px;' />";
						break;
					case 'echec':
						$abonnes.= "<img src='img_pack/puce-rouge.gif' alt='puce-rouge' border='0' style='margin: 1px;' />";
						break;
				}
				$abonnes.= "</td>";
				$abonnes.= "<td class='arial2'>\n";
				$abonnes.= "<div>\n";
				$abonnes.= "<a href=\"".$url_abonne."\" dir='ltr' style='display:block;'>\n";
				$abonnes.= $email;
				$abonnes.= "</a>\n";
				$abonnes.= "</div>\n";
				$abonnes.= "</td>\n";
				$abonnes.= "<td width='130' class='arial1'>"._T('lettres:resultat_'.$resultat)."</td>\n";
				if ($resultat ==  'envoye')
					$abonnes.= "<td width='60' class='arial1'>"._T('lettres:format_'.$format)."</td>\n";
				else
					$abonnes.= "<td width='60' class='arial1'>&nbsp;</td>\n";
				$abonnes.= "<td width='50' class='arial1'><b>N&deg;&nbsp;".$id_abonne."</b></td>\n";
				$abonnes.= "</tr>\n";

			}
			$abonnes.= "</table>\n";
			$requete_total = 'SELECT DISTINCT(A.id_abonne),
								A.email,
								A.format,
								AL.statut AS statut,
								AA.statut AS resultat
							FROM spip_abonnes AS A
							INNER JOIN spip_abonnes_archives AS AA ON A.id_abonne=AA.id_abonne
							LEFT JOIN spip_abonnes_lettres AS AL ON AL.id_abonne=A.id_abonne
							WHERE AL.statut="valide"
								AND AA.id_archive="'.$id_archive.'"
							ORDER BY A.maj DESC';
			$resultat_total = spip_query($requete_total);
			$total = spip_num_rows($resultat_total);
			$abonnes.= lettres_afficher_pagination($fond, $arguments, $total, $position, $nom_position);
			$abonnes.= "</div>\n";
			$abonnes.= "<br />\n";
		}
		
		return $abonnes;

	}	
	

	/**
	 * lettres_afficher_statistiques_globales
	 *
	 * @return string un cadre avec qq stats
	 * @author Pierre Basson
	 **/
	function lettres_afficher_statistiques_globales() {
		$info_plugin_lettres = plugin_get_infos('lettres');

		$requete_nb_inscrits = 'SELECT A.id_abonne 
								FROM spip_abonnes AS A
								INNER JOIN spip_abonnes_lettres AS AL ON AL.id_abonne=A.id_abonne
								WHERE AL.statut="valide"
								GROUP BY A.id_abonne';
		$nb_inscrits = @spip_num_rows(spip_query($requete_nb_inscrits));
		
		$requete_nb_lettres_brouillon = 'SELECT L.id_lettre
										FROM spip_lettres AS L
										WHERE L.statut="brouillon"';
		$nb_lettres_brouillon = @spip_num_rows(spip_query($requete_nb_lettres_brouillon));

		$requete_nb_lettres_publiees = 'SELECT L.id_lettre
										FROM spip_lettres AS L
										WHERE L.statut IN ("publie", "envoi_en_cours")';
		$nb_lettres_publiees = @spip_num_rows(spip_query($requete_nb_lettres_publiees));

		$requete_nb_lettres_envoi_en_cours = 'SELECT L.id_lettre
												FROM spip_lettres AS L
												WHERE L.statut="envoi_en_cours"';
		$nb_lettres_envoi_en_cours = @spip_num_rows(spip_query($requete_nb_lettres_envoi_en_cours));

		$cadre = '';
		$cadre.= debut_cadre_relief("plugin-24.png", true, "", _T('lettres:lettres_information'));
		$cadre.= "<div class='verdana1'>";
		$cadre.= "<b>"._T('lettres:plugin')."</b>";
		$cadre.= "<ul style='margin:0px; padding-$spip_lang_left: 20px; margin-bottom: 5px;'>";
		$cadre.= "<li>"._T("lettres:plugin_version")."&nbsp;: <b>".$info_plugin_lettres['version'].'</b></li>';
		$cadre.= "</ul>";
		$cadre.= "<br />";
		if ($nb_inscrits) {
			$cadre.= afficher_plus(generer_url_ecrire("abonnes",""))."<b>"._T('lettres:abonnes')."</b>";
			$cadre.= "<ul style='margin:0px; padding-$spip_lang_left: 20px; margin-bottom: 5px;'>";
			$cadre.= "<li>"._T("lettres:nb_inscrits")."&nbsp;: <b>".$nb_inscrits.'</b></li>';
			$cadre.= "</ul>";
		}
		if ($nb_lettres_brouillon OR $nb_lettres_publiees OR $nb_lettres_envoi_en_cours) {
			$cadre.= "<b>"._T('lettres:lettres')."</b>";
			$cadre.= "<ul style='margin:0px; padding-$spip_lang_left: 20px; margin-bottom: 5px;'>";
			if ($nb_lettres_brouillon)		$cadre.= "<li>"._T("lettres:nb_lettres_brouillon")."&nbsp;: <b>".$nb_lettres_brouillon.'</b></li>';
			if ($nb_lettres_envoi_en_cours)	$cadre.= "<li>"._T("lettres:nb_lettres_envoi_en_cours")."&nbsp;: <b>".$nb_lettres_envoi_en_cours.'</b></li>';
			if ($nb_lettres_publiees)		$cadre.= "<li>"._T("lettres:nb_lettres_publiees")."&nbsp;: <b>".$nb_lettres_publiees.'</b></li>';
			$cadre.= "</ul>";
		}
		$cadre.= "</div>";
		// 2 </div> suppélementaires
		$cadre.= "</div>";
		$cadre.= "</div>";
		$cadre.= fin_cadre_relief();
		$cadre.= '<br />';

		echo $cadre;
	}


	/**
	 * lettres_afficher_statistiques_lettre_publiee
	 *
	 * @param string titre
	 * @param int id_lettre
	 * @return string un cadre avec qq stats
	 * @author Pierre Basson
	 **/
	function lettres_afficher_statistiques_lettre_publiee($titre, $id_lettre) {
		$requete_nb_inscrits = 'SELECT A.id_abonne 
								FROM spip_abonnes AS A
								INNER JOIN spip_abonnes_lettres AS AL ON AL.id_abonne=A.id_abonne
								WHERE AL.statut="valide"
									AND AL.id_lettre="'.$id_lettre.'"';
		$nb_inscrits = @spip_num_rows(spip_query($requete_nb_inscrits));
		
		$cadre = '';
		if ($nb_inscrits) {
			$cadre.= debut_cadre_relief('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/statistiques.png', true, "", $titre);
			$cadre.= "<div class='verdana1'>";
			if ($nb_inscrits) {
				$cadre.= "<b>"._T('lettres:abonnes')."</b>";
				$cadre.= "<ul style='margin:0px; padding-$spip_lang_left: 20px; margin-bottom: 5px;'>";
				$cadre.= "<li>"._T("lettres:nb_inscrits")."&nbsp;: <b>".$nb_inscrits.'</b></li>';
				$cadre.= "</ul>";
			}
			$cadre.= "</div>";
			// 2 </div> suppélementaires
			$cadre.= "</div>";
			$cadre.= "</div>";
			$cadre.= fin_cadre_relief();
		}
		echo $cadre;
	}


	/**
	 * lettres_afficher_statistiques_envoi_en_cours
	 *
	 * @param string titre
	 * @param int id_archive
	 * @return string un cadre avec qq stats
	 * @author Pierre Basson
	 **/
	function lettres_afficher_statistiques_envoi_en_cours($titre, $id_archive) {
		$requete_total_abonnes = 'SELECT AA.id_abonne 
									FROM spip_abonnes_archives AS AA
									WHERE AA.id_archive="'.$id_archive.'"';
		$nb_total_abonnes = @spip_num_rows(spip_query($requete_total_abonnes));
		
		$requete_total_email_envoyes = 'SELECT AA.id_abonne 
										FROM spip_abonnes_archives AS AA
										WHERE statut="envoye"
											AND AA.id_archive="'.$id_archive.'"';
		$nb_total_email_envoyes = @spip_num_rows(spip_query($requete_total_email_envoyes));
		
		$requete_total_email_attente = 'SELECT AA.id_abonne 
										FROM spip_abonnes_archives AS AA
										WHERE statut="a_envoyer"
											AND AA.id_archive="'.$id_archive.'"';
		$nb_total_email_attente = @spip_num_rows(spip_query($requete_total_email_attente));
		
		$requete_total_email_echec = 'SELECT AA.id_abonne 
										FROM spip_abonnes_archives AS AA
										WHERE statut="echec"
											AND AA.id_archive="'.$id_archive.'"';
		$nb_total_email_echec = @spip_num_rows(spip_query($requete_total_email_echec));
		
		$cadre = '';
		if ($nb_total_abonnes OR $nb_total_email_envoyes OR $nb_total_email_attente OR $nb_total_email_echec) {
			$cadre.= debut_cadre_relief('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/statistiques.png', true, "", $titre);
			$cadre.= "<div class='verdana1'>";
			$cadre.= "<b>"._T('lettres:abonnes')."</b>";
			$cadre.= "<ul style='margin:0px; padding-$spip_lang_left: 20px; margin-bottom: 5px;'>";
			if ($nb_total_abonnes)
				$cadre.= "<li>"._T("lettres:nb_total_abonnes")."&nbsp;: <b>".$nb_total_abonnes.'</b></li>';
			if ($nb_total_email_envoyes)
				$cadre.= "<li>"._T("lettres:nb_total_emails_envoyes")."&nbsp;: <b>".$nb_total_email_envoyes.'</b></li>';
			if ($nb_total_email_attente)
				$cadre.= "<li>"._T("lettres:nb_total_emails_attente")."&nbsp;: <b>".$nb_total_email_attente.'</b></li>';
			if ($nb_total_email_echec)
				$cadre.= "<li>"._T("lettres:nb_total_emails_echec")."&nbsp;: <b>".$nb_total_email_echec.'</b></li>';
			$cadre.= "</ul>";
			$cadre.= "</div>";
			// 2 </div> suppélementaires
			$cadre.= "</div>";
			$cadre.= "</div>";
			$cadre.= fin_cadre_relief();
		}
		echo $cadre;
	}

	/**
	 * lettres_afficher_statistiques_archive
	 *
	 * @param string titre
	 * @param int id_archive
	 * @return string un cadre avec qq stats
	 * @author Pierre Basson
	 **/
	function lettres_afficher_statistiques_archive($titre, $id_archive) {
		$requete_total_emails = 'SELECT nb_emails_envoyes,
										nb_emails_non_envoyes,
										nb_emails_echec
									FROM spip_archives
									WHERE id_archive="'.$id_archive.'"';
		list($nb_email_envoyes, $nb_emails_non_envoyes, $nb_emails_echec) = @spip_fetch_array(spip_query($requete_total_emails));

		$cadre = '';
		if ($nb_email_envoyes OR $nb_emails_non_envoyes OR $nb_emails_echec) {
			$cadre.= debut_cadre_relief('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/statistiques.png', true, "", $titre);
			$cadre.= "<div class='verdana1'>";
			$cadre.= "<b>"._T('lettres:resultat')."</b>";
			$cadre.= "<ul style='margin:0px; padding-$spip_lang_left: 20px; margin-bottom: 5px;'>";
			if ($nb_email_envoyes)
				$cadre.= "<li>"._T("lettres:nb_total_emails_envoyes")."&nbsp;: <b>".$nb_email_envoyes.'</b></li>';
			if ($nb_emails_non_envoyes)
				$cadre.= "<li>"._T("lettres:nb_total_emails_non_envoyes")."&nbsp;: <b>".$nb_emails_non_envoyes.'</b></li>';
			if ($nb_emails_echec)
				$cadre.= "<li>"._T("lettres:nb_total_emails_echec")."&nbsp;: <b>".$nb_emails_echec.'</b></li>';
			$cadre.= "</ul>";
			$cadre.= "</div>";
			// 2 </div> suppélementaires
			$cadre.= "</div>";
			$cadre.= "</div>";
			$cadre.= fin_cadre_relief();
		}
		echo $cadre;
	}

	/**
	 * lettres_afficher_numero_lettre
	 *
	 * @param int id_lettre
	 * @param boolean prévisualisation
	 * @param boolean statistiques
	 * @author Pierre Basson
	 **/
	function lettres_afficher_numero_lettre($id_lettre, $previsu=false, $statistiques=false) {
		echo "<br />";
		debut_boite_info();
		echo "<div align='center'>\n";
		echo "<font face='Verdana,Arial,Sans,sans-serif' size='1'><b>"._T('lettres:numero_lettre')."</b></font>\n";
		echo "<br><font face='Verdana,Arial,Sans,sans-serif' size='6'><b>$id_lettre</b></font>\n";
		$fond_message_html		= lettres_recuperer_meta('fond_message_html');
		$fond_message_texte		= lettres_recuperer_meta('fond_message_texte');
		if ($previsu) {
			icone_horizontale(_T('lettres:previsualiser_html'), generer_url_public($fond_message_html, 'id_lettre='.$id_lettre.'&var_mode=preview'), $image, "racine-24.gif");
			icone_horizontale(_T('lettres:previsualiser_texte'), generer_url_public($fond_message_texte, 'id_lettre='.$id_lettre.'&var_mode=preview'), $image, "racine-24.gif");
		}
		if ($statistiques)
			lettres_afficher_raccourci_statistiques_lettre($id_lettre);
		echo "</div>\n";
		fin_boite_info();
	}


	/**
	 * lettres_afficher_numero_archive
	 *
	 * @param int id_archive
	 * @author Pierre Basson
	 **/
	function lettres_afficher_numero_archive($id_archive) {
		echo "<br />";
		debut_boite_info();
		echo "<div align='center'>\n";
		echo "<font face='Verdana,Arial,Sans,sans-serif' size='1'><b>"._T('lettres:numero_archive')."</b></font>\n";
		echo "<br><font face='Verdana,Arial,Sans,sans-serif' size='6'><b>$id_archive</b></font>\n";
		icone_horizontale(_T('lettres:voir_message_html'), generer_url_ecrire('archives_message', "id_archive=$id_archive&format=html", '&'), '', "racine-24.gif");
		icone_horizontale(_T('lettres:voir_message_texte'), generer_url_ecrire('archives_message', "id_archive=$id_archive&format=texte", '&'), '', "racine-24.gif");
		echo "</div>\n";
		fin_boite_info();
	}

	/**
	 * lettres_afficher_raccourci_retourner_lettre
	 *
	 * @param int id_lettre
	 * @author Pierre Basson
	 **/
	function lettres_afficher_raccourci_retourner_lettre($id_lettre) {
		icone_horizontale(_T('lettres:raccourci_retourner_lettre'), generer_url_ecrire("lettres_visualisation", "id_lettre=$id_lettre", "&"), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre-24.png');
	}


	/**
	 * lettres_afficher_date
	 *
	 * @param datetime date
	 * @param boolean affiche pour modifs
	 * @author Pierre Basson
	 **/
	function lettres_afficher_date($date, $modif=false) {
		$titre_barre = _T('lettres:date').'&nbsp;&nbsp;('.majuscules(affdate($date)).')';
		if ($modif) {
			debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/date.png', false, "", bouton_block_invisible('datepub').$titre_barre);
			echo debut_block_invisible('datepub');
			echo "<table border='0' width='100%' style='text-align: right'>";
			echo "<tr>";
			echo "	<td><span class='verdana1'><B>"._T('lettres:changer_date')."</B></span> &nbsp;</td>";
			echo "	<td>";
			echo afficher_jour(affdate($date, 'jour'), "name='jour' size='1' class='fondl'", true);
			echo afficher_mois(affdate($date, 'mois'), "name='mois' size='1' class='fondl'", true);
			echo afficher_annee(affdate($date, 'annee'), "name='annee' size='1' class='fondl'");
			echo "	</td>";
			echo "	<td> &nbsp; <INPUT TYPE='submit' NAME='changer_date' VALUE='"._T('lettres:changer')."' CLASS='fondo' STYLE='font-size:10px'></td>";
			echo "</tr>";
			echo "</table>";
			echo fin_block();
			fin_cadre_enfonce();
		} else {
			debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/date.png', false, "", $titre_barre);
			fin_cadre_enfonce();
		}
	}


	/**
	 * lettres_afficher_dates_archive
	 *
	 * @param datetime date
	 * @param datetime date_debut_envoi
	 * @param datetime date_fin_envoi
	 * @author Pierre Basson
	 **/
	function lettres_afficher_dates_archive($date, $date_debut_envoi, $date_fin_envoi) {
		$titre_barre = _T('lettres:dates');
		debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/date.png', false, "", $titre_barre);
		echo "<table border='0' width='100%' style='text-align: right'>";
		echo "<tr>";
		echo "	<td><span class='verdana1'><B>"._T('lettres:date_message')."</B></span> &nbsp;</td>";
		echo "	<td>".affdate($date, 'jour')." ".affdate($date, 'nom_mois')." ".affdate($date, 'annee')."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "	<td><span class='verdana1'><B>"._T('lettres:date_debut_envoi')."</B></span> &nbsp;</td>";
		echo "	<td>".affdate_heure($date_debut_envoi)."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "	<td><span class='verdana1'><B>"._T('lettres:date_fin_envoi')."</B></span> &nbsp;</td>";
		echo "	<td>".affdate_heure($date_fin_envoi)."</td>";
		echo "</tr>";
		echo "</table>";
		fin_cadre_enfonce();
	}

	/**
	 * lettres_afficher_auteurs
	 *
	 * @param int id_lettre
	 * @param boolean affiche pour modifs
	 * @author Pierre Basson
	 **/
	function lettres_afficher_auteurs($id_lettre, $modif=false) {
		$titre_barre = _T('lettres:auteurs');

		if ($modif)
			debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/auteur.png', false, "", bouton_block_invisible('auteurs').$titre_barre);
		else
			debut_cadre_enfonce('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/auteur.png', false, "", $titre_barre);

		$tableau_auteurs_interdits = array();

		$auteurs_associes = 'SELECT A.id_auteur,
								A.email,
								A.nom
							FROM spip_auteurs AS A
							INNER JOIN spip_auteurs_lettres AS AL ON AL.id_auteur=A.id_auteur
							WHERE AL.id_lettre="'.$id_lettre.'"
							ORDER BY A.nom';
		$resultat_auteurs_associes = spip_query($auteurs_associes);
		if (@spip_num_rows($resultat_auteurs_associes) > 0) {
			echo "<div class='liste'>\n";
			echo "<table width='100%' cellpadding='3' cellspacing='0' border='0' background=''>\n";
			while ($arr = spip_fetch_array($resultat_auteurs_associes)) {
				$tableau_auteurs_interdits[] = $arr['id_auteur'];
				echo "<tr class='tr_liste'>\n";
				echo "<td width='25' class='arial11'>\n";
				echo "</td>\n";
				echo "<td class='arial2'>\n";
				echo "<A HREF='".generer_url_ecrire("auteurs_edit","id_auteur=".$arr['id_auteur'], '&')."'>\n";
				echo propre($arr['nom']);
				echo "</A>\n";
				echo "</td>\n";
				echo "<td class='arial2'>\n";
				echo $arr['email'];
				echo "</td>\n";
				if ($modif) {
					echo "<td class='arial1'>\n";
					echo "<A HREF='".generer_url_ecrire("lettres_visualisation","id_lettre=$id_lettre&supprimer_auteur=".$arr['id_auteur'], '&')."'>\n";
					echo _T('lettres:retirer_auteur')."\n";
					echo "<img src='img_pack/croix-rouge.gif' alt='X' width='7' height='7' border='0' align='middle' />\n";
					echo "</A>\n";
					echo "</td>\n";
				}
				echo "</tr>\n";
			}
			echo "</table>\n";
			echo "</div>\n";
		}
		if ($modif) {
			$auteurs_interdits = implode(",", $tableau_auteurs_interdits);
			if (!empty($auteurs_interdits))
				$where_auteurs_interdits = ' WHERE A.id_auteur NOT IN ('.$auteurs_interdits.')';
			else
				$where_auteurs_interdits = '';
			$requete = 'SELECT A.id_auteur, 
							A.nom
						FROM spip_auteurs AS A
						'.$where_auteurs_interdits.'
						ORDER BY A.nom';
			$resultat_requete = spip_query($requete);
			if (@spip_num_rows($resultat_requete) > 0) {
				echo debut_block_invisible('auteurs');
				echo "<table border='0' width='100%' style='text-align: right'>";
				echo "<tr>";
				echo "	<td><span class='verdana1'><B>"._T('lettres:ajouter_auteur')."</B></span> &nbsp;</td>";
				echo "	<td>";
				echo "		<select name='id_auteur' SIZE='1' STYLE='width: 180px;' CLASS='fondl'>";
				while ($arr = spip_fetch_array($resultat_requete)) {
					echo "				<option value='".$arr['id_auteur']."'>".propre($arr['nom'])."</option>";
				}
				echo "		</select><br/>";
				echo "	</td>";
				echo "	<td> &nbsp; <INPUT TYPE='submit' NAME='changer_auteur' VALUE='"._T('lettres:choisir')."' CLASS='fondo' STYLE='font-size:10px'></td>";
				echo "</tr>";
				echo "</table>";
				echo fin_block();
			}
		}
		fin_cadre_enfonce();
	}
	

	/**
	 * lettres_afficher_mots_cles
	 *
	 * @param int id_lettre
	 * @param boolean affiche pour modifs
	 * @author Pierre Basson
	 **/
	function lettres_afficher_mots_cles($id_lettre, $modif=false) {
		$requete_verification = 'SELECT M.id_mot AS id_mot, 
									M.titre AS titre, 
									M.type AS type 
								FROM spip_mots AS M
								INNER JOIN spip_groupes_mots AS GM ON GM.id_groupe=M.id_groupe
								WHERE GM.articles="oui"
								GROUP BY GM.titre';
		$resultat_verification = spip_query($requete_verification);
		if (@spip_num_rows($resultat_verification) > 0) {
			$titre_barre = _T('lettres:mots_cles');

			if ($modif)
				debut_cadre_enfonce('mot-cle-24.gif', false, "", bouton_block_invisible('motscles').$titre_barre);
			else
				debut_cadre_enfonce('mot-cle-24.gif', false, "", $titre_barre);

			$tableau_mots_interdits = array();

			$mots_associes = 'SELECT M.id_mot AS id_mot, 
									M.titre AS titre,
									M.type AS type
								FROM spip_mots AS M
								INNER JOIN spip_mots_lettres AS ML ON ML.id_mot=M.id_mot
								WHERE ML.id_lettre="'.$id_lettre.'"
								GROUP BY M.type
								ORDER BY M.titre';
			$resultat_mots_associes = spip_query($mots_associes);
			if (@spip_num_rows($resultat_mots_associes) > 0) {
				echo "<div class='liste'>\n";
				echo "<table width='100%' cellpadding='3' cellspacing='0' border='0' background=''>\n";
				while ($arr = spip_fetch_array($resultat_mots_associes)) {
					$tableau_mots_interdits[] = $arr['id_mot'];
					echo "<tr class='tr_liste'>\n";
					echo "<td width='25' class='arial11'>\n";
					echo "<img src='img_pack/petite-cle.gif' alt='petite-cle' width='23' height='12' border='0' />\n";
					echo "</td>\n";
					echo "<td class='arial2'>\n";
					echo "<A HREF='".generer_url_ecrire("mots_edit","id_mot=".$arr['id_mot'], '&')."'>\n";
					echo propre($arr['titre']);
					echo "</A>\n";
					echo "</td>\n";
					echo "<td class='arial2'>";
					echo propre($arr['type']);
					echo "</td>\n";
					if ($modif) {
						echo "<td class='arial1'>\n";
						echo "<A HREF='".generer_url_ecrire("lettres_visualisation","id_lettre=$id_lettre&supprimer_mot=".$arr['id_mot'], '&')."'>\n";
						echo _T('lettres:retirer_mot')."\n";
						echo "<img src='img_pack/croix-rouge.gif' alt='X' width='7' height='7' border='0' align='middle' />\n";
						echo "</A>\n";
						echo "</td>\n";
					}
					echo "</tr>\n";
				}
				echo "</table>\n";
				echo "</div>\n";
			}
			if ($modif) {
				$mots_interdits = implode(",", $tableau_mots_interdits);
				if (!empty($mots_interdits))
					$where_mots_interdits = ' AND M.id_mot NOT IN ('.$mots_interdits.')';
				else
					$where_mots_interdits = '';
				$requete = 'SELECT M.id_mot AS id_mot, 
								M.titre AS titre, 
								M.type AS type 
							FROM spip_mots AS M
							INNER JOIN spip_groupes_mots AS GM ON GM.id_groupe=M.id_groupe
							WHERE GM.articles="oui" '.$where_mots_interdits.'
							ORDER BY GM.titre';
				$resultat_requete = spip_query($requete);
				if (@spip_num_rows($resultat_requete) > 0) {
					echo debut_block_invisible('motscles');
					echo "<table border='0' width='100%' style='text-align: right'>";
					echo "<tr>";
					echo "	<td><span class='verdana1'><B>"._T('lettres:ajouter_mot')."</B></span> &nbsp;</td>";
					echo "	<td>";
					echo "		<select name='id_mot' SIZE='1' STYLE='width: 180px;' CLASS='fondl'>";
					$type_old = '';
					while ($arr = spip_fetch_array($resultat_requete)) {
						$type = $arr['type'];
						if ($type != $type_old) {
							if ($type_old != '')
								echo "			</optgroup>";
							echo "			<optgroup label='".propre($arr['type'])."'>";
						}
						echo "				<option value='".$arr['id_mot']."'>".propre($arr['titre'])."</option>";
						$type_old = $type;
					}
					echo "			</optgroup>";
					echo "		</select><br/>";
					echo "	</td>";
					echo "	<td> &nbsp; <INPUT TYPE='submit' NAME='changer_mot' VALUE='"._T('lettres:choisir')."' CLASS='fondo' STYLE='font-size:10px'></td>";
					echo "</tr>";
					echo "</table>";
					echo fin_block();
				}
			}
			fin_cadre_enfonce();
		}
	}
	

	/**
	 * lettres_afficher_langue
	 *
	 * @param string lang
	 * @param boolean modif
	 * @author Pierre Basson
	 **/
	function lettres_afficher_langue($lang, $modif=false) {
		$titre_barre = _T('lettres:langue_lettre')."&nbsp; (".traduire_nom_langue($lang).")";
		$ret = liste_options_langues('var_lang', $lang);
		if ($modif)
			debut_cadre_enfonce('langues-24.gif', false, "", bouton_block_invisible('langueslettre').$titre_barre);
		else
			debut_cadre_enfonce('langues-24.gif', false, "", $titre_barre);
		if ($modif) {
			echo debut_block_invisible('langueslettre');
			echo "<table border='0' width='100%' style='text-align: right'>";
			echo "<tr>";
			echo "	<td><span class='verdana1'><B>"._T('lettres:langue_cette_lettre')."</B></span> &nbsp;</td>";
			echo "	<td>";
			echo '		<select name="lang" size="1" style="width: 180px;"  CLASS="fondl">';
			echo $ret;
			echo '		</select>';
			echo "	</td>";
			echo "	<td> &nbsp; <INPUT TYPE='submit' NAME='changer_langue' CLASS='fondo' VALUE='"._T('lettres:changer')."' STYLE='font-size:10px'></td>";
			echo "</tr>";
			echo "</table>";
			echo fin_block();
		}
		fin_cadre_enfonce();
	}
	
	/**
	 * lettres_afficher_raccourci_creer_lettre
	 *
	 * affiche un raccourci vers la création d'une nouvelle lettre d'information
	 *
	 * @author Pierre Basson
	 **/
	function lettres_afficher_raccourci_creer_lettre() {
		icone_horizontale(_T('lettres:raccourci_creer_nouvelle_lettre'), generer_url_ecrire("lettres_edition", "new=oui"), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre-24.png', 'creer.gif');
	}


	/**
	 * lettres_afficher_raccourci_configurer_plugin
	 *
	 * affiche un raccourci vers la création d'une nouvelle lettre d'information
	 *
	 * @author Pierre Basson
	 **/
	function lettres_afficher_raccourci_configurer_plugin() {
		icone_horizontale(_T('lettres:raccourci_configurer_plugin'), generer_url_ecrire("lettres_configuration"), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/configuration.png');
	}


	/**
	 * lettres_afficher_raccourci_voir_abonnes
	 *
	 * affiche un raccourci vers la liste des abonnés
	 *
	 * @param int id_lettre
	 * @author Pierre Basson
	 **/
	function lettres_afficher_raccourci_voir_abonnes($id_lettre) {
		return icone_horizontale(_T('lettres:raccourci_voir_abonnes'), generer_url_ecrire("abonnes_lettre","id_lettre=$id_lettre"), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonnes.png', '', false);
	}


	/**
	 * lettres_afficher_raccourci_ajouter_abonne
	 *
	 * affiche un raccourci vers l'ajout d'un abonné à une liste
	 *
	 * @param int id_lettre
	 * @author Pierre Basson
	 **/
	function lettres_afficher_raccourci_ajouter_abonne($id_lettre=0) {
		if ($id_lettre)
			icone_horizontale(_T('lettres:raccourci_ajouter_abonne'), generer_url_ecrire("abonnes_edition","new=oui&id_lettre=$id_lettre"), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonne.png', 'creer.gif');
		else
			icone_horizontale(_T('lettres:raccourci_ajouter_abonne'), generer_url_ecrire("abonnes_edition","new=oui"), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonne.png', 'creer.gif');
	}


	/**
	 * lettres_afficher_raccourci_liste_abonnes
	 *
	 * @param string titre
	 * @author Pierre Basson
	 **/
	function lettres_afficher_raccourci_liste_abonnes($titre) {
		icone_horizontale($titre, generer_url_ecrire("abonnes",""), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonnes.png', '');
	}


	/**
	 * lettres_afficher_raccourci_envoyer_lettre
	 *
	 * affiche un raccourci vers l'envoi d'une lettre
	 *
	 * @param int id_lettre
	 * @author Pierre Basson
	 **/
	function lettres_afficher_raccourci_envoyer_lettre($id_lettre) {
		$id_archive = lettres_recuperer_dernier_id_archive($id_lettre);
		
		
		echo "<script type=\"text/javascript\">
		function popupSendMail()
		{
			window.open(\"".str_replace('&amp;','&',generer_url_ecrire("lettres_envoyer", "id_archive=$id_archive"))."\", \"fenetreEnvoi\", \"width=400,height=200,scrollbars=0,top=200,left=300\");
		}
		</script>";
		
		$url = "javascript:popupSendMail()";
		icone_horizontale(_T('lettres:raccourci_envoyer_lettre'), $url, '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/envoyer.png');
	}


	/**
	 * lettres_afficher_raccourci_tester_envoi
	 *
	 * affiche un raccourci vers la page de test d'envoi d'une lettre
	 *
	 * @param int id_lettre
	 * @author Pierre Basson
	 **/
	function lettres_afficher_raccourci_tester_envoi($id_lettre) {
		echo "<script type=\"text/javascript\">
		function popupTesterEnvoi()
		{
			window.open(\"".str_replace('&amp;','&',generer_url_ecrire("lettres_tester", "id_lettre=$id_lettre"))."\", \"fenetreTest\", \"width=400,height=200,scrollbars=0,top=200,left=300\");
		}
		</script>";
		$url = "javascript:popupTesterEnvoi()";
		icone_horizontale(_T('lettres:raccourci_tester_envoi'), $url, '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/test.png');
	}


	/**
	 * lettres_afficher_raccourci_reprendre_envoi
	 *
	 * affiche un raccourci vers la page pour reprendre un envoi
	 *
	 * @param int id_lettre
	 * @author Pierre Basson
	 **/
	function lettres_afficher_raccourci_reprendre_envoi($id_lettre) {
		$id_archive = lettres_recuperer_dernier_id_archive($id_lettre);
		$url = "javascript:window.open(\"".generer_url_ecrire("lettres_envoyer", "id_archive=$id_archive&envoi=oui")."\", \"fenetreEnvoi\", \"width=400,height=200,scrollbars=0,top=200,left=300\");";
		icone_horizontale(_T('lettres:raccourci_reprendre_envoi'), $url, '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/reprendre.png');
	}


	/**
	 * lettres_afficher_raccourci_statistiques
	 *
	 * affiche un raccourci vers les statistiques
	 *
	 * @author Pierre Basson
	 **/
	function lettres_afficher_raccourci_statistiques() {
		icone_horizontale(_T('lettres:raccourci_statistiques'), generer_url_ecrire("lettres"), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/statistiques.png');
	}


	/**
	 * lettres_afficher_raccourci_statistiques_lettre
	 *
	 * affiche un raccourci vers les statistiques
	 *
	 * @param int id_lettre
	 * @author Pierre Basson
	 **/
	function lettres_afficher_raccourci_statistiques_lettre($id_lettre) {
		icone_horizontale(_T('lettres:raccourci_statistiques'), generer_url_ecrire("lettres_statistiques", "id_lettre=$id_lettre"), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/statistiques.png');
	}


	/**
	 * lettres_afficher_raccourci_archives
	 *
	 * affiche un raccourci vers les archives
	 *
	 * @param int id_lettre
	 * @author Pierre Basson
	 **/
	function lettres_afficher_raccourci_archives($id_lettre) {
		$icone = icone_horizontale(_T('lettres:raccourci_archives'), generer_url_ecrire("lettres_archives", "id_lettre=$id_lettre"), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/archives.png', '', false);
		return $icone;
	}

	function lettres_afficher_raccourci_import_csv($id_lettre=0) {
		if ($id_lettre)
			icone_horizontale(_T('lettres:import_csv'), generer_url_ecrire("abonnes_import", "id_lettre=$id_lettre"), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/import.png', '');
		else
			icone_horizontale(_T('lettres:import_csv'), generer_url_ecrire("abonnes_import"), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/import.png', '');
	}

	function lettres_afficher_raccourci_export_csv($id_lettre) {
		icone_horizontale(_T('lettres:export_csv'), generer_url_ecrire("abonnes_export", "id_lettre=$id_lettre"), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/export.png', '');
	}

	/**
	 * lettres_afficher_raccourci_formulaire_inscription
	 *
	 * affiche un raccourci vers les archives
	 *
	 * @param int id_lettre
	 * @author Pierre Basson
	 **/
	function lettres_afficher_raccourci_formulaire_inscription() {
		$fond_formulaire_inscription = lettres_recuperer_meta('fond_formulaire_lettre');
		icone_horizontale(_T('lettres:raccourci_formulaire_inscription'), generer_url_public($fond_formulaire_inscription), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/formulaire.png');
	}


	/**
	 * lettres_afficher_raccourci_liste_lettres
	 *
	 * affiche un raccourci vers la liste des lettres
	 *
	 * @param string titre
	 * @author Pierre Basson
	 **/
	function lettres_afficher_raccourci_liste_lettres($titre) {
		icone_horizontale($titre, generer_url_ecrire("lettres"), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre-24.png');
	}
	

	/**
	 * lettres_recuperer_dernier_id_archive
	 *
	 * @param int id_lettre
	 * @return int id_archive
	 * @author Pierre Basson
	 **/
	function lettres_recuperer_dernier_id_archive($id_lettre) {
		$requete_id_archive = 'SELECT MAX(id_archive) FROM spip_archives WHERE id_lettre="'.$id_lettre.'" LIMIT 1';
		$resultat_id_archive = spip_query($requete_id_archive);
		list($id_archive) = spip_fetch_array($resultat_id_archive);
		return $id_archive;
	}
	
	
	/**
	 * lettres_recuperer_id_lettre_depuis_id_archive
	 *
	 * @param int id_archive
	 * @return int id_lettre
	 * @author Pierre Basson
	 **/
	function lettres_recuperer_id_lettre_depuis_id_archive($id_archive) {
		$requete_id_lettre = 'SELECT id_lettre FROM spip_archives WHERE id_archive="'.$id_archive.'" LIMIT 1';
		$resultat_id_lettre = spip_query($requete_id_lettre);
		list($id_lettre) = spip_fetch_array($resultat_id_lettre);
		return $id_lettre;
	}
	
	
	/**
	 * lettres_afficher_pagination
	 *
	 * @param string fond
	 * @param string arguments
	 * @param int total
	 * @param int position
	 * @author Pierre Basson
	 **/
	function lettres_afficher_pagination($fond, $arguments, $total, $position, $nom) {
		global $pas;
		$pagination = '';
		$i = 0;

		$nombre_pages = floor(($total-1)/$pas)+1;

		if($nombre_pages>1) {

			$pagination.= "<div style='background-color: white; color: black; padding: 3px; padding-left: 30px;  padding-right: 40px; text-align: right;' class='verdana2'>\n";
			while($i<$nombre_pages) {
				$url = generer_url_ecrire($fond, $nom.'='.strval($i*$pas).$arguments, '&');
				$item = strval($i+1);
				if(($i*$pas) != $position) {
					$pagination.= '&nbsp;&nbsp;&nbsp;<a href="'.$url.'">'.$item.'</a>'."\n";
				} else {
					$pagination.= '&nbsp;&nbsp;&nbsp;<i>'.$item.'</i>'."\n";
				}
				$i++;
			}
			
			$pagination.= "</ul>\n";
			$pagination.= "</div>\n";

			
		}
		
		return $pagination;
	}


	/**
	 * lettres_afficher_recherche
	 *
	 * @author Pierre Basson
	 **/
	function lettres_afficher_recherche($fond) {
		echo generer_url_post_ecrire($fond, '', 'formulaire');
		echo debut_cadre_relief('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/recherche_'.$fond.'.png', true, "", _T('lettres:recherche'));
		echo "<div class='verdana1'>";
		$recherche = $_POST['recherche'];
		echo '<input type="text" name="recherche" value="'.$recherche.'" style="margin-left: 20px; background-color: #e4e4e4; border: 1px solid #C0CAD4; padding-left: 2px;" size="12" />';
		echo '&nbsp;<a href="javascript:document.formulaire.submit();"><img border="0" style="vertical-align: bottom;" src="'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/ok.png'.'" alt="OK" /></a>';
		echo '&nbsp;<a href="'.generer_url_ecrire($fond).'"><img border="0" style="vertical-align: bottom;" src="'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/annuler.png'.'" alt="Reset" /></a>';
		echo "</div>";
		echo "</div>";
		echo "</div>";
		echo fin_cadre_relief();
		echo "</form>";
	}


	function lettres_afficher_etapes_import($etape=1) {
		$cadre.= debut_cadre_relief('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/import.png', true, "", _T('lettres:import_csv'));
		$cadre.= "<div class='verdana1'>";
		$cadre.= "<b>"._T('lettres:import_etapes')."</b>";
		$cadre.= "<ol style='margin:0px; padding-$spip_lang_left: 20px; margin-bottom: 5px;'>";
		if ($etape == 1 )
			$cadre.= '<li style="font-weight: bold">'._T("lettres:import_etape_1").'</li>';
		else
			$cadre.= "<li>"._T("lettres:import_etape_1").'</li>';
		if ($etape == 2 )
			$cadre.= '<li style="font-weight: bold">'._T("lettres:import_etape_2").'</li>';
		else
			$cadre.= "<li>"._T("lettres:import_etape_2").'</li>';
		if ($etape == 3 )
			$cadre.= '<li style="font-weight: bold">'._T("lettres:import_etape_3").'</li>';
		else
			$cadre.= "<li>"._T("lettres:import_etape_3").'</li>';
		if ($etape == 4 )
			$cadre.= '<li style="font-weight: bold">'._T("lettres:import_etape_4").'</li>';
		else
			$cadre.= "<li>"._T("lettres:import_etape_4").'</li>';
		$cadre.= "</ol>";
		$cadre.= "<br />";
		$cadre.= "</div>";
		// 2 </div> suppélementaires
		$cadre.= "</div>";
		$cadre.= "</div>";
		$cadre.= fin_cadre_relief();
		$cadre.= '<br />';
		echo $cadre;
	}


	function lettres_afficher_etapes_export($etape) {
		$cadre.= debut_cadre_relief('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/export.png', true, "", _T('lettres:export_csv'));
		$cadre.= "<div class='verdana1'>";
		$cadre.= "<b>"._T('lettres:export_etapes')."</b>";
		$cadre.= "<ol style='margin:0px; padding-$spip_lang_left: 20px; margin-bottom: 5px;'>";
		if ($etape == 1 )
			$cadre.= '<li style="font-weight: bold">'._T("lettres:export_etape_1").'</li>';
		else
			$cadre.= "<li>"._T("lettres:export_etape_1").'</li>';
		if ($etape == 2 )
			$cadre.= '<li style="font-weight: bold">'._T("lettres:export_etape_2").'</li>';
		else
			$cadre.= "<li>"._T("lettres:export_etape_2").'</li>';
		$cadre.= "</ol>";
		$cadre.= "<br />";
		$cadre.= "</div>";
		// 2 </div> suppélementaires
		$cadre.= "</div>";
		$cadre.= "</div>";
		$cadre.= fin_cadre_relief();
		$cadre.= '<br />';
		echo $cadre;
	}

?>