<?php
/*
+--------------------------------------------+
| ACTIVITE DU JOUR v. 1.52 - 08/2007 - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifie KOAK2.0 strict, mais si !
+--------------------------------------------+
| recup du vieux statistiques.php3 adapte pour
| l_occassion ...
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function http_img_rien($width, $height, $style='', $title='') {
  return http_img_pack('rien.gif', $title, 
		       "width='$width' height='$height'" 
		       . (!$style ? '' : (" style='$style'"))
		       . (!$title ? '' : (" title=\"$title\"")));
}

#
#
#

function exec_actijour_graph() {

	// elements spip
	global 	$connect_statut,
			$connect_toutes_rubriques,
			$connect_id_auteur,
			$couleur_claire, $couleur_foncee,
			$spip_lang_left;
			

// reconstruire .. var=val des get et post
	// var : $outil
	// .. Option .. utiliser : $var = _request($var);
	foreach($_GET as $k => $v) { $$k=$_GET[$k]; }
	foreach($_POST as $k => $v) { $$k=$_POST[$k]; }
	
	$id_article=intval($id_article);
	$aff_jours = intval($aff_jours);
#h.09/03 adaptation 1.9.2
##
include_spip('inc/headers');
http_no_cache();
include_spip('inc/commencer_page');
# + echo sur fonction :
echo init_entete(_T('graph article : '.$id_article),'');
##


?>
	<script type="text/javascript">
	if (window.blur) window.focus();
	</script>
<?php
echo "<body>\n";

if ($id_article) {
	$query = "SELECT titre, visites, popularite, DATE_FORMAT(date,'%d/%m/%y') AS date_ed, DATE_FORMAT(date_redac,'%d/%m/%y') AS date_red FROM spip_articles WHERE statut='publie' AND id_article ='$id_article'";
	$result = spip_query($query);

	if ($row = spip_fetch_array($result)) {
		$titre = typo($row['titre']);
		$total_absolu = $row['visites'];
		$val_popularite = round($row['popularite']);
		$date_edit = $row['date_ed'];
		$date_redac = $row['date_red'];
		
	}
} 
else {
	$query = "SELECT SUM(visites) AS total_absolu FROM spip_visites";
	$result = spip_query($query);

	if ($row = spip_fetch_array($result)) {
		$total_absolu = $row['total_absolu'];
	}
}




	echo "<div style='margin:5px;'>";
	gros_titre($titre);
	

if ($connect_statut != '0minirezo') {
	echo _T('avis_non_acces_page');
	fin_page();
	exit;
}



/* ------------------------------- */


if (!$aff_jours) $aff_jours = 105;

