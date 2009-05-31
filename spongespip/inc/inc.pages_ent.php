<?php

echo "<a name=\"pages-entree\"></a>";
$lang_top_pages = _t("Top %s des pages d'entree");
echo "<h2>";printf($lang_top_pages,lire_config("spongespip/interface/nombre_pages_vues"));echo "</h2>\n";
echo "<div id=\"pages-entree\">\n";

$texte = _t("Les pages d'entree sont les premieres pages consultees par un visiteur lorsqu'il entre sur votre site. Elles sont classees par nombre de consultation pour la periode en cours.");
//affiche_aide($texte,"themes/".$sps_config['default_theme']."/icones/help.png",$sps_config['aide']);
	$req_pages_ent = @mysql_query("SELECT url_page,COUNT(url_page) AS nbip FROM ".lire_config("spongespip/prefixe")."_statistiques WHERE $format_date_sql GROUP BY url_page ORDER BY nbip DESC LIMIT 0,".lire_config("spongespip/interface/nombre_pages_entree").";");
	$res_all_request = mysql_num_rows($req_pages_ent);
	$i = 0;
	while($i != $res_all_request)
	{
	$gauche = @mysql_result($req_pages_ent,$i,"url_page");
	$droite = @mysql_result($req_pages_ent,$i,"nbip");
	$url = $gauche;
	
	
	affiche_table($gauche,$droite,$url,0,$i);

	$i++;
	}
	echo "</div>\n";