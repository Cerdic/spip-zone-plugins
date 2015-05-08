<?php
/*
+--------------------------------------------+
| ACTIVITE DU JOUR v. 2.1 - 06/2011 - SPIP 2.1
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| D. Chiche . pour la maj 2.0
| T. Payet . pour la maj 2.1
| Script certifie KOAK2.0 strict, mais si !

+--------------------------------------------+
| mai/08 - Reprise et adaptation de exec/statistiques_visites.php
+--------------------------------------------+
*/

// if (!defined("_ECRIRE_INC_VERSION")) return;

// include_spip('inc/presentation');
#1.9.2x :
// include_spip('inc/statistiques');




// Donne la hauteur du graphe en fonction de la valeur maximale
// Doit etre un entier "rond", pas trop eloigne du max, et dont
// les graduations (divisions par huit) soient jolies :
// on prend donc le plus proche au-dessus de x de la forme 12,16,20,40,60,80,100
// http://doc.spip.org/@maxgraph
function maxgraph($max) {
	$max = max(10,$max);
	$p = pow(10, strlen($max)-2);
	$m = $max/$p;
	foreach (array(100,80,60,40,20,16,12,10) as $l)
		if ($m<=$l) $maxgraph = $l*$p;
	return $maxgraph;
}



// http://doc.spip.org/@http_img_rien
function http_img_rien($width, $height, $style='', $title='') {

	return http_img_pack('rien.gif', $title, 
		"width='$width' height='$height'" 
		. (!$style ? '' : (" style='$style'"))
		. (!$title ? '' : (" title=\"$title\"")));
}

#
#
#

// FONCTIONS QUI ONT DISPARU DANS SPIP 2.0 Fichier ecrire/inc/minipres.php
////////////////////////////////////////////////////////////////////////////

// http://doc.spip.org/@http_href_img
function http_href_img($href, $img, $att, $alt, $title='', $style='', $class='', $evt='') {
	if (!$title) $title = $alt;
	return  http_href($href, http_img_pack($img, $alt, $att), $title, $style, $class, $evt);
}
////////////////////////////////////////////////////////////////////////////
function exec_actijour_graph() {

	# elements spip
	global 	
	$aff_jours,
    $connect_statut,$connect_toutes_rubriques,
    $couleur_claire,
    $couleur_foncee,
    $id_article,
    $limit,
    $origine,
    $spip_lang_left;

		
	$id_article=intval(_request('id_article'));
	$aff_jours = intval(_request('aff_jours'));


##
include_spip('inc/headers');
http_no_cache();
include_spip('inc/commencer_page');
# + echo sur fonction :

# Ajout pour emp�cher un probl�me d'affichage avec Spip 2.0
echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>";
#####################################

 echo init_entete(_T('actijour:graph_article_dpt').$id_article,'');


##


?>
	
<?php
echo "<body>\n";


if ($id_article) {
	$q = sql_select("titre, visites, popularite, 
					DATE_FORMAT(date,'%d/%m/%y') AS date_ed, 
					DATE_FORMAT(date_redac,'%d/%m/%y') AS date_red 
					FROM spip_articles 
					WHERE statut='publie' AND id_article ='$id_article'"
					);

	if ($row = sql_fetch($q)) {
		$titre = typo($row['titre']);
		$total_absolu = $row['visites'];
		$val_popularite = round($row['popularite']);
		$date_edit = $row['date_ed'];
		$date_redac = $row['date_red'];
		
	}
} 
else {
	$query = sql_select("SUM(visites) AS total_absolu FROM spip_visites");

	if ($row = sql_fetch($query)) {
		$total_absolu = $row['total_absolu'];
	}
}


#
# affichage bloc page ##########################################################
# 
echo "<div style='margin:5px;'>"; // (marge) ferme en fin de page !
	echo gros_titre($titre, "", false);
	
	# acces page : admin principal
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}



