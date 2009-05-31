<?php
if(!empty($annee) && !empty($mois) && !empty($jour)) { $format_date_sql = "date='$annee-$mois-$jour'";}
if(!empty($annee) && !empty($mois) && empty($jour))  { $format_date_sql = "'$annee-$mois-01' <= date AND date <= '$annee-$mois-31'";}
if(!empty($annee) && empty($mois) && empty($jour))	 { $format_date_sql = "'$annee-01-01' <= date AND date <= '$annee-12-31'";}


	echo "<a id=\"navigateurs\"></a>";
	echo "<h2>"._t("Navigateurs")."</h2>\n";
	echo "<div id=\"navigateurs\">\n";
	
	$texte = _t("Les navigateurs sont les logiciels utilises par les visiteurs pour consulter votre site. Ils sont classes par nombre de consultation avec ce navigateur pour la periode en cours. Pour editer la liste des navigateurs pris en compte, veuillez vous reporter a la documentation");
affiche_aide($texte,"themes/".$sps_config['default_theme']."/icones/help.png",$sps_config['aide']);
	
	$req_nav = "SELECT COUNT(logiciel) AS user_agent_total FROM ".lire_config("spongespip/prefixe")."_statistiques WHERE $format_date_sql and logiciel!='';";
	$nb_nav_total = @mysql_result(mysql_query($req_nav),0,"user_agent_total");
	
	$req_browser = @mysql_query("SELECT logiciel,COUNT(logiciel) AS nbref FROM ".lire_config("spongespip/prefixe")."_statistiques WHERE $format_date_sql AND type_logiciel!=2 GROUP BY logiciel ORDER BY nbref DESC;");
	

	$i_ua = 0;
	$totalprct = 0;
	$total = 0;
	
	while($i_ua != @mysql_num_rows($req_browser))
		{
		$logiciel = @mysql_result($req_browser,$i_ua,"logiciel");
		$nbref = @mysql_result($req_browser,$i_ua,"nbref");
		$pourcentage = @round($nbref * 100 / $nb_nav_total,1);
		$totalprct = $totalprct + $pourcentage;
		$total = $total + $nbref;
		if($nbref != 0)
			{
			$path_icone = "images/icones/useragents/".strtolower($logiciel).".png";
			if(file_exists("../".$path_icone)) {$icone = $path_icone;} else {unset($icone);}
			$gauche = "<a href=\"javascript:void(0);\" class=\"plus_details_user_agents\" id=\"user_agent-$i_ua\"><img src=\"themes/".$sps_config['default_theme']."/plus-details.png\"/></a>$logiciel";
			
			affiche_table($gauche,$nbref." - $pourcentage %",0,$icone,$i_ua);

			echo "<span id=\"details-$i_ua\" style=\"display:none;\" class=\"details\">";
			if($logiciel != 'unknown' || $logiciel != 'safari')
				{
				$req_browser_version = @mysql_query("SELECT version,COUNT(version) AS nbref FROM ".lire_config("spongespip/prefixe")."_statistiques WHERE $format_date_sql AND type_logiciel != '2' AND logiciel='$logiciel' AND version != '0' GROUP BY version ORDER BY nbref DESC;");
				
				$ivers = 0;
				$nb_ref = mysql_num_rows($req_browser_version);
				//No data for this user agent? Let's displaying a message
				if($nb_ref)
					{
					echo "<ul>";
					while(mysql_num_rows($req_browser_version) != $ivers)
						{
						$version = mysql_result($req_browser_version,$ivers,"version");
						$nbref = mysql_result($req_browser_version,$ivers,"nbref");
						echo "<li>";
						echo "<strong>$logiciel $version :</strong>";
						echo $nbref;
						echo "</li>";
						$ivers++;
						}
					echo "</ul>";
					}
				else
					{
					echo _t("Pas de donnees precises pour ce navigateur");
					}
				}
			echo "</span>";

			}
		$i_ua++;
		}
	
	affiche_table("<strong>"._t("Nombre de visites via un navigateur")."</strong>",$total." - ".@round($totalprct,1)." %",0,0,$i_ua);
	
	echo "</div>\n";
	
	
