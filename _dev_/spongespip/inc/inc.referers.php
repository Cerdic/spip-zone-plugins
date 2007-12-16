<?php
$annee = (_request("annee")) ? _request("annee") : '';
$mois = (_request("mois")) ? _request("mois") : '';
$jour = (_request("jour")) ? _request("jour") : '';

if((!empty($annee)) && (!empty($mois)) && (!empty($jour))) { $format_date_sql = "date='$annee-$mois-$jour'";}
if(!empty($annee) && !empty($mois) && empty($jour))  { $format_date_sql = "'$annee-$mois-01' <= date AND date <= '$annee-$mois-31'";}
if(!empty($annee) && empty($mois) && empty($jour))	 { $format_date_sql = "'$annee-01-01' <= date AND date <= '$annee-12-31'";}
if(empty($annee) && empty($mois) && empty($jour))  { $format_date_sql = "'".date("Y")."-".date("m")."-01' <= date AND date <= '".date("Y")."-".date("m")."-31'";}

$longueur_chaine = 60;

$search_engines = array("google","yahoo","exalead","altavista","search","recherche","seek","lycos");

// Affichage des sites referents

foreach ($search_engines as $engine)
	{
	$req_engine .= "AND referer NOT LIKE '%$engine%' ";
	}

	echo "<a name=\"domaines-referers\"></a>";
	$lang_top_refererers = _t("Top %s des sites referents");
	echo "<h2>";printf($lang_top_refererers,lire_config("spongespip/interface/nombre_domaines_referents"));echo "</h2>\n";
	echo "<div id=\"domaines-referers\">\n";
	
	$texte = _t("Les domaines referers sont les sites qui ont amenes des visiteurs sur votre site, ils sont classes par nombre de visiteurs qu'ils ont amenes. Les moteurs de recherche les plus utilises ne sont pas affiches dans cette liste.");
	//affiche_aide($texte,"themes/".$sps_config['default_theme']."/icones/help.png",$sps_config['aide']);
		$req = @mysql_query("SELECT domaine_referer,COUNT(domaine_referer) AS nbref FROM ".lire_config("spongespip/prefixe")."_statistiques WHERE domaine_referer != '' AND $format_date_sql $req_engine GROUP BY domaine_referer ORDER BY nbref DESC LIMIT 0,".lire_config("spongespip/interface/nombre_domaines_referents").";");
	
	$res_all_request = @mysql_num_rows($req);
	$i = 0;
	while($i != $res_all_request)
	{
	$gauche = @mysql_result($req,$i,"domaine_referer");
	$droite = @mysql_result($req,$i,"nbref");
	
	if (strlen($gauche) > $longueur_chaine)
	{
		$gauche = substr($gauche, 0, $longueur_chaine)."...";
	}
	$icone = $gauche."favicon.ico";
	
	affiche_table($gauche,$droite,$gauche,$icone,$i);
	
	unset($icone);
	
	$i++;
	}
	
	echo "</div>\n";
	
	
####################################################################################################
	// Affichage des pages referentes
	// Referers display

	
	echo "<a name=\"pages-referers\"></a>";
	$lang_top_pagesrefererers = _t("Top %s des pages referentes");
	echo "<h2>";printf($lang_top_pagesrefererers,lire_config("spongespip/interface/nombre_pages_referer"));echo "</h2>\n";
	echo "<div id=\"pages-referers\">\n";
	
	$texte = _t("Les pages referers sont les pages des sites qui ont amenes des visiteurs sur votre site, ils sont classes par nombre de visiteurs qu'ils ont amenes. Les moteurs de recherche les plus utilises ne sont pas affiches dans cette liste.");
	//affiche_aide($texte,"themes/".$sps_config['default_theme']."/icones/help.png",$sps_config['aide']);
	$req_dr=mysql_query("SELECT COUNT(referer) AS nb_dr FROM ".lire_config("spongespip/prefixe")."_statistiques WHERE referer = '' AND $format_date_sql $req_engine AND referer NOT LIKE '%, %' GROUP BY referer ORDER BY nb_dr DESC LIMIT 0,".lire_config("spongespip/interface/nombre_pages_referer").";");
	$nb_dr=mysql_fetch_array($req_dr);
	$nb_dr=$nb_dr['nb_dr'];
	affiche_table(_t("Acces direct"),$nb_dr,0,$icone,$i);

	
	$req = @mysql_query("SELECT referer,COUNT(referer) AS nbref FROM ".lire_config("spongespip/prefixe")."_statistiques WHERE referer != '' AND $format_date_sql $req_engine AND referer NOT LIKE '%, %' GROUP BY referer ORDER BY nbref DESC LIMIT 0,".lire_config("spongespip/interface/nombre_pages_referer").";");
	$res_all_request = @mysql_num_rows($req);
	$i = 0;
	while($i != $res_all_request)
	{
	$referer = htmlentities(mysql_result($req,$i,"referer"));
	$refererdisplay = $referer;
	
	if (strlen($referer) > $longueur_chaine)
	{
		$refererdisplay = substr($referer, 0, $longueur_chaine)."...";
	}
	
	$droite = @mysql_result($req,$i,"nbref");
	
	$icone = ereg_replace("(http://[^/]*/)(.*)", "\\1", $referer)."/favicon.ico";

	affiche_table($refererdisplay,$droite,$referer,$icone,$i);
	$i++;
	}

	unset($icone);	
	echo "</div>";
	
	
// Affichage des moteurs de recherche referents

$imot=0;
foreach ($search_engines as $engine)
	{
	if($imot!=0) { $req_moteur.= "OR "; }
	$req_moteur .= "referer LIKE '%$engine%'";
	$imot++;
	}

	echo "<a name=\"moteurs-referers\"></a>";
	$lang_top_refererers = _t("Moteurs de recherche");
	echo "<h2>";printf($lang_top_refererers,lire_config("spongespip/interface/nombre_domaines_referents"));echo "</h2>\n";
	echo "<div id=\"domaines-referers\">\n";
	
	$texte = _t("Les moteurs de recherche sont les sites qui ont amenes des visiteurs sur votre site par une recherche par mot cle, ils sont classes par nombre de visiteurs qu'ils ont amenes. Les 100 premiers moteurs sont affiches.");
	
	//affiche_aide($texte,"themes/".$sps_config['default_theme']."/icones/help.png",$sps_config['aide']);
	
	$req = @mysql_query("SELECT domaine_referer,COUNT(domaine_referer) AS nbref FROM ".lire_config("spongespip/prefixe")."_statistiques WHERE $format_date_sql AND ($req_moteur) GROUP BY domaine_referer ORDER BY nbref DESC LIMIT 0,100;");
	
	$res_all_request = @mysql_num_rows($req);
	$i = 0;
	while($i != $res_all_request)
	{
	$gauche = @mysql_result($req,$i,"domaine_referer");
	$droite = @mysql_result($req,$i,"nbref");
	
	if (strlen($gauche) > $longueur_chaine)
	{
		$gauche = substr($gauche, 0, $longueur_chaine)."...";
	}
	$icone = $gauche."favicon.ico";
	
	affiche_table($gauche,$droite,$gauche,$icone,$i);
	
	unset($icone);
	
	$i++;
	}
	
	echo "</div>\n";
	
	
?>