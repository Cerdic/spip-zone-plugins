<?php
# DW2 -- stats-graph des téléchargements.
# Script original :
#
/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2005                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/
#
# Modifié pour la circonstance : DW2 2.1 (20/07/2006) - SPIP 1.9
# Scoty - koakidi.com
# (2.13 - prefix .. spip_)
# 
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/statistiques');

// Donne la hauteur du graphe en fonction de la valeur maximale
// Doit etre un entier "rond", pas trop eloigne du max, et dont
// les graduations (divisions par huit) soient jolies :
// on prend donc le plus proche au-dessus de x de la forme 12,16,20,40,60,80,100

/* dans inc/statistiques 
function maxgraph($max) {
function http_img_rien($width, $height, $style='', $title='') {
*/

// cf statistiques_zoom
function statistiques_zoom_document($id_document, $largeur_abs, $date_premier, $date_debut, $date_fin)
{
		if ($largeur_abs > 1) {
			$inc = ceil($largeur_abs / 5);
			$aff_jours_plus = 420 / ($largeur_abs - $inc);
			$aff_jours_moins = 420 / ($largeur_abs + $inc);
		}
		
		if ($largeur_abs == 1) {
			$aff_jours_plus = 840;
			$aff_jour_moins = 210;
		}
		
		if ($largeur_abs < 1) {
			$aff_jours_plus = 420 * ((1/$largeur_abs) + 1);
			$aff_jours_moins = 420 * ((1/$largeur_abs) - 1);
		}
		
		$aff_jours_plus = round($aff_jours * 1.5);		
		$aff_jours_moins = round($aff_jours / 1.5);
		
		
		
		if ($id_document) {
			$pour_article="&id_document=$id_document";
			$url_type_aff_page ="dw2_popup_stats";
			$arg_aff_page = "aff_jours=";
		}
		else {
			$url_type_aff_page ="dw2_admin";
			$arg_aff_page = "page_affiche=stats&aff_jours=";
		}
		
		if ($date_premier < $date_debut )
			$zoom = http_href(generer_url_ecrire($url_type_aff_page, $arg_aff_page.$aff_jours_plus.$pour_article),
				 http_img_pack('loupe-moins.gif',
					       _T('info_zoom'). '-',
					       "style='border: 0px; vertical-align: middle;'"),
				 "&nbsp;");
		if ( (($date_fin - $date_debut) / (24*3600)) > 30)
			$zoom .= http_href(generer_url_ecrire($url_type_aff_page, $arg_aff_page.$aff_jours_moins.$pour_article),
				 http_img_pack('loupe-plus.gif',
					       _T('info_zoom'). '+',
					       "style='border: 0px; vertical-align: middle;'"),
				 "&nbsp;");
	return $zoom;
}

// cf statistiques_href
function statistiques_href_telechargement($jour, $moyenne, $script='', $value='')
{
//	$ce_jour=date("Y-m-d", $jour_prec+(3600*24*($i+1)));
	$ce_jour=date("Y-m-d H:i:s", $jour);
//	$jour = nom_jour($ce_jour).' '.affdate_jourcourt($ce_jour);						

	$title = nom_jour($ce_jour) . ' '
		. affdate_court($ce_jour)  .' '.
		" | " . _T('dw:telechargements')." $value | " ._T('info_moyenne')." "
		. round($moyenne,2);
	return attribut_html(supprimer_tags($title));
}

//h.8/11 ...............
global $spip_lang_left;


if ($id_document = intval($id_document)) {

	$result = sql_select("nom, total, DATE_FORMAT(date_crea,'%d/%m/%Y') AS datecrea",
						"spip_dw2_doc",
						"statut='actif' AND id_document ='$id_document'");

	if ($row = sql_fetch($result)) {
		// h.20/01/07 .. cesure ' ' sur nom/nomfichier trop long + 30 caract
		$titre = wordwrap(typo($row['nom']),30,' ',1);
		$total_absolu = $row['total'];
		$date_crea = $row['datecrea'];
		//$val_popularite = round($row['popularite']);
	}
} 
else {
	$result = sql_select("SUM(total) AS total_absolu",
							"spip_dw2_doc",
							"statut='actif'");

	if ($row = sql_fetch($result)) {
		$total_absolu = $row['total_absolu'];
	}
}