if (!$origine) {




	if ($id_article) {
		$table = "spip_visites_articles";
		$table_ref = "spip_referers_articles";
		$where = "id_article=$id_article";
	} else {
		$table = "spip_visites";
		$table_ref = "spip_referers";
		$where = "1";
	}
	
	$query="SELECT UNIX_TIMESTAMP(date) AS date_unix FROM $table ".
		"WHERE $where ORDER BY date LIMIT 0,1";
	$result = spip_query($query);
	while ($row = spip_fetch_array($result)) {
		$date_premier = $row['date_unix'];
	}

	$query="SELECT UNIX_TIMESTAMP(date) AS date_unix, visites FROM $table ".
		"WHERE $where AND date > DATE_SUB(NOW(),INTERVAL $aff_jours DAY) ORDER BY date";
	$result=spip_query($query);

	while ($row = spip_fetch_array($result)) {
		$date = $row['date_unix'];
		$visites = $row['visites'];

		$log[$date] = $visites;
		if ($i == 0) $date_debut = $date;
		$i++;
	}

	// Visites du jour
	if ($id_article) {
		$query = "SELECT visites FROM spip_visites_articles WHERE id_article = $id_article AND date=NOW()";
		$result = spip_query($query);
	} else {
		$query = "SELECT visites FROM spip_visites WHERE date = NOW()";
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
		
		$maxgraph = substr(ceil(substr($max,0,2) / 10)."000000000000", 0, strlen($max));
	
		if ($maxgraph < 10) $maxgraph = 10;
		if (1.1 * $maxgraph < $max) $maxgraph.="0";	
		if (0.8*$maxgraph > $max) $maxgraph = 0.8 * $maxgraph;
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
			$aff_jour_moins = 210;
		}
		
		if ($largeur_abs < 1) {
			$aff_jours_plus = 420 * ((1/$largeur_abs) + 1);
			$aff_jours_moins = 420 * ((1/$largeur_abs) - 1);
		}
		
//		$aff_jours_plus = round($aff_jours * 1.5);		
//		$aff_jours_moins = round($aff_jours / 1.5);


// add. koak 
	$date_deb_fr=date('d/m/Y', $date_debut);
		echo "<div class='verdana2'>Stat. depuis le $date_deb_fr";
		if ($id_article)
			{
			echo "... (Art. Edité le $date_edit";
			if ($date_redac>0)
				{ 
				echo " - Redac. le $date_redac";
				}
			echo ")";
			}
		echo "</div><br />";
// fin add. koak
		
		if ($id_article) $pour_article="&id_article=$id_article";
		
		if ($date_premier < $date_debut)
		  echo http_href_img(generer_url_ecrire("actijour_graph", "aff_jours=".$aff_jours_plus.$pour_article), // modif du lien
				     'loupe-moins.gif',
				     "border='0' valign='center'",
				     _T('info_zoom'). '-'), "&nbsp;";
		if ( (($date_today - $date_debut) / (24*3600)) > 30)
		  echo http_href_img(generer_url_ecrire("actijour_graph", "aff_jours=".$aff_jours_moins.$pour_article),  // modif du lien
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
						._T('info_visites')." | "
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
			echo "<td valign='bottom' width=$largeur>";
			if ($hauteur > 0){
			  echo http_img_rien($largeur, 1, "background-color:$couleur_foncee;");
	
				// prevision de visites jusqu'a minuit
				// basee sur la moyenne (site) ou popularite (article)
				if (! $id_article) $val_popularite = $moyenne;
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
				
				
		$moyenne =  round($total_absolu / ((date("U")-$date_premier)/(3600*24)));

		echo "<span class='verdana1'>"._T('texte_statistiques_visites')."</span>";
		echo "<p><table cellpadding=0 cellspacing=0 border=0 width='100%'><tr width='100%'>";
		echo "<td valign='top' width='33%'><span class='verdana2'>";
		echo _T('info_maximum')." ".$max;
		echo "<br>"._T('info_moyenne')." ".round($moyenne);
		echo "</span></td>";
		echo "<td valign='top' width='33%'><span class='verdana2'>";
		echo _T('info_aujourdhui').' '.$visites_today;
		if ($val_prec > 0) echo "<br>"._T('info_hier').' '.$val_prec;
		if ($id_article) echo "<br>"._T('info_popularite_5').' '.$val_popularite;

		echo "</span></td>";
		echo "<td valign='top' width='33%'><span class='verdana2'>";
		echo "<b>"._T('info_total')." ".$total_absolu."</b>";
		
		if ($id_article) {
			if ($classement[$id_article] > 0) {
				if ($classement[$id_article] == 1)
				      $ch = _T('info_classement_1', array('liste' => $liste));
				else
				      $ch = _T('info_classement_2', array('liste' => $liste));
				echo "<br>".$classement[$id_article].$ch;
			}
		} else {
			echo "";
			echo "<br>"._T('info_popularite_2')." ";
			echo ceil(lire_meta('popularite_total'));
			echo "</span>";
		}
		echo "</td></tr></table>";	
	}		
	
	if (count($log) > 60) {
		echo "<p>";
		echo "<span class='verdana2'><b>"._T('info_visites_par_mois')."</b></span>";

		echo "<div align='left'>";
		///////// Affichage par mois
		$query="SELECT FROM_UNIXTIME(UNIX_TIMESTAMP(date),'%Y-%m') AS date_unix, SUM(visites) AS total_visites  FROM $table ".
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
			$maxgraph = substr(ceil(substr($max,0,2) / 10)."000000000000", 0, strlen($max));
			
			if ($maxgraph < 10) $maxgraph = 10;
			if (1.1 * $maxgraph < $max) $maxgraph.="0";	
			if (0.8*$maxgraph > $max) $maxgraph = 0.8 * $maxgraph;
			$rapport = 200 / $maxgraph;
	
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
			._T('info_visites')." ".$value));

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
		echo "</td>";

		echo "</tr></table>";
		echo "</td></tr></table>";
		echo "</div>";
	}
	
	/////
		
	fin_cadre_relief();

}

// +---------------------+
echo "</div>\n</body>\n</html>";

}

?>