/*---------------------------------------------------------------------------*\
 * les barre_graph ( Script SPIP )
\*---------------------------------------------------------------------------*/

 if (!($aff_jours = intval($aff_jours))) $aff_jours = 105;

 if (!$origine) {

	if ($id_article) {
		$table = "spip_visites_articles";
		$table_ref = "spip_referers_articles";
		$where = "id_article=$id_article";
	} else {
		$table = "spip_visites";
		$table_ref = "spip_referers";
		$where = "0=0";
	}
	
	$result = sql_select("UNIX_TIMESTAMP(date) AS date_unix 
						FROM $table 
						WHERE $where 
						ORDER BY date LIMIT 1");

	while ($row = sql_fetch($result)) {
		$date_premier = $row['date_unix'];
	}

	$result=sql_select("UNIX_TIMESTAMP(date) AS date_unix, visites 
						FROM $table 
						WHERE $where AND date > DATE_SUB(NOW(),INTERVAL $aff_jours DAY) 
						ORDER BY date");

	$date_debut = '';
	$log = array();
	while ($row = sql_fetch($result)) {
		$date = $row['date_unix'];
		if (!$date_debut) $date_debut = $date;
		$log[$date] = $row['visites'];
	}


	// S'il y a au moins cinq minutes de stats :-)
	if (count($log)>0) {
		// les visites du jour
		$date_today = max(array_keys($log));
		$visites_today = $log[$date_today];
		// sauf s'il n'y en a pas :
		if (time()-$date_today>3600*24) {
			$date_today = time();
			$visites_today=0;
		}
		
		// le nombre maximum
		$max = max($log);
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

		debut_cadre_relief("statistiques-24.gif");
		
		
		$largeur_abs = 420 / $aff_jours;
		
		if ($largeur_abs > 1) {
			$inc = ceil($largeur_abs / 5);
			$aff_jours_plus = 420 / ($largeur_abs - $inc);
			$aff_jours_moins = 420 / ($largeur_abs + $inc);
		}
		
		if ($largeur_abs == 1) {
			$aff_jours_plus = 840;
			$aff_jours_moins = 210;
		}
		
		if ($largeur_abs < 1) {
			$aff_jours_plus = 420 * ((1/$largeur_abs) + 1);
			$aff_jours_moins = 420 * ((1/$largeur_abs) - 1);
		}

#### add. actijour 
#    les dates : periode et edition article
	$date_deb_fr=date('d/m/Y', $date_debut);
		echo "<div class='verdana2'>"._T('actijour:popup_date_debut_stats',array('date_deb_fr'=>$date_deb_fr));
		if ($id_article) {
			echo "...&nbsp;("._T('actijour:popup_date_edit_art',array('date_edit'=>$date_edit));
			if ($date_redac>0) { 
				echo _T('actijour:popup_date_redac_art',array('date_redac'=>$date_redac));
			}
			echo ")";
		}
		echo "</div><br />";
#### fin add. actijour

		$pour_article = $id_article ? "&id_article=$id_article" : '';
		
		if ($date_premier < $date_debut)
### modif actijour ... --> url
		  echo http_href_img(generer_url_ecrire("actijour_graph", "aff_jours=".$aff_jours_plus.$pour_article),
				     'loupe-moins.gif',
				     "style='border: 0px; vertical-align:center;'",
				     _T('info_zoom'). '-'), "&nbsp;";
		if ( (($date_today - $date_debut) / (24*3600)) > 30)
### modif actijour ... --> url
		  echo http_href_img(generer_url_ecrire("actijour_graph", "aff_jours=".$aff_jours_moins.$pour_article), 
				     'loupe-plus.gif',
				     "style='border: 0px; vertical-align:center;'",
				     _T('info_zoom'). '+'), "&nbsp;";
	
####### actijour --- 'svg' pas exploite pour actijour !!!	
if ($GLOBALS['accepte_svg']) {
	echo "\n<div>";
	echo "<object data='", generer_url_ecrire('statistiques_svg',"id_article=$id_article&aff_jours=$aff_jours"), "' width='450' height='310' type='image/svg+xml'>";
	echo "<embed src='", generer_url_ecrire('statistiques_svg',"id_article=$id_article&aff_jours=$aff_jours"), "' width='450' height='310' type='image/svg+xml' />";
	echo "</object>";
	echo "\n</div>";
	$total_absolu = $total_absolu + $visites_today;
	$test_agreg = $decal = $jour_prec = $val_prec = $total_loc =0;
	foreach ($log as $key => $value) {
		# quand on atteint aujourd'hui, stop
		if ($key == $date_today) break; 
		$test_agreg ++;
		if ($test_agreg == $agreg) {	
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
					$moyenne = 0;
					while (list(,$val_tab) = each($tab_moyenne))
						$moyenne += $val_tab;
					$moyenne = $moyenne / count($tab_moyenne);
					$moyenne = round($moyenne,2); // Pour affichage harmonieux
				}
			}
			$total_loc = $total_loc + $value;
			reset($tab_moyenne);

			$moyenne = 0;
			while (list(,$val_tab) = each($tab_moyenne))
				$moyenne += $val_tab;
			$moyenne = $moyenne / count($tab_moyenne);
			$moyenne = round($moyenne,2); // Pour affichage harmonieux
			$jour_prec = $key;
			$val_prec = $value;
		}
	}
} else {
	
	echo "<table cellpadding='0' cellspacing='0' border='0'><tr>",
	  "<td ".http_style_background("fond-stats.gif").">";
	echo "<table cellpadding='0' cellspacing='0' border='0'><tr>";
	
	echo "<td style='background-color: black'>", http_img_rien(1,200), "</td>";
	
	$test_agreg = $decal = $jour_prec = $val_prec = $total_loc =0;

	// Presentation graphique (rq: on n'affiche pas le jour courant)
	foreach ($log as $key => $value) {
		# quand on atteint aujourd'hui, stop
		if ($key == $date_today) break; 

		$test_agreg ++;
		
		if ($test_agreg == $agreg) {	
				
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
	
						$ce_jour=date("Y-m-d", $jour_prec+(3600*24*($i+1)));
						$jour = nom_jour($ce_jour).' '.affdate_jourcourt($ce_jour);
	
						reset($tab_moyenne);
						$moyenne = 0;
						while (list(,$val_tab) = each($tab_moyenne))
							$moyenne += $val_tab;
						$moyenne = $moyenne / count($tab_moyenne);
		
						$hauteur_moyenne = round(($moyenne) * $rapport) - 1;
						echo "<td valign='bottom' width='$largeur'>";
						$difference = ($hauteur_moyenne) -1;
						$moyenne = round($moyenne,2); // Pour affichage harmonieux
						$tagtitle= attribut_html(supprimer_tags("$jour | "
						._T('info_visites')." | "
						._T('info_moyenne')." $moyenne"));
						if ($difference > 0) {	
						  echo http_img_rien($largeur,1, 'background-color:#333333;', $tagtitle);
						  echo http_img_rien($largeur, $hauteur_moyenne, '', $tagtitle);
						}
						echo 
						    http_img_rien($largeur,1,'background-color:black;', $tagtitle);
						echo "</td>";
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
				echo "<td valign='bottom' width='$largeur'>";
	
				$tagtitle= attribut_html(supprimer_tags("$jour | "
				._T('info_visites')." ".$value));
	
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
			$total_absolu = $total_absolu + $visites_today;
			echo "<td valign='bottom' width='$largeur'>";
			// prevision de visites jusqu'a minuit
			// basee sur la moyenne (site) ou popularite (article)
			if (! $id_article) $val_popularite = $moyenne;
			$prevision = (1 - (date("H")*60 + date("i"))/(24*60)) * $val_popularite;
			$hauteurprevision = ceil($prevision * $rapport);
			// Afficher la barre tout en haut
			if ($hauteur+$hauteurprevision>0)
				echo http_img_rien($largeur, 1, "background-color:$couleur_foncee;");
			// preparer le texte de survol (prevision)
			$tagtitle= attribut_html(supprimer_tags(_T('info_aujourdhui')." $visites_today &rarr; ".(round($prevision,0)+$visites_today)));
			// afficher la barre previsionnelle
			if ($hauteurprevision>0)
				echo http_img_rien($largeur, $hauteurprevision,'background-color:#eeeeee;', $tagtitle);
				// afficher la barre deja realisee
			if ($hauteur>0)
				echo http_img_rien($largeur, $hauteur, 'background-color:#cccccc;', $tagtitle);
			// et afficher la ligne de base
			echo http_img_rien($largeur, 1, 'background-color:black;');
			echo "</td>";


			echo "<td style='background-color: black'>",http_img_rien(1, 1),"</td>";
			echo "</tr></table>";
			echo "</td>",
			  "<td ".http_style_background("fond-stats.gif")."  valign='bottom'>", http_img_rien(3, 1, 'background-color:black;'),"</td>";
			echo "<td>", http_img_rien(5, 1),"</td>";
			echo "<td valign='top'><div style='font-size:small;' class='verdana1'>";
			echo "<table cellpadding='0' cellspacing='0' border='0'>";
			echo "<tr><td height='15' valign='top'>";		
			echo "<span class='arial1 spip_x-small'><b>".round($maxgraph)."</b></span>";
			echo "</td></tr>";
			echo "<tr><td height='25' valign='middle' $style>";		
			echo round(7*($maxgraph/8));
			echo "</td></tr>";
			echo "<tr><td height='25' valign='middle'>";		
			echo "<span class='arial1 spip_x-small'>".round(3*($maxgraph/4))."</span>";
			echo "</td></tr>";
			echo "<tr><td height='25' valign='middle' $style>";		
			echo round(5*($maxgraph/8));
			echo "</td></tr>";
			echo "<tr><td height='25' valign='middle'>";		
			echo "<span class='arial1 spip_x-small'><b>".round($maxgraph/2)."</b></span>";
			echo "</td></tr>";
			echo "<tr><td height='25' valign='middle' $style>";		
			echo round(3*($maxgraph/8));
			echo "</td></tr>";
			echo "<tr><td height='25' valign='middle'>";		
			echo "<span class='arial1 spip_x-small'>".round($maxgraph/4)."</span>";
			echo "</td></tr>";
			echo "<tr><td height='25' valign='middle' $style>";		
			echo round(1*($maxgraph/8));
			echo "</td></tr>";
			echo "<tr><td height='10' valign='bottom'>";		
			echo "<span class='arial1 spip_x-small'><b>0</b></span>";
			echo "</td>";
			
			
			echo "</tr></table>";
			echo "</div></td>";
			echo "</tr></table>";
			
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
	}
		//}

		// cette ligne donne la moyenne depuis le debut
		// (desactive au profit de la moeynne "glissante")
		# $moyenne =  round($total_absolu / ((date("U")-$date_premier)/(3600*24)));

		echo "<span class='arial1 spip_x-small'>"._T('texte_statistiques_visites')."</span>";
		echo "<br /><table cellpadding='0' cellspacing='0' border='0' width='100%'><tr style='width:100%;'>";
		echo "<td valign='top' style='width: 33%; ' class='verdana1'>", _T('info_maximum')." ".$max, "<br />"._T('info_moyenne')." ".round($moyenne), "</td>";
		echo "<td valign='top' style='width: 33%; ' class='verdana1'>";
### actijour --> lien inutile sous Actijour !!
		echo /*'<a href="' . generer_url_ecrire("statistiques_referers","").'" title="'._T('titre_liens_entrants').'">'.*/
		_T('info_aujourdhui')/*.'</a> '*/.$visites_today;
		if ($val_prec > 0) 
			echo '<br />'
			/*<a href="' . generer_url_ecrire("statistiques_referers","jour=veille").'"  title="'._T('titre_liens_entrants').'">'*/
			._T('info_hier')/*'.</a> '*/.$val_prec;
### actijour
		if ($id_article) echo "<br />"._T('info_popularite_5').' '.$val_popularite;

		echo "</td>";
		echo "<td valign='top' style='width: 33%; ' class='verdana1'>";
		echo "<b>"._T('info_total')." ".$total_absolu."</b>";
		
		if ($id_article) {
			if ($classement[$id_article] > 0) {
				if ($classement[$id_article] == 1)
				      $ch = _T('info_classement_1', array('liste' => $liste));
				else
				      $ch = _T('info_classement_2', array('liste' => $liste));
				echo "<br />".$classement[$id_article].$ch;
			}
		} else {
		  echo "<span class='spip_x-small'><br />"._T('info_popularite_2')." ", ceil($GLOBALS['meta']['popularite_total']), "</span>";
		}
		echo "</td></tr></table>";	
	}		
	
	if (count($log) > 60) {
		echo "<br />";
		echo "<span class='verdana1 spip_small'><b>"._T('info_visites_par_mois')."</b></span>";

		echo "<div align='left'>";
		///////// Affichage par mois
		$result=sql_select("FROM_UNIXTIME(UNIX_TIMESTAMP(date),'%Y-%m') AS date_unix, SUM(visites) AS total_visites  FROM $table WHERE $where AND date > DATE_SUB(NOW(),INTERVAL 2700 DAY) GROUP BY date_unix ORDER BY date");

		
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
		
		echo "<table cellpadding='0' cellspacing='0' border='0'><tr>",
		  "<td ".http_style_background("fond-stats.gif").">";
		echo "<table cellpadding='0' cellspacing='0' border='0'><tr>";
		echo "<td style='background-color: black'>", http_img_rien(1, 200),"</td>";
	
		// Presentation graphique
		$decal = 0;
		$tab_moyenne = "";
			
		while (list($key, $value) = each($entrees)) {
			
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
			echo "<td valign='bottom' width='$largeur'>";

			$tagtitle= attribut_html(supprimer_tags("$mois | "
			._T('info_visites')." ".$value));

			if ($hauteur > 0){
				if ($hauteur_moyenne > $hauteur) {
					$difference = ($hauteur_moyenne - $hauteur) -1;
					echo http_img_rien($largeur, 1, 'background-color:#333333;');
					echo http_img_rien($largeur, $difference, '', $tagtitle);
					echo http_img_rien($largeur,1,"background-color:$couleur_foncee;");
					if (preg_match("-01",$key)){ // janvier en couleur foncee
					  echo http_img_rien($largeur,$hauteur,"background-color:$couleur_foncee;", $tagtitle);
					} 
					else {
					  echo http_img_rien($largeur,$hauteur,"background-color:$couleur_claire;", $tagtitle);
					}
				}
				else if ($hauteur_moyenne < $hauteur) {
					$difference = ($hauteur - $hauteur_moyenne) -1;
					echo http_img_rien($largeur,1,"background-color:$couleur_foncee;", $tagtitle);
					if (preg_match("-01",$key)){ // janvier en couleur foncee
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
					if (preg_match("-01",$key)){ // janvier en couleur foncee
					  echo http_img_rien($largeur, $hauteur, "background-color:$couleur_foncee;", $tagtitle);
					} 
					else {
					  echo http_img_rien($largeur,$hauteur, "background-color:$couleur_claire;", $tagtitle);
					}
				}
			}
			echo http_img_rien($largeur,1,'background-color:black;', $tagtitle);
			echo "</td>\n";
		}
		
		echo "<td style='background-color: black'>", http_img_rien(1, 1),"</td>";
		echo "</tr></table>";
		echo "</td>",
		  "<td ".http_style_background("fond-stats.gif")." valign='bottom'>", http_img_rien(3, 1, 'background-color:black;'),"</td>";
		echo "<td>", http_img_rien(5, 1),"</td>";
		echo "<td valign='top'><div style='font-size:small;' class='verdana1'>";
		echo "<table cellpadding='0' cellspacing='0' border='0'>";
		echo "<tr><td height='15' valign='top'>";		
		echo "<span class='arial1 spip_x-small'><b>".round($maxgraph)."</b></span>";
		echo "</td></tr>";
		echo "<tr><td height='25' valign='middle' $style>";		
		echo round(7*($maxgraph/8));
		echo "</td></tr>";
		echo "<tr><td height='25' valign='middle'>";		
		echo "<span class='arial1 spip_x-small'>".round(3*($maxgraph/4))."</span>";
		echo "</td></tr>";
		echo "<tr><td height='25' valign='middle' $style>";		
		echo round(5*($maxgraph/8));
		echo "</td></tr>";
		echo "<tr><td height='25' valign='middle'>";		
		echo "<span class='arial1 spip_x-small'><b>".round($maxgraph/2)."</b></span>";
		echo "</td></tr>";
		echo "<tr><td height='25' valign='middle' $style>";		
		echo round(3*($maxgraph/8));
		echo "</td></tr>";
		echo "<tr><td height='25' valign='middle'>";		
		echo "<span class='arial1 spip_x-small'>".round($maxgraph/4)."</span>";
		echo "</td></tr>";
		echo "<tr><td height='25' valign='middle' $style>";		
		echo round(1*($maxgraph/8));
		echo "</td></tr>";
		echo "<tr><td height='10' valign='bottom'>";		
		echo "<span class='arial1 spip_x-small'><b>0</b></span>";
		echo "</td>";

		echo "</tr></table>";
		echo "</div></td></tr></table>";
		echo "</div>";
	}
	
	/////
		
	fin_cadre_relief();


}

/*---------------------------------------------------------------------------*\
\*---------------------------------------------------------------------------*/
echo "</div>\n</body>\n</html>";

} // exec
?>
