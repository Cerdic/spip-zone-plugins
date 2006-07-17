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


	include_spip('base/create');
	include_spip('base/abstract_sql');
	include_spip('inc/sondages_fonctions');
	include_spip('inc/plugin');
	global $pas;
	$pas = 20;


	/**
	 * sondages_verifier_base
	 *
	 * @return true
	 * @author Pierre Basson
	 **/
	function sondages_verifier_base() {
		$info_plugin_sondages = plugin_get_infos(_NOM_PLUGIN_SONDAGES);
		$version_plugin = $info_plugin_sondages['version'];
		if (!isset($GLOBALS['meta']['spip_sondages_version'])) {
			creer_base();
			ecrire_meta('spip_sondages_version', $version_plugin);
			ecrire_meta('fond_sondage', 'sondage');
			ecrire_metas();
		} else {
			$version_base = $GLOBALS['meta']['spip_sondages_version'];
/*			if ($version_base < 1.1) {
				creer_base();
				ecrire_meta('spip_sondages_version', $version_base = 1.1);
				ecrire_metas();
			}
*/		}
		return true;
	}


	/**
	 * sondages_verifier_droits
	 *
	 * redirige vers l'accueil si l'auteur n'est pas un admin
	 *
	 * @author Pierre Basson
	 **/
	function sondages_verifier_droits() {
		if ($GLOBALS['connect_statut'] != "0minirezo")
			sondages_rediriger_javascript(generer_url_ecrire('accueil')); 
	}
	
	
	/**
	 * sondages_rediriger_javascript
	 *
	 * redirige vers une url
	 *
	 * @param string url
	 * @author Pierre Basson
	 **/
	function sondages_rediriger_javascript($url) {
		echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
		exit();
	}

	/**
	 * sondages_mettre_a_jour_sondage
	 *
	 * met à jour un sondage en fonction de ses dates de début et de fin
	 *
	 * @param int id_sondage
	 * @return true
	 * @author Pierre Basson
	 **/
	function sondages_mettre_a_jour_sondage($id_sondage) {
		$requete_en_attente = 'SELECT statut FROM spip_sondages WHERE id_sondage="'.$id_sondage.'" AND NOW() < date_debut';
		$resultat_en_attente = spip_query($requete_en_attente);
		if (spip_num_rows($resultat_en_attente) == 1) {
			list($statut) = spip_fetch_array($resultat_en_attente);
			if ($statut != 'en_attente')
				spip_query('UPDATE spip_sondages SET statut="en_attente" WHERE id_sondage="'.$id_sondage.'"');
			return true;
		}

		$requete_publie = 'SELECT statut FROM spip_sondages WHERE id_sondage="'.$id_sondage.'" AND NOW() >= date_debut AND NOW() <= date_fin';
		$resultat_publie = spip_query($requete_publie);
		if (spip_num_rows($resultat_publie) == 1) {
			list($statut) = spip_fetch_array($resultat_publie);
			if ($statut != 'publie')
				spip_query('UPDATE spip_sondages SET statut="publie" WHERE id_sondage="'.$id_sondage.'"');
			return true;
		}

		$requete_termine = 'SELECT statut FROM spip_sondages WHERE id_sondage="'.$id_sondage.'" AND NOW() > date_fin';
		$resultat_termine = spip_query($requete_termine);
		if (spip_num_rows($resultat_termine) == 1) {
			list($statut) = spip_fetch_array($resultat_termine);
			if ($statut != 'termine')
				spip_query('UPDATE spip_sondages SET statut="termine" WHERE id_sondage="'.$id_sondage.'"');
			return true;
		}
	}


	/**
	 * sondages_afficher_statistiques_globales
	 *
	 * @return string un cadre avec qq stats
	 * @author Pierre Basson
	 **/
	function sondages_afficher_statistiques_globales() {
		$info_plugin_lettres = plugin_get_infos(_NOM_PLUGIN_SONDAGES);

		$requete_nb_sondages = 'SELECT id_sondage 
								FROM spip_sondages';
		$nb_sondages = @spip_num_rows(spip_query($requete_nb_sondages));
		
		$requete_nb_sondages_en_ligne = 'SELECT id_sondage 
										FROM spip_sondages
										WHERE en_ligne="oui"';
		$nb_sondages_en_ligne = @spip_num_rows(spip_query($requete_nb_sondages_en_ligne));
		
		$nb_sondages_hors_ligne = $nb_sondages - intval($nb_sondages_en_ligne);
		
		$requete_nb_avis = 'SELECT id_avis
							FROM spip_avis';
		$nb_avis = @spip_num_rows(spip_query($requete_nb_avis));

		$cadre = '';
		$cadre.= debut_cadre_relief("plugin-24.gif", true, "", _T('sondages:sondages'));
		$cadre.= "<div class='verdana1'>";
		$cadre.= "<b>"._T('sondages:plugin')."</b>";
		$cadre.= "<ul style='margin:0px; padding-$spip_lang_left: 20px; margin-bottom: 5px;'>";
		$cadre.= "<li>"._T("sondages:plugin_version")."&nbsp;: <b>".$info_plugin_lettres['version'].'</b></li>';
		$cadre.= "<li>"._T("sondages:plugin_auteur")."&nbsp;: <b>".propre($info_plugin_lettres['auteur']).'</b></li>';
		$cadre.= "</ul>";
		$cadre.= "<br />";
		if ($nb_sondages) {
			$cadre.= "<b>"._T('sondages:sondages')."</b>";
			$cadre.= "<ul style='margin:0px; padding-$spip_lang_left: 20px; margin-bottom: 5px;'>";
			$cadre.= "<li>"._T("sondages:nb_sondages")."&nbsp;: <b>".$nb_sondages.'</b></li>';
			if ($nb_sondages_en_ligne)		$cadre.= "<li>"._T("sondages:nb_sondages_en_ligne")."&nbsp;: <b>".$nb_sondages_en_ligne.'</b></li>';
			if ($nb_sondages_hors_ligne)	$cadre.= "<li>"._T("sondages:nb_sondages_hors_ligne")."&nbsp;: <b>".$nb_sondages_hors_ligne.'</b></li>';
			if ($nb_avis)					$cadre.= "<li>"._T("sondages:nb_avis")."&nbsp;: <b>".$nb_avis.'</b></li>';
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
	 * sondages_afficher_raccourci_creer_sondage
	 *
	 * affiche un raccourci vers la création d'un nouveau sondage
	 *
	 * @author Pierre Basson
	 **/
	function sondages_afficher_raccourci_creer_sondage() {
		icone_horizontale(_T('sondages:raccourci_creer_nouveau_sondage'), generer_url_ecrire("sondages_edition", "new=oui"), '../'._DIR_PLUGIN_SONDAGES.'/img_pack/sondages-24.png', 'creer.gif');
	}


	/**
	 * sondages_afficher_raccourci_liste_sondages
	 *
	 * affiche un raccourci vers la liste des sondages
	 *
	 * @author Pierre Basson
	 **/
	function sondages_afficher_raccourci_liste_sondages() {
		icone_horizontale(_T('sondages:raccourci_aller_liste_sondages'), generer_url_ecrire("sondages"), '../'._DIR_PLUGIN_SONDAGES.'/img_pack/sondages-24.png', '');
	}


	/**
	 * sondages_afficher_sondages
	 *
	 * affiche la la liste des sondages selon un statut donné
	 *
	 * @param string titre
	 * @param string image
	 * @param string en_ligne
	 * @param string statut
	 * @param string recherche
	 * @param string nom_position
	 * @return string la liste des sondages pour le statut demandé
	 * @author Pierre Basson
	 **/
	function sondages_afficher_sondages($titre, $image, $en_ligne, $statut, $recherche='', $nom_position='position') {
		global $pas;
		$position = intval($_GET[$nom_position]);

		$clause_where = '';
		if (!empty($recherche)) {
			$recherche = addslashes($recherche);
			$clause_where.= ' AND ( titre LIKE "%'.$recherche.'%"  OR  texte LIKE "%'.$recherche.'%" )';
		}
	
		$sondages = '';

		$requete_sondages = 'SELECT id_sondage,
								titre,
								date_debut,
								date_fin,
								lang
							FROM spip_sondages
							WHERE en_ligne="'.$en_ligne.'"
								AND statut="'.$statut.'" '.$clause_where.'
							ORDER BY date_debut DESC
							LIMIT '.$position.','.$pas.'';
		$resultat_sondages = spip_query($requete_sondages);
		if (@spip_num_rows($resultat_sondages) > 0) {

			$sondages.= "<div class='liste'>\n";
			$sondages.= "<div style='position: relative;'>\n";
			$sondages.= "<div style='position: absolute; top: -12px; left: 3px;'>\n";
			$sondages.= "<img src='".$image."'  />\n";
			$sondages.= "</div>\n";
			$sondages.= "<div style='background-color: white; color: black; padding: 3px; padding-left: 30px; border-bottom: 1px solid #444444;' class='verdana2'>\n";
			$sondages.= "<b>\n";
			$sondages.= $titre;
			$sondages.= "</b>\n";
			$sondages.= "</div>\n";
			$sondages.= "</div>\n";
			$sondages.= "<table width='100%' cellpadding='2' cellspacing='0' border='0'>\n";

			while ($sondage = spip_fetch_array($resultat_sondages)) {
				$id_sondage		= $sondage['id_sondage'];
				$titre			= $sondage['titre'];
				$date_debut		= affdate($sondage['date_debut']);
				$date_fin		= affdate($sondage['date_fin']);
				$nom_langue		= traduire_nom_langue($sondage['lang']);
				$url_sondage	= generer_url_ecrire('sondages_visualisation', 'id_sondage='.$id_sondage);

				$sondages.= "<tr class='tr_liste'>\n";
				$sondages.= "<td width='11'>";
				switch ($statut) {
					case 'brouillon':
						$sondages.= "<img src='img_pack/puce-blanche.gif' alt='puce-blanche' border='0' style='margin: 1px;' />";
						break;
					case 'en_attente':
						$sondages.= "<img src='img_pack/puce-orange.gif' alt='puce-orange' border='0' style='margin: 1px;' />";
						break;
					case 'publie':
						$sondages.= "<img src='img_pack/puce-verte.gif' alt='puce-verte' border='0' style='margin: 1px;' />";
						break;
					case 'termine':
						$sondages.= "<img src='img_pack/puce-poubelle.gif' alt='puce-noire' border='0' style='margin: 1px;' />";
						break;
				}
				$sondages.= "</td>";
				$sondages.= "<td class='arial2'>\n";
				$sondages.= "<div>\n";
				$sondages.= "<a href=\"".$url_sondage."\" dir='ltr' style='display:block;'>\n";
				$sondages.= $titre;
				if ($GLOBALS['langue_site'] != $sondage['lang']) {
					$sondages.= " <font size='1' color='#666666' dir='ltr'>\n";
					$sondages.= "(".$nom_langue.")\n";
					$sondages.= "</font>\n";
				}
				$sondages.= "</a>\n";
				$sondages.= "</div>\n";
				$sondages.= "</td>\n";
				$sondages.= "<td width='100' class='arial1'>".$date_debut."</td>\n";
				$sondages.= "<td width='100' class='arial1'>".$date_fin."</td>\n";
				$sondages.= "<td width='40' class='arial1'><b>N&deg;&nbsp;".$id_sondage."</b></td>\n";
				$sondages.= "</tr>\n";

			}
			$sondages.= "</table>\n";
			$requete_total = 'SELECT id_sondage
								FROM spip_sondages
								WHERE en_ligne="'.$en_ligne.'"
									AND statut="'.$statut.'" '.$clause_where.'
								ORDER BY date DESC';
			$resultat_total = spip_query($requete_total);
			$total = spip_num_rows($resultat_total);
			$sondages.= sondages_afficher_pagination('sondages', '', $total, $position, $nom_position);
			$sondages.= "</div>\n";
			$sondages.= "<br />\n";
		}
		
		echo $sondages;

	}


	/**
	 * sondages_afficher_pagination
	 *
	 * @param string fond
	 * @param string arguments
	 * @param int total
	 * @param int position
	 * @author Pierre Basson
	 **/
	function sondages_afficher_pagination($fond, $arguments, $total, $position, $nom) {
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
	 * sondages_afficher_numero_sondage
	 *
	 * @param int id_sondage
	 * @param boolean prévisualisation
	 * @param boolean statistiques
	 * @author Pierre Basson
	 **/
	function sondages_afficher_numero_sondage($id_sondage, $previsu=false, $statistiques=false) {
		echo "<br />";
		debut_boite_info();
		echo "<div align='center'>\n";
		echo "<font face='Verdana,Arial,Sans,sans-serif' size='1'><b>"._T('sondages:numero_sondage')."</b></font>\n";
		echo "<br><font face='Verdana,Arial,Sans,sans-serif' size='6'><b>$id_sondage</b></font>\n";
		$fond_sondage = $GLOBALS['meta']['fond_sondage'];
		if ($previsu) {
			icone_horizontale(_T('sondages:previsualiser'), generer_url_public($fond_sondage, 'id_sondage='.$id_sondage.'&var_mode=preview'), $image, "racine-24.gif");
		}
		echo "</div>\n";
		fin_boite_info();
	}


	/**
	 * sondages_afficher_dates
	 *
	 * @param datetime date de debut
	 * @param datetime date de fin
	 * @param boolean affiche pour modifs
	 * @author Pierre Basson
	 **/
	function sondages_afficher_dates($date_debut, $date_fin, $modif=false) {
		$titre_barre = _T('sondages:periode_de_validite').'<br>'._T('sondages:du').'&nbsp;'.majuscules(affdate($date_debut)).'&nbsp;'._T('sondages:au').'&nbsp;'.majuscules(affdate($date_fin));
		if ($modif) {
			debut_cadre_enfonce('../'._DIR_PLUGIN_SONDAGES.'/img_pack/periode.png', false, "", bouton_block_invisible('dates').$titre_barre);
			echo debut_block_invisible('dates');
			echo "<table border='0' width='100%' style='text-align: right'>";
			echo "<tr>";
			echo "	<td><span class='verdana1'><B>"._T('sondages:changer_date_debut')."</B></span> &nbsp;</td>";
			echo "	<td>";
			echo afficher_jour(affdate($date_debut, 'jour'), "name='jour_debut' size='1' class='fondl'", true);
			echo afficher_mois(affdate($date_debut, 'mois'), "name='mois_debut' size='1' class='fondl'", true);
			echo afficher_annee(affdate($date_debut, 'annee'), "name='annee_debut' size='1' class='fondl'");
			echo "	</td>";
			echo "	<td>&nbsp;</td>";
			echo "</tr>";
			echo "<tr>";
			echo "	<td><span class='verdana1'><B>"._T('sondages:changer_date_fin')."</B></span> &nbsp;</td>";
			echo "	<td>";
			echo afficher_jour(affdate($date_fin, 'jour'), "name='jour_fin' size='1' class='fondl'", true);
			echo afficher_mois(affdate($date_fin, 'mois'), "name='mois_fin' size='1' class='fondl'", true);
			echo afficher_annee(affdate($date_fin, 'annee'), "name='annee_fin' size='1' class='fondl'");
			echo "	</td>";
			echo "	<td> &nbsp; <INPUT TYPE='submit' NAME='changer_dates' VALUE='"._T('sondages:changer')."' CLASS='fondo' STYLE='font-size:10px'></td>";
			echo "</tr>";
			echo "</table>";
			echo fin_block();
			fin_cadre_enfonce();
		} else {
			debut_cadre_enfonce('../'._DIR_PLUGIN_SONDAGES.'/img_pack/date.png', false, "", $titre_barre);
			fin_cadre_enfonce();
		}
	}


	/**
	 * sondages_afficher_auteurs
	 *
	 * @param int id_sondage
	 * @param boolean affiche pour modifs
	 * @author Pierre Basson
	 **/
	function sondages_afficher_auteurs($id_sondage, $modif=false) {
		$titre_barre = _T('sondages:auteurs');

		if ($modif)
			debut_cadre_enfonce('../'._DIR_PLUGIN_SONDAGES.'/img_pack/auteur.png', false, "", bouton_block_invisible('auteurs').$titre_barre);
		else
			debut_cadre_enfonce('../'._DIR_PLUGIN_SONDAGES.'/img_pack/auteur.png', false, "", $titre_barre);

		$tableau_auteurs_interdits = array();

		$auteurs_associes = 'SELECT A.id_auteur,
								A.email,
								A.nom
							FROM spip_auteurs AS A
							INNER JOIN spip_auteurs_sondages AS AL ON AL.id_auteur=A.id_auteur
							WHERE AL.id_sondage="'.$id_sondage.'"
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
					echo "<A HREF='".generer_url_ecrire("sondages_visualisation","id_sondage=$id_sondage&supprimer_auteur=".$arr['id_auteur'], '&')."'>\n";
					echo _T('sondages:retirer_auteur')."\n";
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
				echo "	<td><span class='verdana1'><B>"._T('sondages:ajouter_auteur')."</B></span> &nbsp;</td>";
				echo "	<td>";
				echo "		<select name='id_auteur' SIZE='1' STYLE='width: 180px;' CLASS='fondl'>";
				while ($arr = spip_fetch_array($resultat_requete)) {
					echo "				<option value='".$arr['id_auteur']."'>".propre($arr['nom'])."</option>";
				}
				echo "		</select><br/>";
				echo "	</td>";
				echo "	<td> &nbsp; <INPUT TYPE='submit' NAME='changer_auteur' VALUE='"._T('sondages:choisir')."' CLASS='fondo' STYLE='font-size:10px'></td>";
				echo "</tr>";
				echo "</table>";
				echo fin_block();
			}
		}
		fin_cadre_enfonce();
	}
	

	/**
	 * sondages_afficher_mots_cles
	 *
	 * @param int id_sondage
	 * @param boolean affiche pour modifs
	 * @author Pierre Basson
	 **/
	function sondages_afficher_mots_cles($id_sondage, $modif=false) {
		$requete_verification = 'SELECT M.id_mot AS id_mot, 
									M.titre AS titre, 
									M.type AS type 
								FROM spip_mots AS M
								INNER JOIN spip_groupes_mots AS GM ON GM.id_groupe=M.id_groupe
								WHERE GM.articles="oui"
								GROUP BY GM.titre';
		$resultat_verification = spip_query($requete_verification);
		if (@spip_num_rows($resultat_verification) > 0) {
			$titre_barre = _T('sondages:mots_cles');

			if ($modif)
				debut_cadre_enfonce('mot-cle-24.gif', false, "", bouton_block_invisible('motscles').$titre_barre);
			else
				debut_cadre_enfonce('mot-cle-24.gif', false, "", $titre_barre);

			$tableau_mots_interdits = array();

			$mots_associes = 'SELECT M.id_mot AS id_mot, 
									M.titre AS titre,
									M.type AS type
								FROM spip_mots AS M
								INNER JOIN spip_mots_sondages AS MS ON MS.id_mot=M.id_mot
								WHERE MS.id_sondage="'.$id_sondage.'"
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
						echo "<A HREF='".generer_url_ecrire("sondages_visualisation","id_sondage=$id_sondage&supprimer_mot=".$arr['id_mot'], '&')."'>\n";
						echo _T('sondages:retirer_mot')."\n";
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
					echo "	<td><span class='verdana1'><B>"._T('sondages:ajouter_mot')."</B></span> &nbsp;</td>";
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
					echo "	<td> &nbsp; <INPUT TYPE='submit' NAME='changer_mot' VALUE='"._T('sondages:choisir')."' CLASS='fondo' STYLE='font-size:10px'></td>";
					echo "</tr>";
					echo "</table>";
					echo fin_block();
				}
			}
			fin_cadre_enfonce();
		}
	}
	

	/**
	 * sondages_afficher_langue
	 *
	 * @param string lang
	 * @param boolean modif
	 * @author Pierre Basson
	 **/
	function sondages_afficher_langue($lang, $modif=false) {
		$titre_barre = _T('sondages:langue')."&nbsp; (".traduire_nom_langue($lang).")";
		$ret = liste_options_langues('changer_lang', $lang);
		if ($modif)
			debut_cadre_enfonce('langues-24.gif', false, "", bouton_block_invisible('languessondage').$titre_barre);
		else
			debut_cadre_enfonce('langues-24.gif', false, "", $titre_barre);
		if ($modif) {
			echo debut_block_invisible('languessondage');
			echo "<table border='0' width='100%' style='text-align: right'>";
			echo "<tr>";
			echo "	<td><span class='verdana1'><B>"._T('sondages:langue_ce_sondage')."</B></span> &nbsp;</td>";
			echo "	<td>";
			echo '		<select name="lang" size="1" style="width: 180px;"  CLASS="fondl">';
			echo $ret;
			echo '		</select>';
			echo "	</td>";
			echo "	<td> &nbsp; <INPUT TYPE='submit' NAME='changer_langue' CLASS='fondo' VALUE='"._T('sondages:changer')."' STYLE='font-size:10px'></td>";
			echo "</tr>";
			echo "</table>";
			echo fin_block();
		}
		fin_cadre_enfonce();
	}


	/**
	 * sondages_modifier_ordre_choix
	 *
	 * @param int id_sondage
	 * @param int id_choix
	 * @param int position
	 * @author Pierre Basson
	 **/
	function sondages_modifier_ordre_choix($id_sondage, $id_choix, $position) {
		$tous_les_choix = 'SELECT id_choix FROM spip_choix WHERE id_sondage="'.$id_sondage.'" AND id_choix!="'.$id_choix.'" ORDER BY ordre';
		$resultat_tous_les_choix = spip_query($tous_les_choix);
		if ($position == 0) {
			$tableau_choix = array();
			while ($arr = spip_fetch_array($resultat_tous_les_choix)) {
				$tableau_choix[] = $arr['id_choix'];
			}
			$tableau_final = array_merge(array($id_choix), $tableau_choix);
		} else {
			$i = 0;
			$tableau_choix_avant = array();
			$tableau_choix_apres = array();
			$deuxieme_tableau = false;
			while ($arr = spip_fetch_array($resultat_tous_les_choix)) {
				if ($position == $i)
					$deuxieme_tableau = true;
				if ($deuxieme_tableau)
					$tableau_choix_apres[] = $arr['id_choix'];
				else
					$tableau_choix_avant[] = $arr['id_choix'];
				$tableau_choix[] = $arr['id_choix'];
				$i++;
			}
			$tableau_final = array_merge($tableau_choix_avant, array($id_choix), $tableau_choix_apres);
		}

		foreach ($tableau_final as $cle => $valeur) {
			spip_query('UPDATE spip_choix SET ordre="'.$cle.'" WHERE id_choix="'.$valeur.'"');
		}

	}
	
	
?>