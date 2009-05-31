<?php

$mot_cle = _request("mot_cle");
$show_evolution = _request("evolution");

switch($details)
	{
	case "last":
	$req_details = mysql_query("SELECT ip,referer,logiciel,version,url_page,domaine_referer,plateforme FROM ".lire_config("spongespip/prefixe")."_statistiques WHERE id='$id';");
	$ip = mysql_result($req_details,0,"ip");
	$logiciel = mysql_result($req_details,0,"logiciel");
	$plateforme = mysql_result($req_details,0,"plateforme");
	$version = mysql_result($req_details,0,"version");
	$icone = "http://api.hostip.info/flag.php?ip=".$ip;
	$icone_logiciel = "images/icones/useragents/".strtolower($logiciel).".png";
	$icone_os = "images/icones/useragents/".strtolower($plateforme).".png";
	echo "<strong>"._t("Adresse IP")." :</strong> <img src=\"$icone\" class=\"flags\" style=\"width:16px;height:10px\" />".$ip."<br />";
	echo "<strong>"._t("Referent")." :</strong> <a href=\"".mysql_result($req_details,0,"referer")."\">".mysql_result($req_details,0,"domaine_referer")."</a><br />";
	if($version) {$logiciel = "$logiciel $version";}
	echo "<strong>"._t("Logiciel")." :</strong> <img src=\"$icone_logiciel\"  />".$logiciel." / <img src=\"$icone_os\" width=\"16\"  height=\"16\" />".$plateforme."<br />";
	echo "<strong>"._t("Page d'entree")." :</strong> <a href=\"..".mysql_result($req_details,0,"url_page")."\">".mysql_result($req_details,0,"url_page")."</a><br />";

	$req_first_date = mysql_query("SELECT date,referer,domaine_referer FROM ".lire_config("spongespip/prefixe")."_statistiques WHERE ip='".$ip."' ORDER BY id ASC LIMIT 0,1;");

	$date_ajd = date("Y-m-d");
	$first_date = mysql_result($req_first_date,0,"date");
	if($date_ajd == $first_date) { $first_date = _t("Aujourd'hui")."";$last=1; }
	echo "<strong>"._t("Premiere visite")." :</strong> ".$first_date;

	if($last != 1)
		{
		$referer = mysql_result($req_first_date,0,"referer");
		if(!empty($referer)) { echo " (<a href=\"".$referer."\">".mysql_result($req_first_date,0,"domaine_referer")."</a>)"; }
		$req_last_date = mysql_query("SELECT date FROM ".lire_config("spongespip/prefixe")."_statistiques WHERE ip='".$ip."' ORDER BY id DESC LIMIT 1,1;");
		$last_visit = @mysql_result($req_last_date,0,"date");
		echo "<br /><strong>"._t("Visite precedente")." :</strong> ".$last_visit."<br /><br />";
		echo "<a href=\"#\" class=\"lien_historique\" id=\"recherche-ip-$ip\">"._t("Voir l'historique de ce visiteur")."</a>";
		}


	break;


	case "keywords":
	

	$mot_cle = addslashes(mysql_result(mysql_query("SELECT mot_cle FROM ".lire_config("spongespip/prefixe")."_statistiques WHERE id='$id' ORDER BY id LIMIT 0,1;"),0,"mot_cle"));
	
	$mot_cle_search = htmlentities(utf8_decode(stripslashes($mot_cle)));
	
	$lang_details= _t("Detail et evolution pour le mot cle %s");
	echo "<h2>";
	printf($lang_details,"<strong>$mot_cle_search</strong>");
	echo "</h2>\n";
	
	echo "<span>";
	echo "<img src=\"http://www.google.com/favicon.ico\" class=\"icone\" /><a href=\"http://www.google.com/search?q=".$mot_cle_search."\">"._t("Voir le positionnement dans")." Google</a><br />\n";
	echo "<img src=\"http://www.exalead.fr/favicon.ico\" class=\"icone\" /><a href=\"http://www.exalead.fr/search/?q=".$mot_cle_search."\">"._t("Voir le positionnement dans")." Exalead</a><br />\n";
	echo "<img src=\"http://search.yahoo.com/favicon.ico\" class=\"icone\" /><a href=\"http://search.yahoo.com/search?p=".$mot_cle_search."\">"._t("Voir le positionnement dans")." Yahoo</a><br />\n";
	echo "<img src=\"http://search.live.com/favicon.ico\" class=\"icone\" /><a href=\"http://search.live.com/results.aspx?q=".$mot_cle_search."\">"._t("Voir le positionnement dans")." Live.com</a><br />\n";
	echo "</span>\n";


	$lang_histo=_t("Evolution sur les %s derniers jours");
	echo "<h2>";
	printf($lang_histo,$sps_config['display_historique']);
	echo "</h2>\n";
	
	if($show_evolution == 1)
		{
	
		$i_histo = 0;
		while($i_histo != $sps_config['display_historique'])
			{
			$timestamp = mktime(0, 0, 0, date("m")  , date("d")-($sps_config['display_historique']-$i_histo), date("Y"));
			$format_date = date("Y-m-d",$timestamp);
	
			$req_histo="SELECT COUNT(id) as nbref FROM ".lire_config("spongespip/prefixe")."_statistiques WHERE date='$format_date' AND mot_cle='$mot_cle'";
			$req_histo = mysql_query($req_histo);
			$table_histo[$i_histo] = @mysql_result($req_histo,0,"nbref");
			$i_histo++;
			}
	
		$table_sort_horaire = $table_histo;
	
		@sort($table_sort_horaire);
		@reset($table_sort_horaire);
		while (list ($key, $val) = @each ($table_sort_horaire)) {
		$h_max = $val;
		}
	
		$size = 80;
		@ $coef_h = $size / $h_max;
	
		echo "<div id=\"details\" style=\"position:absolute;\" class=\"survol\"></div>";
	
		echo "<table style=\"width:80%\">";
	
		$ihoraire = 0;
		while($ihoraire != $sps_config['display_historique'])
			{
			$heure_visit = $table_histo[$ihoraire];
			$long_h = round($heure_visit * $coef_h);
			echo "<td style=\"vertical-align:bottom;\" ";
			echo "onmouseover=\"affiche_details('','','','".$heure_visit."','".$heure_visit."');\" onmouseout=\"cache_details();\"";
			echo ">";
			echo "<div id=\"horaire-$ihoraire\" class=\"barre-horaire\" style=\"margin-left:auto;margin-right:auto;;width:12px;height:".$long_h."px;\"></div>";
			echo "</td>\n";
			$ihoraire++;
			}
		
	
	
		echo "</tr><tr>";
		$i = 0;
		while($i != $sps_config['display_historique'])
			{
			$timestamp = mktime(0, 0, 0, date("m")  , date("d")-($sps_config['display_historique']-$i), date("Y"));
			$format_date = date("dM",$timestamp);
			echo "<td style=\"text-align:center\">$format_date</td>\n";
			$i++;
			}
	
		echo "</tr></table>";
		
		}
	else
		{
		echo "<span>";
		echo _t("L'evolution n'est pas disponible pour les donnees quotidiennes");
		echo "</span>";
		}
		

	echo "<h2>"._t("Derniers referents pour ce mot cle")."</h2>";
	
	if($show_evolution == 1)
		{
		
	
		$top_url_key = mysql_query("SELECT DISTINCT(referer),domaine_referer FROM ".lire_config("spongespip/prefixe")."_statistiques WHERE mot_cle='$mot_cle' ORDER BY id DESC LIMIT 0,10;");
		
		echo "<span>";
		
		while ($referer = mysql_fetch_assoc($top_url_key))
		{
		echo "<a href=\"".$referer['referer']."\">".$referer['domaine_referer']."</a><br />";
		}
		
		echo "</span>";
		
		}
	else
		{
		echo "<span>";
		echo _t("Les derniers referents ne sont disponibles pour les donnees quotidiennes");
		echo "</span>";
		}

	break;

  }
?>