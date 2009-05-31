<?php

if(!empty($annee) && !empty($mois) && !empty($jour)) { $format_date_sql = "date='$annee-$mois-$jour'";$show_evolution = 0;}
if(!empty($annee) && !empty($mois) && empty($jour))  { $format_date_sql = "'$annee-$mois-01' <= date AND date <= '$annee-$mois-31'";$show_evolution = 1;}


####################################################################################################
	// Affichage des mots clés
	
	
	
//	Affiche d'un nuage de tag pour test !
	echo "<a id=\"keyword-clouds\"></a>";

	$lang_mots_cles=_t("Top %s des Mots-cles");
	echo "<h2>";
	printf($lang_mots_cles,lire_config("spongespip/interface/nombre_mots_cles"));
	echo "</h2>\n";
	echo "<div>\n";
	echo "<a name=\"keyword\"></a>\n";
	
	
	$texte = _t("Les mots cles sont les elements de la recherche qui ont permis a un visiteur d'arriver sur votre site. Ils sont classes par nombre de recherche depuis les differents moteurs disponible sur Internet. En cliquant sur l'icone a cote du mot, vous accederez a un lien permettant de faire une recherche sur plusieurs moteurs afin d'etudier votre positionnement pour un mot cle donne.Vous pourrez aussi voir l'evolution de ce mots cles sur les derniers jours.");
//affiche_aide($texte,"themes/".$sps_config['default_theme']."/icones/help.png",$sps_config['aide']);

define("MIN_SIZE", 9);
define("MAX_SIZE", 30);

$req = @mysql_query("SELECT DISTINCT(mot_cle),COUNT(ip) as nbip FROM ".lire_config("spongespip/prefixe")."_statistiques WHERE $format_date_sql AND mot_cle != '' GROUP BY mot_cle HAVING COUNT(ip)>=".lire_config("spongespip/interface/nombre_occurences_mots_cles")." ORDER BY nbip DESC LIMIT 0,".lire_config("spongespip/interface/nombre_mots_cles").";") or die(mysql_error());

$min = MAX_INT;
$max = -MAX_INT;

// On récupère les mots clés les plus demandés et on mélange le tableau des réponses aléatoirement avec shuffle pour générer un beau nuage de tag.
// Fonction adaptée de Vinch.be (http://www.vinch.be/blog/2007/01/28/comment-creer-un-nuage-de-tags-en-phpmysql/)

while ($tag = mysql_fetch_assoc($req)) {
    if ($tag['nbip'] < $min) $min = $tag['nbip'];
    if ($tag['nbip'] > $max) $max = $tag['nbip'];
    $tags[] = $tag;
}

$min_size = MIN_SIZE;
$max_size = MAX_SIZE;

if(count($tags))
	{
	@shuffle($tags);
	echo "<span id=\"keytags\">";
	
	foreach ($tags as $tag) {
		$tag['size'] = @intval($min_size + (($tag['nbip'] - $min) * (($max_size - $min_size) / ($max - $min))));
		$tags_extended[] = $tag;
		$last_id = @mysql_result(mysql_query("SELECT id FROM ".lire_config("spongespip/prefixe")."_statistiques WHERE $format_date_sql AND mot_cle='".addslashes($tag['mot_cle'])."' ORDER BY id DESC LIMIT 0,1;"),0,"id");
		//$mot_cle = htmlentities(utf8_encode($mot_cle));
		$tag['mot_cle']=htmlentities($tag['mot_cle']);
		$class = "cloud-big";
		if($tag['size'] < 22) $class='cloud-medium';
		if($tag['size'] < 14) $class='cloud-small';
		if($tag['size'] < 10) $class='cloud-xsmall';
		echo "<a href=\"javascript:void(0);\" id=\"plus_details_keywords-$last_id-$show_evolution\" class=\"plus_details_keywords $class\" style=\"font-size:".$tag['size']."px;\" title=\"".$tag['nbip']."\" onmouseover=\"affiche_details('','','','".$tag['nbip']."','".$tag['nbip']."');\" onmouseout=\"cache_details();\">".stripslashes($tag['mot_cle'])."</a>\n ";
		}
	echo "</span>";
	echo "<span id=\"details-evolution\" class=\"details\"></span>";
	echo "<div id=\"details\" style=\"position:absolute;\" class=\"survol\"></div>";
	}


/*	$res_all_request = @mysql_num_rows($req);
	$i = 0;
	while($i != $res_all_request)
		{
		$mot_cle = mysql_result($req,$i,"mot_cle");
		$mot_cle = htmlentities(utf8_decode($mot_cle));
		
		$droite = @mysql_result($req,$i,"nbip");	
		affiche_table($mot_cle,$droite,0,0,$i);
	
		$i++;
		}*/
		

	echo "</div>";
?>