//if ($titre) $pourarticle = " "._T('info_pour')." &laquo; $titre &raquo;";

if ($id_document) {
	if ($titre) echo gros_titre($titre,'','',true);
	echo "<div class='verdana2' style='padding:3px;'>"._T('dw:enreg_dans_cat')." ".$date_crea."</div>";
}




//////

if (!$aff_jours) $aff_jours = 105;

if (!$origine) {

	$order='date';
	$table='spip_dw2_stats';

	if ($id_document){ $where = "id_doc=$id_document"; }
	else { $where = '1'; }
	
	// requete premiere date dans dw2_stats
	$date_premier = sql_getfetsel("UNIX_TIMESTAMP($order) AS d", $table, $where, '', $order, 1);

	// global sur la période (105 j. :$aff_jours)
		$result=sql_select("UNIX_TIMESTAMP(date) AS date_unix, SUM(telech) AS visites",
							"spip_dw2_stats ",
							"$where AND date > DATE_SUB(NOW(),INTERVAL $aff_jours DAY) AND TO_DAYS(date) < TO_DAYS(NOW())",
							"date",
							"date");

	while ($row = sql_fetch($result)) {
		$date = $row['date_unix'];
		$visites = $row['visites'];

		$log[$date] = $visites;
		if ($i == 0) $date_debut = $date;
		$i++;
	}

	// Visites du jour
	if ($id_document) {
		$result = sql_select("telech AS visites",
								"spip_dw2_stats",
								"to_days(date) = to_days(NOW()) AND id_doc = $id_document");

	} else {
		$result = sql_select("SUM(telech) AS visites",
								"spip_dw2_stats",
								"to_days(date) = to_days(NOW())");
	}
	if ($row = @sql_fetch($result))
		$visites_today = $row['visites'];
	else
		$visites_today = 0;

	if (count($log)>0) {
		// ...
		echo debut_cadre_relief("",true);
		// h.23/11
		if(!$id_document) {
			debut_band_titre($couleur_foncee);
			echo "<div align='center' class='verdana3'><b>"._T('dw:evolution_telech')."</b></div>";
			fin_bloc();
		}

		$max = max(max($log),$visites_today);
		$date_today = time();
		$nb_jours = floor(($date_today-$date_debut)/(3600*24));
		$maxgraph = maxgraph($max);
		$rapport = 200 / $maxgraph;

		if (count($log) < 420) $largeur = floor(450 / ($nb_jours+1));
		if ($largeur < 1) {
			$largeur = 1;
			$agreg = ceil(count($log) / 420);	
		} else {
			$agreg = 1;
		}
		if ($largeur > 50) $largeur = 50;
		
		$largeur_abs = 420 / $aff_jours;

		
//date_fin=date_today
		echo statistiques_zoom_document($id_document, $largeur_abs, $date_premier, $date_debut, $date_today);

	
		/*
		if ($spip_svg_plugin == 'oui') {
			echo "<div>";
			echo "<object data='statistiques_svg.php3?id_article=$id_article&aff_jours=$aff_jours' width='450' height='310' type='image/svg+xml'>";
			echo "<embed src='statistiques_svg.php3?id_article=$id_article&aff_jours=$aff_jours'  width='450' height='310' type='image/svg+xml' />";
			echo "</object>";
			echo "</div>";
		} 
		else {
		*/
// cf statistiques_tous		
			echo "\n<table cellpadding='0' cellspacing='0' border='0'><tr>" .
				"\n<td ".http_style_background("fond-stats.gif").">"
				. "\n<table cellpadding='0' cellspacing='0' border='0' class='bottom'><tr>"
				. "\n<td style='background-color: black'>" . http_img_rien(1, 200) . "</td>";
	
			// Presentation graphique
			foreach ($log as $key => $value) {
			
				$test_agreg ++;
				if ($test_agreg != $agreg) continue;
				$test_agreg = 0;
				if ($decal == 30) $decal = 0;
				$decal ++;
				$tab_moyenne[$decal] = $value;
				// Inserer des jours vides si pas d'entrees	
				if ($jour_prec > 0) {
					$ecart = floor(($key-$jour_prec)/((3600*24)*$agreg)-1);
					for ($i=0; $i < $ecart; $i++){
						if ($decal == 30) $decal = 0;
						$decal ++;
						$tab_moyenne[$decal] = $value;
						reset($tab_moyenne);
						$moyenne = statistiques_moyenne($tab_moyenne);
						$hauteur_moyenne = round(($moyenne) * $rapport) - 1;
						echo "\n<td style='width: ${largeur}px'>";
						$moyenne = round($moyenne,2); // Pour affichage harmonieux
						// cf statistiques_vides
						/*
						$ce_jour=date("Y-m-d", $jour_prec+(3600*24*($i+1)));
						$jour = nom_jour($ce_jour).' '.affdate_jourcourt($ce_jour);						
						$tagtitle= attribut_html(supprimer_tags("$jour | "
						._T('dw:telechargements')." | "
						._T('info_moyenne')." $moyenne"));
						*/
						$tagtitle = statistiques_href_telechargement($jour_prec+(3600*24*($i+1)), $moyenne, '', '');
						if ($hauteur_moyenne > 1) {
							echo http_img_rien($largeur,1, 'trait_moyen', $tagtitle)
								. http_img_rien($largeur, $hauteur_moyenne, '', $tagtitle);						  
						}
						echo 
							http_img_rien($largeur,1,'trait_bas', $tagtitle);
						echo "</td>";
					}
				}
	
				$ce_jour=date("Y-m-d", $key);
				$jour = nom_jour($ce_jour).' '.affdate_jourcourt($ce_jour);
	
				$total_loc = $total_loc + $value;
				reset($tab_moyenne);
				/*
				$moyenne = 0;
				while (list(,$val_tab) = each($tab_moyenne))
					$moyenne += $val_tab;
				$moyenne = $moyenne / count($tab_moyenne);
				*/
				$moyenne = statistiques_moyenne($tab_moyenne);
			
				$hauteur_moyenne = round($moyenne * $rapport) - 1;
				$hauteur = round($value * $rapport) - 1;
				$moyenne = round($moyenne,2); // Pour affichage harmonieux
				echo "\n<td style='width: ${largeur}px'>";
	
				$tagtitle= attribut_html(supprimer_tags("$jour | "
				._T('dw:telechargements')." : ".$value));

				$dimanche = (date("w",$key) == "0");
				// cf statistiques_jour
				if ($hauteur > 0){
					$couleur = $dimanche ? "couleur_dimanche" :  "couleur_jour";
					if ($hauteur_moyenne > $hauteur) {
						$difference = ($hauteur_moyenne - $hauteur) -1;

						echo http_img_rien($largeur, 1,'trait_moyen',$tagtitle)
							. http_img_rien($largeur, $difference, '', $tagtitle)
							. http_img_rien($largeur, 1, "trait_haut", $tagtitle)
							. http_img_rien($largeur, $hauteur, $couleur, $tagtitle);
						
					} else if ($hauteur_moyenne < $hauteur) {
						$difference = ($hauteur - $hauteur_moyenne) -1;

						echo http_img_rien($largeur,1,"trait_haut", $tagtitle)
							. http_img_rien($largeur, $difference, $couleur, $tagtitle)
							. http_img_rien($largeur,1,"trait_moyen", $tagtitle)
							. http_img_rien($largeur, $hauteur_moyenne, $couleur, $tagtitle);
						
					} else {
						echo http_img_rien($largeur, 1, "trait_haut", $tagtitle)
							. http_img_rien($largeur, $hauteur, $couleur, $tagtitle);
					}
				}
				echo http_img_rien($largeur,1,"trait_haut", $tagtitle);
				echo "</td>\n";
			
				$jour_prec = $key;
				$val_prec = $value;
			}
	
			// Dernier jour
			$hauteur = round($visites_today * $rapport)	- 1;
			//$total_absolu = $total_absolu + $visites_today;
			echo "<td valign='bottom' width=$largeur>";
			if ($hauteur > 0){
			  echo http_img_rien($largeur, 1, "background-color:$couleur_foncee;");
	
				// prevision de visites jusqu'a minuit
				// basee sur la moyenne (site) ou popularite (article)
				if (! $id_document) $val_popularite = $moyenne;
				$prevision = (1 - (date("H")*60 - date("i"))/(24*60)) * $val_popularite;
				$hauteurprevision = ceil($prevision * $rapport);
				$prevision = round($prevision,0)+$visites_today; // Pour affichage harmonieux
				$tagtitle= attribut_html(supprimer_tags(_T('info_aujourdhui')." $visites_today &rarr; $prevision"));
				echo http_img_rien($largeur, $hauteurprevision,'background-color:#eeeeee;', $tagtitle);
	
				echo http_img_rien($largeur, $hauteur, 'background-color:#cccccc;', $tagtitle);
			}
			echo http_img_rien($largeur, 1, 'background-color:black;');
			echo "</td>";

			echo "\n<td style='background-color: black'>"
				. http_img_rien(1, 1) ."</td>"
				. "</tr></table>"
				. "</td>\n<td "
				. http_style_background("fond-stats.gif")."  valign='bottom'>"
				. http_img_rien(3, 1, 'trait_bas') ."</td>"
				. "\n<td>" . http_img_rien(5, 1) ."</td>"
				. "\n<td valign='top'>"
				. statistiques_echelle($maxgraph)
				. "</td></tr></table>";
				
			echo "<div style='position: relative; height: 15px;'>";
			$gauche_prec = -50;
			for ($jour = $date_debut; $jour <= $date_today; $jour = $jour + (24*3600)) {
				$ce_jour = date("d", $jour);
				
				if ($ce_jour == "1") {
					$afficher = nom_mois(date("Y-m-d", $jour));
					if (date("m", $jour) == 1) $afficher = "<b>".annee(date("Y-m-d", $jour))."</b>";
					
				
					$gauche = ($jour - $date_debut) * $largeur / ((24*3600)*$agreg);
					
					if ($gauche - $gauche_prec >= 40 OR date("m", $jour) == 1) {									
						echo "<div class='arial0' style='border-$spip_lang_left: 1px solid black; padding-$spip_lang_left: 2px; padding-top: 3px; position: absolute; $spip_lang_left: ".$gauche."px; top: -1px;'>".$afficher."</div>";
						$gauche_prec = $gauche;
					}
				}
			}
			echo "</div>";
			
		//}

		// cette ligne donne la moyenne depuis le debut
		// (desactive au profit de la moeynne "glissante")
		# $moyenne =  round($total_absolu / ((date("U")-$date_premier)/(3600*24)));

		echo "<div class='verdana2'>"._T('texte_statistiques_visites')."</div>";
		echo "<p><table cellpadding=0 cellspacing=0 border=0 width='100%'><tr width='100%'>";
		echo "<td valign='top' width='33%'><span class='verdana2'>";
		echo _T('info_maximum')." ".$max;
		echo "<br>"._T('info_moyenne')." ".round($moyenne);
		echo "</span></td>";
		echo "<td valign='top' width='33%'><span class='verdana2'>";
		echo _T('info_aujourdhui').' '.$visites_today;
		if ($val_prec > 0) echo "<br>"._T('info_hier').' '.$val_prec;
		//if ($id_article) echo "<br>"._T('info_popularite_5').' '.$val_popularite;

		echo "</span></td>";
		echo "<td valign='top' width='33%'><span class='verdana2'>";
		echo "<b>"._T('info_total')." ".$total_absolu."</b>";
		
		if ($id_document) {
			if ($classement[$id_document] > 0) {
				if ($classement[$id_document] == 1)
				      $ch = _T('info_classement_1', array('liste' => $liste));
				else
				      $ch = _T('info_classement_2', array('liste' => $liste));
				echo "<br>".$classement[$id_document].$ch;
			}
		}
		echo "<span></td></tr></table>";	
	}		
	
	if (count($log) > 60) {
	
		// voir statistiques_par_mois
		echo "<p>";
		echo "<font face='verdana,arial,helvetica,sans-serif' size='2'><b>"._T('info_visites_par_mois')."</b></font>";

		echo "<div align='left'>";
		///////// Affichage par mois
		$result=sql_select("FROM_UNIXTIME(UNIX_TIMESTAMP(date),'%Y-%m') AS date_unix, SUM(telech) AS total_visites ",
							"spip_dw2_stats",
							"$where AND date > DATE_SUB(NOW(),INTERVAL 2700 DAY)",
							"date_unix",
							"date");

		$i = 0;
		while ($row = sql_fetch($result)) {
			$date = $row['date_unix'];
			$visites = $row['total_visites'];
			$i++;
			$entrees["$date"] = $visites;
		}
		
		if (count($entrees)>0){
		
			$max = max($entrees);
			$maxgraph = maxgraph($max);
			$rapport = 200/$maxgraph;

			$largeur = floor(420 / (count($entrees)));
			if ($largeur < 1) $largeur = 1;
			if ($largeur > 50) $largeur = 50;
		}
		
		echo "<table cellpadding=0 cellspacing=0 border=0><tr>",
		  "<td background='", _DIR_IMG_PACK, "fond-stats.gif'>";
		echo "<table cellpadding=0 cellspacing=0 border=0><tr>";
		echo "<td bgcolor='black'>", http_img_rien(1, 200),"</td>";
	
		// Presentation graphique
		$n = 0;
		$decal = 0;
		$tab_moyenne = "";
			
		while (list($key, $value) = each($entrees)) {
			$n++;
			
			$mois = affdate_mois_annee($key);

			if ($decal == 30) $decal = 0;
			$decal ++;
			$tab_moyenne[$decal] = $value;
			
			$total_loc = $total_loc + $value;
			reset($tab_moyenne);
	
			$moyenne = 0;
			while (list(,$val_tab) = each($tab_moyenne))
				$moyenne += $val_tab;
			$moyenne = $moyenne / count($tab_moyenne);
			
			$hauteur_moyenne = round($moyenne * $rapport) - 1;
			$hauteur = round($value * $rapport) - 1;
			echo "\n<td style='width: ${largeur}px'>";

			$tagtitle= attribut_html(supprimer_tags("$mois | "
			._T('dw:telechargements')." : ".$value));

			if ($hauteur > 0){
				if ($hauteur_moyenne > $hauteur) {
					$difference = ($hauteur_moyenne - $hauteur) -1;
					echo http_img_rien($largeur, 1, 'trait_moyen');
					echo http_img_rien($largeur, $difference, '', $tagtitle);
					echo http_img_rien($largeur,1,"trait_haut");
					if (preg_match(",-01,",$key)){ // janvier en couleur foncee
						echo http_img_rien($largeur,$hauteur,"couleur_janvier", $tagtitle);
					} else {
						echo http_img_rien($largeur,$hauteur,"couleur_mois", $tagtitle);
					}
				}
				else if ($hauteur_moyenne < $hauteur) {
				
					$difference = ($hauteur - $hauteur_moyenne) -1;
					echo http_img_rien($largeur,1,"trait_haut", $tagtitle);
					if (preg_match(",-01,",$key)){ // janvier en couleur foncee
							$couleur =  'couleur_janvier';
					} else {
							$couleur = 'couleur_mois';
					}
					echo http_img_rien($largeur,$difference, $couleur, $tagtitle);
					echo http_img_rien($largeur,1,'trait_moyen',$tagtitle);
					echo http_img_rien($largeur,$hauteur_moyenne, $couleur, $tagtitle);
				}
				else {
					echo http_img_rien($largeur,1,"trait_haut", $tagtitle);
					if (preg_match(",-01,",$key)){ // janvier en couleur foncee
						echo http_img_rien($largeur, $hauteur, "couleur_janvier", $tagtitle);
					} else {
						echo http_img_rien($largeur,$hauteur, "couleur_mois", $tagtitle);
					}
				}
			}
			echo http_img_rien($largeur,1,'background-color:black;', $tagtitle);
			echo "</td>\n";
			
			$jour_prec = $key;
			$val_prec = $value;
		}
		
		echo "<td bgcolor='black'>", http_img_rien(1, 1),"</td>"
			. "</tr></table></td>"
			. "\n<td ".http_style_background("fond-stats.gif")." valign='bottom'>"
			. http_img_rien(3, 1, 'trait_bas') ."</td>"		  
			. "\n<td>" . http_img_rien(5, 1) ."</td>"
			. "\n<td valign='top'>"
			. statistiques_echelle($maxgraph)
			. "</td></tr></table>";
		echo "</div>";
	}
	
	/////
		
	echo fin_cadre_relief(true);

}

// http://doc.spip.org/@http_href_img
function http_href_img($href, $img, $att, $alt, $title='', $style='', $class='', $evt='') {
	if (!$title) $title = $alt;
	return  http_href($href, http_img_pack($img, $alt, $att), $title, $style, $class, $evt);
}

?>