####################################################################################################
	// Affichage des agrégateurs RSS
	// RSS newsreaders display
	
	
	
	echo "<a id=\"agregateurs\"></a>";
	echo "<h2>";
	echo _t("Agregateurs");
	echo "</h2>";
	
	echo "<div id=\"agregateurs\">\n";
	
	$texte = _t("Les agregateurs sont les logiciels utilises par les visiteurs pour consulter les fils d'informations disponible sur votre site. Ils sont classes par nombre de consultation avec cet agregateur pour la periode en cours, si vous n'avez pas de valeur dans cette partie, c'est surement que votre site ne propose pas de fil d'informations au format RSS ou ATOM. Pour editer la liste des agregateurs pris en compte, veuillez vous reporter a la documentation");
//affiche_aide($texte,"themes/".$sps_config['default_theme']."/icones/help.png",$sps_config['aide']);

		
	$req_browser = mysql_query("SELECT logiciel,COUNT(logiciel) AS nbref FROM ".lire_config("spongespip/prefixe")."_statistiques WHERE $format_date_sql AND type_logiciel=2 GROUP BY logiciel ORDER BY nbref DESC;");
	

	$i_ua = 0;
	$totalprct = 0;
	$total = 0;
	
	while($i_ua != @mysql_num_rows($req_browser))
		{
		$logiciel = @mysql_result($req_browser,$i_ua,"logiciel");
		$nbref = @mysql_result($req_browser,$i_ua,"nbref");
		$pourcentage = @round($nbref * 100 / $nb_nav_total,1);
		$totalprct = $totalprct + $pourcentage;
		$total = $total + $nbref;
		if($nbref != 0)
			{
			$path_icone = "images/icones/useragents/".strtolower($logiciel).".png";
			if(file_exists("../".$path_icone)) {$icone = $path_icone;} else {unset($icone);}
			affiche_table($logiciel,$nbref." - $pourcentage %",0,$icone,$i_ua);
			}
		$i_ua++;
		}
	
	affiche_table("<strong>"._t("Nombre de visites via un agregateur")."</strong>",$total." - ".@round($totalprct,1)." %",0,0,$i_ua);
	
	echo "</div>\n";
	
####################################################################################################	
	
	echo "<a id=\"os\"></a>";
	echo "<h2>"._t("Systemes d'exploitation")."</h2>\n";

	echo "<div id=\"os\">\n";
	
	$texte = _t("Les systemes d'exploitation sont les plateformes utilises par les visiteurs pour consulter votre site. Ils sont classes par nombre de consultation avec ce systeme d'exploitation pour la periode en cours. Pour editer la liste des systemes d'exploitation pris en compte, veuillez vous reporter a la documentation");
affiche_aide($texte,"themes/".$sps_config['default_theme']."/icones/help.png",$sps_config['aide']);
	
	$req_browser = @mysql_query("SELECT plateforme,COUNT(plateforme) AS nbref FROM ".lire_config("spongespip/prefixe")."_statistiques WHERE $format_date_sql GROUP BY plateforme ORDER BY nbref DESC;");
	

	$i_ua = 0;
	$totalprct = 0;
	$total = 0;
	
	while($i_ua != @mysql_num_rows($req_browser))
		{
		$logiciel = @mysql_result($req_browser,$i_ua,"plateforme");
		$nbref = @mysql_result($req_browser,$i_ua,"nbref");
		$pourcentage = @round($nbref * 100 / $nb_nav_total,1);
		$totalprct = $totalprct + $pourcentage;
		$total = $total + $nbref;
		if($nbref != 0)
			{
			$path_icone = "images/icones/useragents/".strtolower($logiciel).".png";
			if(file_exists("../".$path_icone)) {$icone = $path_icone;} else {unset($icone);}
			affiche_table($logiciel,$nbref." - $pourcentage %",0,$icone,$i_ua);
			}
		$i_ua++;
		}
	
	echo "</div>\n";

?>