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


// Donne la hauteur du graphe en fonction de la valeur maximale
// Doit etre un entier "rond", pas trop eloigne du max, et dont
// les graduations (divisions par huit) soient jolies :
// on prend donc le plus proche au-dessus de x de la forme 12,16,20,40,60,80,100
function maxgraph($max) {
	$max = max(10,$max);
	$p = pow(10, strlen($max)-2);
	$m = $max/$p;
	foreach (array(100,80,60,40,20,16,12,10) as $l)
		if ($m<=$l) $maxgraph = $l*$p;
	return $maxgraph;
}

function http_img_rien($width, $height, $style='', $title='') {
	return http_img_pack('rien.gif', $title, 
		"width='$width' height='$height'" 
		. (!$style ? '' : (" style='$style'"))
		. (!$title ? '' : (" title=\"$title\"")));
}

//h.8/11 ...............
global $spip_lang_left;


if ($id_document = intval($id_document)) {
	$query = "SELECT nom, total, DATE_FORMAT(date_crea,'%d/%m/%Y') AS datecrea 
			FROM spip_dw2_doc 
			WHERE statut='actif' AND id_document ='$id_document'";
	$result = spip_query($query);

	if ($row = spip_fetch_array($result)) {
		// h.20/01/07 .. cesure ' ' sur nom/nomfichier trop long + 30 caract
		$titre = wordwrap(typo($row['nom']),30,' ',1);
		$total_absolu = $row['total'];
		$date_crea = $row['datecrea'];
		//$val_popularite = round($row['popularite']);
	}
} 
else {
	$query = "SELECT SUM(total) AS total_absolu FROM spip_dw2_doc WHERE statut='actif'";
	$result = spip_query($query);

	if ($row = spip_fetch_array($result)) {
		$total_absolu = $row['total_absolu'];
	}
}

//if ($titre) $pourarticle = " "._T('info_pour')." &laquo; $titre &raquo;";

if ($id_document) {
	if ($titre) gros_titre($titre);
	echo "<div class='verdana2' style='padding:3px;'>"._T('dw:enreg_dans_cat')." ".$date_crea."</div>";
}




//////

if (!$aff_jours) $aff_jours = 105;

