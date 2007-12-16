<?php
$year = _request("annee");
$mois = _request("mois");


####################################################################################################
?>

<?php
	
echo gros_titre(lire_config("spongespip/interface/nombre_visiteurs")." "._t("derniers_visiteurs"));


$texte = _t("Ce tableau affiche les derniers visiteurs ayant parcouru votre site. En cliquant sur l'icone a cote du nom d'hote, vous pourrez voir le detail de ce visiteur (adresse ip, referent, logiciel, date de premiere visite, etc...)");
	affiche_aide($texte,_DIR_PLUGIN_SPONGESPIP."img/help.png",$sps_config['aide']);

$req = mysql_query("SELECT id,host,nb_pages,heure,date FROM ".lire_config("spongespip/prefixe")."_statistiques ORDER BY id DESC LIMIT 0,".lire_config("spongespip/interface/nombre_visiteurs").";");



affiche_table("<strong>"._t("spongespip:hote")."</strong>",_t("spongespip:pages_vues"),0,0,1,1,$titre=1);
$i = 0;

$res_all_request = mysql_num_rows($req);
	
while($i != $res_all_request)
	{
	$id = mysql_result($req,$i,"id");
	if(mysql_result($req,$i,"date") == date("Y-m-d"))
		{
		$timeday = mysql_result($req,$i,"heure")."h";
		}
	else
		{
		$timeday = mysql_result($req,$i,"date");
		}
	$gauche = "<a href=\"javascript:void(0);\" id=\"plus_details-$id-last\" class=\"plus_details\" ><img src=\""._DIR_PLUGIN_SPONGESPIP."img/plus-details.png\" alt=\""._t("Plus de details")."\"/></a>"."<span style=\"font-weight:bold;;\">$timeday</span><span class=\"donnees\">".mysql_result($req,$i,"host")."</span>";
	
	$droite = mysql_result($req,$i,"nb_pages");


	affiche_table($gauche,$droite,0,0,$id);
	echo "<div id=\"details-$id\" style=\"display:visible;\" class=\"details\">";
	$details="last";
	include("plus_details.php");
	echo "</div>";
	
	$i++;
	}

?>
<!-- <script type="text/javascript">
refresh_details();
</script> -->