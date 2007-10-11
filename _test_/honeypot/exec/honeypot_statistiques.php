<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 * modif Pierre Andrews pour le plugin honeypot
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/statistiques');

// Donne la hauteur du graphe en fonction de la valeur maximale
// Doit etre un entier "rond", pas trop eloigne du max, et dont
// les graduations (divisions par huit) soient jolies :
// on prend donc le plus proche au-dessus de x de la forme 12,16,20,40,60,80,100
function honeypot_maxgraph($max) {
  $max = max(10,$max);
  $p = pow(10, strlen($max)-2);
  $m = $max/$p;
  foreach (array(100,80,60,40,20,16,12,10) as $l)
	if ($m<=$l) $maxgraph = $l*$p;
  return $maxgraph;
}

function honeypot_http_img_rien($width, $height, $style='', $title='') {
  return http_img_pack('rien.gif', $title, 
					   "width='$width' height='$height'" 
					   . (!$style ? '' : (" style='$style'"))
					   . (!$title ? '' : (" title=\"$title\"")));
}

// pondre les stats sous forme d'un fichier csv tres basique
function honeypot_statistiques_csv($filtre) {
  if($filtre)
	$q = "SELECT date, type, threat, filtre, cnt FROM spip_honeypot_stats WHERE filtre$filtre ORDER BY date";
  else
	$q = "SELECT date, type, threat, filtre, cnt FROM spip_honeypot_stats ORDER BY date";
  
  if (!autoriser('voirhoneypotstats', '')) exit;
  
  
  $filename = 'honeypot_stats.csv';
  header('Content-Type: text/csv');
  header('Content-Disposition: attachment; filename='.$filename);
  
  $s = spip_query($q);
  while ($t = spip_fetch_array($s)) {
	echo $t['date'].";".$t['cnt'].";".$t['type'].";".(intval($t['threat'])/intval($t['cnt']))."\n";
  }
}