if (!$origine) {

	if ($id_document){ $where = "id_doc=$id_document"; }
	else { $where = "1"; }
	
	// requete premiere date dans dw2_stats
	$query="SELECT UNIX_TIMESTAMP(date) AS date_unix FROM spip_dw2_stats ".
		"WHERE $where ORDER BY date LIMIT 0,1";
	$result = spip_query($query);
	while ($row = spip_fetch_array($result)) {
		$date_premier = $row['date_unix'];
	}

	// global sur la période (105 j. :$aff_jours)
	$query="SELECT UNIX_TIMESTAMP(date) AS date_unix, SUM(telech) AS visites FROM spip_dw2_stats ".
		"WHERE $where AND date > DATE_SUB(NOW(),INTERVAL $aff_jours DAY) AND date < NOW() GROUP BY date ORDER BY date";
	$result=spip_query($query);

	while ($row = spip_fetch_array($result)) {
		$date = $row['date_unix'];
		$visites = $row['visites'];

		$log[$date] = $visites;
		if ($i == 0) $date_debut = $date;
		$i++;
	}

	// Visites du jour
	if ($id_document) {
		$query = "SELECT telech AS visites FROM spip_dw2_stats WHERE date = NOW() AND id_doc = $id_document";
		$result = spip_query($query);
	} else {
		$query = "SELECT SUM(telech) AS visites FROM spip_dw2_stats WHERE date = NOW()";
		$result = spip_query($query);
	}
	if ($row = @spip_fetch_array($result))
		$visites_today = $row['visites'];
	else
		$visites_today = 0;

	if (count($log)>0) {
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

		// ...
		debut_cadre_relief("");
		// h.23/11
		if(!$id_document) {
			debut_band_titre($couleur_foncee);
			echo "<div align='center' class='verdana3'><b>"._T('dw:evolution_telech')."</b></div>";
			fin_bloc();
		}
		
		$largeur_abs = 420 / $aff_jours;
		
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
		  echo http_href_img(generer_url_ecrire($url_type_aff_page, $arg_aff_page.$aff_jours_plus.$pour_article),
				     'loupe-moins.gif',
				     "border='0' valign='center'",
				     _T('info_zoom'). '-'), "&nbsp;";
		if ( (($date_today - $date_debut) / (24*3600)) > 30)
		  echo http_href_img(generer_url_ecrire($url_type_aff_page, $arg_aff_page.$aff_jours_moins.$pour_article), 
				     'loupe-plus.gif',
				     "border='0' valign='center'",
				     _T('info_zoom'). '+'), "&nbsp;";
	
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
			echo "<table cellpadding=0 cellspacing=0 border=0><tr>",
			  "<td background='", _DIR_IMG_PACK, "fond-stats.gif'>";
			echo "<table cellpadding=0 cellspacing=0 border=0><tr>";
	
			echo "<td bgcolor='black'>", http_img_rien(1,200), "</td>";
	
			// Presentation graphique
			while (list($key, $value) = each($log)) {
				
				$test_agreg ++;
		
				if ($test_agreg == $agreg) {	
				
				$test_agreg = 0;
				$n++;
			
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
	
						$ce_jour=date("Y-m-d", $jour_prec+(3600*24*($i+1)));
						$jour = nom_jour($ce_jour).' '.affdate_jourcourt($ce_jour);
	
						reset($tab_moyenne);
						$moyenne = 0;
						while (list(,$val_tab) = each($tab_moyenne))
							$moyenne += $val_tab;
						$moyenne = $moyenne / count($tab_moyenne);
		
						$hauteur_moyenne = round(($moyenne) * $rapport) - 1;
						echo "<td valign='bottom' width=$largeur>";
						$difference = ($hauteur_moyenne) -1;
						$moyenne = round($moyenne,2); // Pour affichage harmonieux
						$tagtitle= attribut_html(supprimer_tags("$jour | "
						._T('dw:telechargements')." | "
						._T('info_moyenne')." $moyenne"));
						if ($difference > 0) {	
						  echo http_img_rien($largeur,1, 'background-color:#333333;', $tagtitle);
						  echo http_img_rien($largeur, $hauteur_moyenne, '', $tagtitle);
						}
						echo 
						    http_img_rien($largeur,1,'background-color:black;', $tagtitle);
						echo "</td>";
						$n++;
					}
				}
	
				$ce_jour=date("Y-m-d", $key);
				$jour = nom_jour($ce_jour).' '.affdate_jourcourt($ce_jour);
	
				$total_loc = $total_loc + $value;
				reset($tab_moyenne);
	
				$moyenne = 0;
				while (list(,$val_tab) = each($tab_moyenne))
					$moyenne += $val_tab;
				$moyenne = $moyenne / count($tab_moyenne);
			
				$hauteur_moyenne = round($moyenne * $rapport) - 1;
				$hauteur = round($value * $rapport) - 1;
				$moyenne = round($moyenne,2); // Pour affichage harmonieux
				echo "<td valign='bottom' width=$largeur>";
	
				$tagtitle= attribut_html(supprimer_tags("$jour | "
				._T('dw:telechargements')." : ".$value));
	
				if ($hauteur > 0){
					if ($hauteur_moyenne > $hauteur) {
						$difference = ($hauteur_moyenne - $hauteur) -1;
						echo http_img_rien($largeur, 1,'background-color:#333333;',$tagtitle);
						echo http_img_rien($largeur, $difference, '', $tagtitle);
						echo http_img_rien($largeur,1, "background-color:$couleur_foncee;", $tagtitle);
						if (date("w",$key) == "0") // Dimanche en couleur foncee
						  echo http_img_rien($largeur, $hauteur, "background-color:$couleur_foncee;", $tagtitle);
						else
						  echo http_img_rien($largeur,$hauteur, "background-color:$couleur_claire;", $tagtitle);
					} else if ($hauteur_moyenne < $hauteur) {
						$difference = ($hauteur - $hauteur_moyenne) -1;
						echo http_img_rien($largeur,1,"background-color:$couleur_foncee;", $tagtitle);
						if (date("w",$key) == "0") // Dimanche en couleur foncee
							$couleur =  $couleur_foncee;
						else
							$couleur = $couleur_claire;
						echo http_img_rien($largeur, $difference, "background-color:$couleur;", $tagtitle);
						echo http_img_rien($largeur,1,"background-color:#333333;", $tagtitle);
						echo http_img_rien($largeur, $hauteur_moyenne, "background-color:$couleur;", $tagtitle);
					} else {
					  echo http_img_rien($largeur, 1, "background-color:$couleur_foncee;", $tagtitle);
						if (date("w",$key) == "0") // Dimanche en couleur foncee
						  echo http_img_rien($largeur, $hauteur, "background-color:$couleur_foncee;", $tagtitle);
						else
						  echo http_img_rien($largeur,$hauteur, "background-color:$couleur_claire;", $tagtitle);
					}
				}
				echo http_img_rien($largeur, 1, 'background-color:black;', $tagtitle);
				echo "</td>\n";
			
				$jour_prec = $key;
				$val_prec = $value;
			}
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
		
			echo "<td bgcolor='black'>",http_img_rien(1, 1),"</td>";
			echo "</tr></table>";
			echo "</td>",
			  "<td background='", _DIR_IMG_PACK, "fond-stats.gif' valign='bottom'>", http_img_rien(3, 1, 'background-color:black;'),"</td>";
			echo "<td>", http_img_rien(5, 1),"</td>";
			echo "<td valign='top'><font face='Verdana,Arial,Sans,sans-serif' size=2>";
			echo "<table cellpadding=0 cellspacing=0 border=0>";
			echo "<tr><td height=15 valign='top'>";		
			echo "<font face='arial,helvetica,sans-serif' size=1><b>".round($maxgraph)."</b></font>";
			echo "</td></tr>";
			echo "<tr><td height=25 valign='middle'>";		
			echo "<font face='arial,helvetica,sans-serif' size=1 color='#999999'>".round(7*($maxgraph/8))."</font>";
			echo "</td></tr>";
			echo "<tr><td height=25 valign='middle'>";		
			echo "<font face='arial,helvetica,sans-serif' size=1>".round(3*($maxgraph/4))."</font>";
			echo "</td></tr>";
			echo "<tr><td height=25 valign='middle'>";		
			echo "<font face='arial,helvetica,sans-serif' size=1 color='#999999'>".round(5*($maxgraph/8))."</font>";
			echo "</td></tr>";
			echo "<tr><td height=25 valign='middle'>";		
			echo "<font face='arial,helvetica,sans-serif' size=1><b>".round($maxgraph/2)."</b></font>";
			echo "</td></tr>";
			echo "<tr><td height=25 valign='middle'>";		
			echo "<font face='arial,helvetica,sans-serif' size=1 color='#999999'>".round(3*($maxgraph/8))."</font>";
			echo "</td></tr>";
			echo "<tr><td height=25 valign='middle'>";		
			echo "<font face='arial,helvetica,sans-serif' size=1>".round($maxgraph/4)."</font>";
			echo "</td></tr>";
			echo "<tr><td height=25 valign='middle'>";		
			echo "<font face='arial,helvetica,sans-serif' size=1 color='#999999'>".round(1*($maxgraph/8))."</font>";
			echo "</td></tr>";
			echo "<tr><td height=10 valign='bottom'>";		
			echo "<font face='arial,helvetica,sans-serif' size=1><b>0</b></font>";
			echo "</td>";
			
			
			echo "</table>";
			echo "</font></td>";
			echo "</td></tr></table>";
			
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
		echo "<p>";
		echo "<font face='verdana,arial,helvetica,sans-serif' size='2'><b>"._T('info_visites_par_mois')."</b></font>";

		echo "<div align='left'>";
		///////// Affichage par mois
		$query="SELECT FROM_UNIXTIME(UNIX_TIMESTAMP(date),'%Y-%m') AS date_unix, SUM(telech) AS total_visites ".
			"FROM spip_dw2_stats ".
			"WHERE $where AND date > DATE_SUB(NOW(),INTERVAL 2700 DAY) GROUP BY date_unix ORDER BY date";
		$result=spip_query($query);
		
		$i = 0;
		while ($row = spip_fetch_array($result)) {
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
			echo "<td valign='bottom' width=$largeur>";

			$tagtitle= attribut_html(supprimer_tags("$mois | "
			._T('dw:telechargements')." : ".$value));

			if ($hauteur > 0){
				if ($hauteur_moyenne > $hauteur) {
					$difference = ($hauteur_moyenne - $hauteur) -1;
					echo http_img_rien($largeur, 1, 'background-color:#333333;');
					echo http_img_rien($largeur, $difference, '', $tagtitle);
					echo http_img_rien($largeur,1,"background-color:$couleur_foncee;");
					if (ereg("-01",$key)){ // janvier en couleur foncee
					  echo http_img_rien($largeur,$hauteur,"background-color:$couleur_foncee;", $tagtitle);
					} 
					else {
					  echo http_img_rien($largeur,$hauteur,"background-color:$couleur_claire;", $tagtitle);
					}
				}
				else if ($hauteur_moyenne < $hauteur) {
					$difference = ($hauteur - $hauteur_moyenne) -1;
					echo http_img_rien($largeur,1,"background-color:$couleur_foncee;", $tagtitle);
					if (ereg("-01",$key)){ // janvier en couleur foncee
						$couleur =  $couleur_foncee;
					} 
					else {
						$couleur = $couleur_claire;
					}
					echo http_img_rien($largeur,$difference, "background-color:$couleur;", $tagtitle);
					echo http_img_rien($largeur,1,'background-color:#333333;',$tagtitle);
					echo http_img_rien($largeur,$hauteur_moyenne,"background-color:$couleur;", $tagtitle);
				}
				else {
				  echo http_img_rien($largeur,1,"background-color:$couleur_foncee;", $tagtitle);
					if (ereg("-01",$key)){ // janvier en couleur foncee
					  echo http_img_rien($largeur, $hauteur, "background-color:$couleur_foncee;", $tagtitle);
					} 
					else {
					  echo http_img_rien($largeur,$hauteur, "background-color:$couleur_claire;", $tagtitle);
					}
				}
			}
			echo http_img_rien($largeur,1,'background-color:black;', $tagtitle);
			echo "</td>\n";
			
			$jour_prec = $key;
			$val_prec = $value;
		}
		
		echo "<td bgcolor='black'>", http_img_rien(1, 1),"</td>";
		echo "</tr></table>";
		echo "</td>",
		  "<td background='", _DIR_IMG_PACK, "fond-stats.gif' valign='bottom'>", http_img_rien(3, 1, 'background-color:black;'),"</td>";
		echo "<td>", http_img_rien(5, 1),"</td>";
		echo "<td valign='top'><font face='Verdana,Arial,Sans,sans-serif' size=2>";
		echo "<table cellpadding=0 cellspacing=0 border=0>";
		echo "<tr><td height=15 valign='top'>";		
		echo "<font face='arial,helvetica,sans-serif' size=1><b>".round($maxgraph)."</b></font>";
		echo "</td></tr>";
		echo "<tr><td height=25 valign='middle'>";		
		echo "<font face='arial,helvetica,sans-serif' size=1 color='#999999'>".round(7*($maxgraph/8))."</font>";
		echo "</td></tr>";
		echo "<tr><td height=25 valign='middle'>";		
		echo "<font face='arial,helvetica,sans-serif' size=1>".round(3*($maxgraph/4))."</font>";
		echo "</td></tr>";
		echo "<tr><td height=25 valign='middle'>";		
		echo "<font face='arial,helvetica,sans-serif' size=1 color='#999999'>".round(5*($maxgraph/8))."</font>";
		echo "</td></tr>";
		echo "<tr><td height=25 valign='middle'>";		
		echo "<font face='arial,helvetica,sans-serif' size=1><b>".round($maxgraph/2)."</b></font>";
		echo "</td></tr>";
		echo "<tr><td height=25 valign='middle'>";		
		echo "<font face='arial,helvetica,sans-serif' size=1 color='#999999'>".round(3*($maxgraph/8))."</font>";
		echo "</td></tr>";
		echo "<tr><td height=25 valign='middle'>";		
		echo "<font face='arial,helvetica,sans-serif' size=1>".round($maxgraph/4)."</font>";
		echo "</td></tr>";
		echo "<tr><td height=25 valign='middle'>";		
		echo "<font face='arial,helvetica,sans-serif' size=1 color='#999999'>".round(1*($maxgraph/8))."</font>";
		echo "</td></tr>";
		echo "<tr><td height=10 valign='bottom'>";		
		echo "<font face='arial,helvetica,sans-serif' size=1><b>0</b></font>";
		echo "</font></td>"; // h. 26/63 ajot du </font> (!!??)

		echo "</tr></table>";
		echo "</td></tr></table>";
		echo "</div>";
	}
	
	/////
		
	fin_cadre_relief();

}


?>
