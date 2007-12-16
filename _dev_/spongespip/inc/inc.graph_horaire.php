<?php
echo "bla";

if(!empty($annee) && !empty($mois) && !empty($jour)) { $format_date_sql = "date='$annee-$mois-$jour'";}
if(!empty($annee) && !empty($mois) && empty($jour))  { $format_date_sql = "'$annee-$mois-01' <= date AND date <= '$annee-$mois-31'";}
if(!empty($annee) && empty($mois) && empty($jour))	 { $format_date_sql = "'$annee-01-01' <= date AND date <= '$annee-12-31'";}



echo "<a name=\"plages-horaires\"></a>";
echo "<h2>"._t("Plages horaires de frequentation")."</h2>";

echo "<div id=\"horaires\">";

$texte = _t("Ce graphique affiche la repartition horaire des visiteurs pour la periode en cours, l'heure de visite prise en compte est celle de la premiere visite de la journee.");
affiche_aide($texte,"themes/".$sps_config['default_theme']."/icones/help.png",$sps_config['aide']);

echo "<table cellpadding=\"0\" cellspacing=\"0\" style=\"margin-left:auto;margin-right:auto;\"><tr>";




$i = 0;
while($i != 24)
	{
	if(strlen($i) == 1) { $iheure = "0".$i; } else { $iheure = $i; }
	$req_horaire="SELECT id FROM ".lire_config("spongespip/prefixe")."_statistiques WHERE $format_date_sql AND heure=$iheure;";
	$req_horaire = mysql_query($req_horaire);
	$table_horaire[$i] = @mysql_num_rows($req_horaire);
	$i++;
	}
	
 $table_sort_horaire = $table_horaire;

  @sort($table_sort_horaire);
  @reset($table_sort_horaire);
  while (list ($key, $val) = @each ($table_sort_horaire)) {
    $h_max = $val;
  } 
  

$size = 160;
@ $coef_h = $size / $h_max;

$ihoraire = 0;
while($ihoraire != 24)
	{
	$heure_visit = $table_horaire[$ihoraire];
	$long_h = round($heure_visit * $coef_h);
	echo "<td style=\"vertical-align:bottom;\" ";
	if($long_h != 0) echo "onmouseover=\"affiche_details('','','','".$heure_visit."','".$heure_visit."');\" onmouseout=\"cache_details();\"";
	echo ">";
	echo "<div id=\"horaire-$ihoraire\" class=\"barre-horaire\" style=\"height:".$long_h."px;\"></div>";
	echo "</td>\n";
	$ihoraire++;
	}

	
echo "</tr><tr>";
$i = 0;
while($i != 24)
	{
	echo "<td style=\"text-align:center\" class=\"color-horaire\">$i</td>\n";
	$i++;
	}

echo "</tr></table>";

echo "</div>";
?>