function exec_honeypot_statistiques_dist() {
  global
    $aff_jours,
    $connect_statut,
    $couleur_claire,
    $couleur_foncee,
    $limit,
    $origine,
    $spip_lang_left;

  include_spip('base/abstract_sql');

  $filtre = intval(_request('filtre'));

  if (_request('format') == 'csv')
	return honeypot_statistiques_csv($filtre);


  $GLOBALS['accepte_svg'] = flag_svg();


  $style = "class='arial1 spip_x-small' style='color: #999999'";


  $commencer_page = charger_fonction('commencer_page', 'inc');
  echo $commencer_page(_T('honeypothttpbl:titre_page_statistiques'), "honeypot_statistiques", "honeypot_statistiques");
  echo "<br /><br />";
  gros_titre(_T('honeypothttpbl:titre_page_statistiques'));
  echo barre_onglets("honeypot_statistiques", ($filtre)?"filtre$filtre":"general");

  debut_gauche();
  debut_boite_info();
  echo "<p align='left' style='font-size:small;' class='verdana1'>"._T('honeypothttpbl:stat_info_gauche')."</p>";
  fin_boite_info();
	
  debut_droite();



  if ($connect_statut != '0minirezo') {
	echo _T('avis_non_acces_page');
	echo fin_gauche(), fin_page();
	exit;
  }


  if (!($aff_jours = intval($aff_jours))) $aff_jours = 105;

  $table = "spip_honeypot_stats";
  $where = "0=0";
  if($filtre)
	$where = "filtre=$filtre";

  for($type=0;$type<=7;$type++) {

  if ($filtre){
	$result = spip_query("SELECT SUM(cnt) AS total_absolu FROM spip_honeypot_stats WHERE filtre=$filtre AND type=$type");
	if ($row = spip_abstract_fetch($result)) {
	  $total_absolu = $row['total_absolu'];
	}	
  } else {
	$result = spip_query("SELECT SUM(cnt) AS total_absolu FROM spip_honeypot_stats WHERE type=$type");
	if ($row = spip_fetch_array($result)) {
	  $total_absolu = $row['total_absolu'];
	}
  }
	
	$result = spip_query("SELECT UNIX_TIMESTAMP(date) AS date_unix FROM $table WHERE $where AND type=$type ORDER BY date LIMIT 1");

	while ($row = spip_fetch_array($result)) {
	  $date_premier = $row['date_unix'];
	}

	$result=spip_query("SELECT UNIX_TIMESTAMP(date) AS date_unix, cnt FROM $table WHERE $where AND type=$type AND date > DATE_SUB(NOW(),INTERVAL $aff_jours DAY) ORDER BY date");

	$date_debut = '';
	$log = array();
	while ($row = spip_fetch_array($result)) {
	  $date = $row['date_unix'];
	  if (!$date_debut) $date_debut = $date;
	  $log[$date] = $row['cnt'];
	}


	// S'il y a au moins cinq minutes de stats :-)
	if (count($log)>0) {
	  // les honeypot_cnt du jour
	  $date_today = max(array_keys($log));
	  $honeypot_cnt_today = $log[$date_today];
	  // sauf s'il n'y en a pas :
	  if (time()-$date_today>3600*24) {
		$date_today = time();
		$honeypot_cnt_today=0;
	  }
		
	  // le nombre maximum
	  $max = max($log);
	  $nb_jours = floor(($date_today-$date_debut)/(3600*24));

	  $maxgraph = honeypot_maxgraph($max);
	  $rapport = 200 / $maxgraph;

	  if (count($log) < 420) $largeur = floor(450 / ($nb_jours+1));
	  if ($largeur < 1) {
		$largeur = 1;
		$agreg = ceil(count($log) / 420);	
	  } else {
		$agreg = 1;
	  }
	  if ($largeur > 50) $largeur = 50;

	  debut_cadre_relief("statistiques-24.gif",false,'',_T("honeypothttpbl:type$type"));
				
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
		
		
	  if ($date_premier < $date_debut)
		echo http_href_img(generer_url_ecrire("statistiques_visites","aff_jours=$aff_jours_plus&filtre=$filtre&type=$type",true),
						   'loupe-moins.gif',
						   "style='border: 0px; vertical-align:center;'",
						   _T('info_zoom'). '-'), "&nbsp;";
	  if ( (($date_today - $date_debut) / (24*3600)) > 30)
		echo http_href_img(generer_url_ecrire("statistiques_visites","aff_jours=$aff_jours_moins&filtre=$filtre&type=$type",true), 
						   'loupe-plus.gif',
						   "style='border: 0px; vertical-align:center;'",
						   _T('info_zoom'). '+'), "&nbsp;";
	
	
	  if($GLOBALS['accepte_svg']) {
		echo "\n<div>";
		echo "<object data='", generer_url_ecrire('honeypot_statistiques_svg',"aff_jours=$aff_jours&filtre=$filtre&type=$type",true), "' width='450' height='310' type='image/svg+xml'>";
		echo "<embed src='", generer_url_ecrire('honeypot_statistiques_svg',"aff_jours=$aff_jours&filtre=$filtre&type=$type",true), "' width='450' height='310' type='image/svg+xml' />";
		echo "</object>";
		echo "\n</div>";
		//		$total_absolu = $total_absolu + $honeypot_cnt_today;
		$test_agreg = $decal = $jour_prec = $val_prec = $total_loc =0;
		foreach ($log as $key => $value) {
		  //quand on atteint aujourd'hui, stop
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
				while (list(,$val_tab) = each($tab_moyenne)) {
				  $moyenne += $val_tab;
				}
				$moyenne = $moyenne / count($tab_moyenne);
				$moyenne = round($moyenne,2); // Pour affichage harmonieux
			  }
			}
			$total_loc = $total_loc + $value;
			reset($tab_moyenne);
			  
			$moyenne = 0;
			while (list(,$val_tab) = each($tab_moyenne)) {
			  $moyenne += $val_tab;
			}

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
			
		echo "<td style='background-color: black'>", honeypot_http_img_rien(1,200), "</td>";
			
		$test_agreg = $decal = $jour_prec = $val_prec = $total_loc =0;
			
		// Presentation graphique (rq: on n'affiche pas le jour courant)
		foreach ($log as $key => $value) {
		  // quand on atteint aujourd'hui, stop
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
														._T('honeypothttpbl:stat_info_visites')." | "
														._T('honeypothttpbl:stat_info_moyenne')." $moyenne"));
				if ($difference > 0) {	
				  echo honeypot_http_img_rien($largeur,1, 'background-color:#333333;', $tagtitle);
				  echo honeypot_http_img_rien($largeur, $hauteur_moyenne, '', $tagtitle);
				}
				echo 
				  honeypot_http_img_rien($largeur,1,'background-color:black;', $tagtitle);
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
													._T('honeypothttpbl:stat_info_visites')." ".$value));
	
			if ($hauteur > 0){
			  if ($hauteur_moyenne > $hauteur) {
				$difference = ($hauteur_moyenne - $hauteur) -1;
				echo honeypot_http_img_rien($largeur, 1,'background-color:#333333;',$tagtitle);
				echo honeypot_http_img_rien($largeur, $difference, '', $tagtitle);
				echo honeypot_http_img_rien($largeur,1, "background-color:$couleur_foncee;", $tagtitle);
				if (date("w",$key) == "0") // Dimanche en couleur foncee
				  echo honeypot_http_img_rien($largeur, $hauteur, "background-color:$couleur_foncee;", $tagtitle);
				else
				  echo honeypot_http_img_rien($largeur,$hauteur, "background-color:$couleur_claire;", $tagtitle);
			  } else if ($hauteur_moyenne < $hauteur) {
				$difference = ($hauteur - $hauteur_moyenne) -1;
				echo honeypot_http_img_rien($largeur,1,"background-color:$couleur_foncee;", $tagtitle);
				if (date("w",$key) == "0") // Dimanche en couleur foncee
				  $couleur =  $couleur_foncee;
				else
				  $couleur = $couleur_claire;
				echo honeypot_http_img_rien($largeur, $difference, "background-color:$couleur;", $tagtitle);
				echo honeypot_http_img_rien($largeur,1,"background-color:#333333;", $tagtitle);
				echo honeypot_http_img_rien($largeur, $hauteur_moyenne, "background-color:$couleur;", $tagtitle);
			  } else {
				echo honeypot_http_img_rien($largeur, 1, "background-color:$couleur_foncee;", $tagtitle);
				if (date("w",$key) == "0") // Dimanche en couleur foncee
				  echo honeypot_http_img_rien($largeur, $hauteur, "background-color:$couleur_foncee;", $tagtitle);
				else
				  echo honeypot_http_img_rien($largeur,$hauteur, "background-color:$couleur_claire;", $tagtitle);
			  }
			}
			echo honeypot_http_img_rien($largeur, 1, 'background-color:black;', $tagtitle);
			echo "</td>\n";
			
			$jour_prec = $key;
			$val_prec = $value;
		  }
		}
	
		// Dernier jour
		$hauteur = round($honeypot_cnt_today * $rapport)	- 1;
		//		$total_absolu = $total_absolu + $honeypot_cnt_today;
		echo "<td valign='bottom' width='$largeur'>";
		// prevision de honeypot_cnt jusqu'a minuit
		// basee sur la moyenne (site) ou popularite (article)
		$prevision = (1 - (date("H")*60 + date("i"))/(24*60)) * $val_popularite;
		$hauteurprevision = ceil($prevision * $rapport);
		// Afficher la barre tout en haut
		if ($hauteur+$hauteurprevision>0)
		  echo honeypot_http_img_rien($largeur, 1, "background-color:$couleur_foncee;");
		// preparer le texte de survol (prevision)
		$tagtitle= attribut_html(supprimer_tags(_T('honeypothttpbl:stat_info_aujourdhui')." $honeypot_cnt_today &rarr; ".(round($prevision,0)+$honeypot_cnt_today)));
		// afficher la barre previsionnelle
		if ($hauteurprevision>0)
		  echo honeypot_http_img_rien($largeur, $hauteurprevision,'background-color:#eeeeee;', $tagtitle);
		// afficher la barre deja realisee
		if ($hauteur>0)
		  echo honeypot_http_img_rien($largeur, $hauteur, 'background-color:#cccccc;', $tagtitle);
		// et afficher la ligne de base
		echo honeypot_http_img_rien($largeur, 1, 'background-color:black;');
		echo "</td>";


		echo "<td style='background-color: black'>",honeypot_http_img_rien(1, 1),"</td>";
		echo "</tr></table>";
		echo "</td>",
		  "<td ".http_style_background("fond-stats.gif")."  valign='bottom'>", honeypot_http_img_rien(3, 1, 'background-color:black;'),"</td>";
		echo "<td>", honeypot_http_img_rien(5, 1),"</td>";
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

	  }	  // cette ligne donne la moyenne depuis le debut

	  echo "<span class='arial1 spip_x-small'>"._T('texte_statistiques_visites')."</span>";
	  echo "<br /><table cellpadding='0' cellspacing='0' border='0' width='100%'><tr style='width:100%;'>";
	  echo "<td valign='top' style='width: 33%; ' class='verdana1'>";
	  echo _T('honeypothttpbl:stat_info_aujourdhui').$honeypot_cnt_today;

	  echo "</td>";
	  echo "<td valign='top' style='width: 33%; ' class='verdana1'>";
	  echo "<b>"._T('honeypothttpbl:stat_info_total')." ".$total_absolu."</b>";

	  $result_threat = spip_query("SELECT SUM(threat)/SUM(cnt) AS threat_total FROM $table WHERE $where AND type=$type");
	  if ($row_threat = spip_fetch_array($result_threat)) {
		$moyenne_threat = round($row_threat['threat_total'],2);
		echo "<span class='spip_x-small'><br />"._T('honeypothttpbl:stat_info_threat')." ". $moyenne_threat. "</span>";
	  }
	  echo "</td></tr></table>";		
	
	  if (count($log) > 60) {
		echo "<br />";
		echo "<span class='verdana1 spip_small'><b>"._T('honeypothttpbl:stat_info_par_mois')."</b></span>";
	
		echo "<div align='left'>";
		///////// Affichage par mois
		$result=spip_query("SELECT FROM_UNIXTIME(UNIX_TIMESTAMP(date),'%Y-%m') AS date_unix, SUM(cnt) AS total_visites  FROM $table WHERE $where AND type=$type AND date > DATE_SUB(NOW(),INTERVAL 2700 DAY) GROUP BY date_unix ORDER BY date");

		
		$i = 0;
		while ($row = spip_fetch_array($result)) {
		  $date = $row['date_unix'];
		  $honeypot_cnt = $row['total_visites'];
		  $i++;
		  $entrees["$date"] = $honeypot_cnt;
		}
		
		if (count($entrees)>0){
		
		  $max = max($entrees);
		  $maxgraph = honeypot_maxgraph($max);
		  $rapport = 200/$maxgraph;

		  $largeur = floor(420 / (count($entrees)));
		  if ($largeur < 1) $largeur = 1;
		  if ($largeur > 50) $largeur = 50;
		}
		
		echo "<table cellpadding='0' cellspacing='0' border='0'><tr>",
		  "<td ".http_style_background("fond-stats.gif").">";
		echo "<table cellpadding='0' cellspacing='0' border='0'><tr>";
		echo "<td style='background-color: black'>", honeypot_http_img_rien(1, 200),"</td>";
	
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
												  ._T('honeypothttpbl:stat_info_visites')." ".$value));

		  if ($hauteur > 0){
			if ($hauteur_moyenne > $hauteur) {
			  $difference = ($hauteur_moyenne - $hauteur) -1;
			  echo honeypot_http_img_rien($largeur, 1, 'background-color:#333333;');
			  echo honeypot_http_img_rien($largeur, $difference, '', $tagtitle);
			  echo honeypot_http_img_rien($largeur,1,"background-color:$couleur_foncee;");
			  if (ereg("-01",$key)){ // janvier en couleur foncee
				echo honeypot_http_img_rien($largeur,$hauteur,"background-color:$couleur_foncee;", $tagtitle);
			  } 
			  else {
				echo honeypot_http_img_rien($largeur,$hauteur,"background-color:$couleur_claire;", $tagtitle);
			  }
			}
			else if ($hauteur_moyenne < $hauteur) {
			  $difference = ($hauteur - $hauteur_moyenne) -1;
			  echo honeypot_http_img_rien($largeur,1,"background-color:$couleur_foncee;", $tagtitle);
			  if (ereg("-01",$key)){ // janvier en couleur foncee
				$couleur =  $couleur_foncee;
			  } 
			  else {
				$couleur = $couleur_claire;
			  }
			  echo honeypot_http_img_rien($largeur,$difference, "background-color:$couleur;", $tagtitle);
			  echo honeypot_http_img_rien($largeur,1,'background-color:#333333;',$tagtitle);
			  echo honeypot_http_img_rien($largeur,$hauteur_moyenne,"background-color:$couleur;", $tagtitle);
			}
			else {
			  echo honeypot_http_img_rien($largeur,1,"background-color:$couleur_foncee;", $tagtitle);
			  if (ereg("-01",$key)){ // janvier en couleur foncee
				echo honeypot_http_img_rien($largeur, $hauteur, "background-color:$couleur_foncee;", $tagtitle);
			  } 
			  else {
				echo honeypot_http_img_rien($largeur,$hauteur, "background-color:$couleur_claire;", $tagtitle);
			  }
			}
		  }
		  echo honeypot_http_img_rien($largeur,1,'background-color:black;', $tagtitle);
		  echo "</td>\n";
		}
		
		echo "<td style='background-color: black'>", honeypot_http_img_rien(1, 1),"</td>";
		echo "</tr></table>";
		echo "</td>",
		  "<td ".http_style_background("fond-stats.gif")." valign='bottom'>", honeypot_http_img_rien(3, 1, 'background-color:black;'),"</td>";
		echo "<td>", honeypot_http_img_rien(5, 1),"</td>";
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
	  
	  fin_cadre_relief();	  // Le bouton pour passer de svg a htm
	  
	}
  }
  
	  if ($GLOBALS['accepte_svg']) {
		$lien = 'non'; $alter = 'HTML';
	  } else {
		$lien = 'oui'; $alter = 'SVG';
	  }
		echo "\n<div align='".$GLOBALS['spip_lang_right']."' style='font-size:x-small;' class='verdana1'>
	<a href='".
		  parametre_url(self(), 'var_svg', $lien)."'>$alter</a> | <a href='".
		  parametre_url(self(), 'format', 'csv')."'>CSV</a>".
		  "</div>\n";

  echo fin_gauche(), fin_page();
}
?>
