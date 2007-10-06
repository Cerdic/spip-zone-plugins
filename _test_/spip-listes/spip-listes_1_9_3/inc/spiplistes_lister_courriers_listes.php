<?php
// SPIP-Listes-V :: $Id$
/******************************************************************************************/
/* SPIP-listes est un systeme de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/
// Original From SPIP-Listes-V :: Id: spiplistes_afficher_en_liste.php paladin@quesaco.org
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate: 2007-10-01 08:58:08 +0200 (lun., 01 oct. 2007) $

/**
* Adaptation de spiplistes_afficher_en_liste de SL192
*
* affiche des listes d'éléments
*
* @param string titre
* @param string image
* @param string statut
* @param string recherche
* @param string nom_position
* @return string la liste des lettres pour le statut demandé @author BoOz / Pierre Basson
**/
/*
	CP 20070904
	Ce qui est affiché entre () est le nombre de destinataire
	La date affichée est celle du départ pour un courrier encour|publie|etc..
	
	Ne sert que pour la liste des courriers et des listes de diffusion
*/
function spiplistes_lister_courriers_listes ($titre_tableau, $image, $element='listes', $statut=''
	, $apres_maintenant=false, $nom_position='position'
	, $exec, $pas = 10, $return = true) {

	include_spip('inc/spiplistes_api');

	global $id_auteur
		;
	
	$position = intval($_GET[$nom_position]);
	if(!$position) $position=0 ;

	$pas = intval($pas);
	
	$clause_where = '';
	
	// construction de la requête SQL
	switch($element) {
		case 'courriers':
			$sql_query = "SELECT id_courrier, titre, date, date_debut_envoi,date_fin_envoi, nb_emails_envoyes,total_abonnes,email_test
				FROM spip_courriers
				WHERE statut="._q($statut)."
				ORDER BY date DESC
				LIMIT $position,$pas";
			break;
		case 'listes':
			if (
			// pour lister les listes programmées dans un futur 
				 (($statut == _SPIPLISTES_PRIVATE_LIST) || ($statut == _SPIPLISTES_PUBLIC_LIST)) 
				&& ($apres_maintenant == true)
				) {
				$clause_where.= " AND (maj NOT BETWEEN 0 AND NOW())";
			}
			$sql_query = "SELECT id_liste,titre,date,patron,maj
				FROM spip_listes
				WHERE statut="._q($statut)." $clause_where
				ORDER BY date DESC
				LIMIT $position,$pas";
			break;
	}
	$resultat_aff = spip_query($sql_query);
	
	if (($nb_ = @spip_num_rows($resultat_aff)) > 0) {
		
		$en_liste = ""
			. "<div class='liste'>\n"
			. "<div style='position: relative;'>\n"
			. "<div style='position: absolute; top: -12px; left: 3px;'>\n"
			. "<img src='".$image."'  />\n"
			. "</div>\n"
			. "<div style='background-color:white; color:black; padding:3px; padding-left:30px; border-bottom:1px solid #444;' class='verdana2'>\n"
			. "<strong>\n"
			. $titre_tableau
			. "</strong>\n"
			. "</div>\n"
			. "</div>\n"
			. "<table width='100%' cellpadding='2' cellspacing='0' border='0'>\n"
			;
		
		while ($row = spip_fetch_array($resultat_aff)) {
		
			$titre = $row['titre'];
			$date = $row['date'];
			
			$retour = _DIR_RESTREINT_ABS.self();
			
			switch ($element){
				case 'courriers':
					$id_row	= $row['id_courrier'];			
					$nb_emails_envoyes	= $row['nb_emails_envoyes'];
					$date_debut_envoi	= $row['date_debut_envoi'];
					$date_fin_envoi	= $row['date_fin_envoi'];
					$total_abonnes	= $row['total_abonnes'];
					$email_test	= $row['email_test'];
					$url_row	= generer_url_ecrire($exec, 'id_courrier='.$id_row);
					break;
				case 'listes':
					$id_row = $row['id_liste'];
					$url_row	= generer_url_ecrire($exec, 'id_liste='.$id_row);
					$patron = $row['patron'];
					$maj = $row['maj'];
					break;
			}
			
			$en_liste.= ""
				. "<tr class='tr_liste'>\n"
				. "<td width='11' style='vertical-align:top;'>"
				. "<img src='".spiplistes_items_get_item("puce", $statut)."' alt=\"".spiplistes_items_get_item("alt", $statut)."\" border='0' style='margin: 3px 1px 1px;' />"
				. "</td>"
				. "<td class='arial2'>\n"
				. "<div>\n"
				. "<a href=\"".$url_row."\" dir='ltr' style='display:block;'>\n"
				. $titre
				;
			
			switch($element) {
			// si courriers, donne le nombre de destinataires
				case courriers:
					$nb_abo = "";
					if($nb_emails_envoyes > 0) {
						$en_liste.= "<span style='font-size:100%;color:#666' dir='ltr'>\n"
							. "(".$nb_emails_envoyes.")\n"
							. "</span>\n"
							;
					}
					if(empty($email_test)) {
						$nb_abo = 
							($total_abonnes)
							? (
								($total_abonnes > 1)
								? $total_abonnes._T('spiplistes:nb_abonnes_plur') 
								: $total_abonnes._T('spiplistes:nb_abonnes_sing')
								)
							: 0
							;
					}
					else {
						$nb_abo = _T('spiplistes:email_adresse');
					}
					if($nb_abo) {
						$en_liste .=
							" <span style='font-size:100%;color:#666' dir='ltr'>\n"
							. "(".$nb_abo.")\n"
							. "</span>\n"
							;
					}
					break;
			// si liste, donne le nombre d'abonnés
				case 'listes':
					//$nb_abo = spiplistes_nb_abonnes_liste($id_row);
					// affiche infos complémentaires pour les listes
					$en_liste .=
						" <span style='font-size:100%;color:#666666' dir='ltr'>\n"
						. spiplistes_nb_abonnes_liste_str_get($id_row)
						. (!empty($patron) ? "<br />Patron : <strong>".$patron."</strong>" : "")
						. ((!empty($date) && ($date!=_SPIPLISTES_ZERO_TIME_DATE)) ? "<br />Prochain envoi : <strong>".affdate_heure($date)."</strong>" : "")
						. "</span>\n"
						;
						break;
			}
								
			$en_liste .= ""
				. "</a>\n"
				. "</div>\n"
				. "</td>\n"
				. "<td width='120' class='arial1'>"
				;
			// si c'est un courrier, donne la date 
			// - date debut envoi si encour
			// - sinon date de publication
			if(($element='courriers') && (!in_array($statut, array(_SPIPLISTES_STATUT_REDAC, _SPIPLISTES_STATUT_READY)))) {
				$en_liste .= ""
					. affdate_heure(($statut==_SPIPLISTES_STATUT_ENCOURS) ? $date_debut_envoi : $date_fin_envoi)
					;
			}
			$en_liste .= ""
				. "</td>\n"
				. "<td width='50' class='arial1'><strong>"._T('spiplistes:numero').$id_row."</strong></td>\n"
				. "</tr>\n"
				;
		}
		$en_liste.= "</table>\n";
		
		switch ($element){
			case "courriers":
				$requete_total = "SELECT id_courrier
					FROM spip_courriers
					WHERE statut="._q($statut);
				$retour = _SPIPLISTES_EXEC_COURRIERS_LISTE;
				$param = "&statut=$statut";
				break;
			case "listes":
				$requete_total = "SELECT id_liste
					FROM spip_listes
					WHERE statut="._q($statut)." ".$clause_where."
					ORDER BY date DESC";
				$retour = _SPIPLISTES_EXEC_LISTES_LISTE;
				$param = "";
				break;
		}
		
		$resultat_total = spip_query($requete_total);
		$total = spip_num_rows($resultat_total);

		$en_liste .= 
			spiplistes_afficher_pagination($retour, $param, $total, $position, $nom_position, $pas)
			. "</div>\n"
			. "<br />\n"
			;
	}

	if($return) return($en_liste);
	else echo($en_liste);
}



/**
* adapte de lettres_afficher_pagination
*
* @param string fond
* @param string arguments
* @param int total
* @param int position
* @author Pierre Basson
**/
function spiplistes_afficher_pagination($fond, $arguments, $total, $position, $nom, $pas = 10) {

	$pagination = '';
	$i = 0;

	$nombre_pages = floor(($total-1)/$pas)+1;

	if($nombre_pages>1) {
	
		$pagination.= "<div style='background-color: white; color: black; padding: 3px; padding-left: 30px;  padding-right: 40px; text-align: right;' class='verdana2'>\n";
		while($i<$nombre_pages) {
			$url = generer_url_ecrire($fond, $nom.'='.strval($i*$pas).$arguments);
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

	return ($pagination);
}

?>
