<?php
/*
 * valide_site
 *
 * outil de validation w3c et accessibilite du site
 *
 * Auteur : cedric.morin@yterium.com
 * © 2006 - Distribue sous licence GPL
 *
 */
function exec_w3c_go_home(){
	global $connect_statut;
	
	include_spip ("inc/presentation");

	//
	// Recupere les donnees
	//

	if ($connect_statut != '0minirezo') {
		debut_page(_L("Validation Site W3C"), "w3c", "w3c");
		debut_gauche();
		debut_droite();
		echo "<strong>"._T('avis_acces_interdit')."</strong>";
		fin_page();
		exit;
	}

	if (isset($_GET['reset']))
	{
		effacer_meta('xhtml_access_compliance');
		ecrire_metas();
		$url=generer_url_ecrire("w3c_go_home");
		redirige_par_entete($url);
	}
	
	debut_page(_L("Validation Site W3C"), "w3c", "w3c");
	debut_gauche();
	debut_droite();

	$sitemappath=generer_url_public("sitemap");
	include_spip('inc/distant');
	$xml_sitemap=recuperer_page($sitemappath);

	include_spip('inc/plugin');
	$sitemap=parse_plugin_xml($xml_sitemap);
	$sitemap = reset($sitemap);
	$sitemap = reset($sitemap);
	if (isset($sitemap['url']))
		$sitemap=$sitemap['url'];
	else
		$sitemap=array();

/*	$table_url[]=generer_url_public("recherche","recherche=conseil");	$urlcount++;
	$table_url[]=generer_url_public("recherche","recherche=municipal"); $urlcount++;
	$table_url[]=generer_url_public("recherche","recherche=ecole");	$urlcount++;
	$table_url[]=generer_url_public("recherche","recherche=mairie");	$urlcount++;
	$table_url[]=generer_url_public("recherche","recherche=permis");	$urlcount++;
	$table_url[]=generer_url_public("article","id_article=6");	$urlcount++;*/
		
	$titre_table = _L("Conformit&eacute; du site");
	$icone = "";
	
	echo "<div style='height: 12px;'></div>";
	echo "<a href='".generer_url_ecrire('w3c_go_home','reset=1')."'>Reset</a><br/>";
	echo "<div class='liste'>";
	bandeau_titre_boite2($titre_table, $icone, $couleur_claire, "black");
	echo "<table width='100%' cellpadding='3' cellspacing='0' border='0'>";
	$table[] = array('url',"<a href='http://validateur-accessibilite.apinc.org/'>apinc</a>","<a href='http://validator.w3.org/'>validator</a>");

	$access_compliance = isset($GLOBALS['meta']['xhtml_access_compliance'])?unserialize($GLOBALS['meta']['xhtml_access_compliance']):false;
	if (!$access_compliance)
		$access_compliance = array();
	$w3c_compliance = isset($GLOBALS['meta']['xhtml_w3c_compliance'])?unserialize($GLOBALS['meta']['xhtml_w3c_compliance']):false;
	if (!$w3c_compliance)
		$w3c_compliance = array();
	
	$cpt_ok_access=0;
	$cpt_ok_xhtml=0;
	$cpt=0;
	foreach($sitemap as $url) {
		$lastmod = strtotime($url['lastmod'][0]);
		$loc = $url['loc'][0];
		$ok_access=false;
		$url_access=generer_url_ecrire('test_access',"url=".urlencode($loc)."&time=".time()); // time pour echapper au cache du navigateur
		$valide_access="<img src='$url_access' />";
		if (isset($access_compliance[$loc])){
			$res = $access_compliance[$loc];
			if (($res[0]==0)&&($lastmod<$res[1])){
				$valide_access=date("Y-m-d H:i",$res[1]);
				$ok_access = true;
				$cpt_ok_access++;
			}
		}
		$ok_xhtml=false;
		$url_xhtml=generer_url_ecrire('test_xhtml',"url=".urlencode($loc)."&time=".time());  // time pour echapper au cache du navigateur
		$valide_xhtml="<img src='$url_xhtml' />";
		if (isset($w3c_compliance[$loc])){
			$res = $w3c_compliance[$loc];
			if (($res[0]==0)&&($lastmod<$res[1])){
				$valide_xhtml=date("Y-m-d H:i",$res[1]);
				$ok_xhtml = true;
				$cpt_ok_xhtml++;
			}
		}

		$vals = '';
		$vals[] = ++$cpt;
		

		if ($ok_access&&$ok_xhtml){
			$cpt_ok++;
			$puce = 'puce-verte-breve.gif';
		}
		else
			$puce = 'puce-orange-breve.gif';

		$s = "<img src='"._DIR_IMG_PACK."$puce' width='7' height='7' border='0'>&nbsp;&nbsp;";
		$s .= "<a href='$loc'>".lignes_longues($loc,50)."</a>";
		$vals[] = $s;
		
		$s = "";
		$url_apinc=generer_url_ecrire('test_apinc',"urlAVerif=".urlencode($loc));
		$s .= "<a href='$url_apinc'>$valide_access</a>";
		$vals[] = $s;


		$s = "";
		$url_w3c="http://validator.w3.org/check?uri=".urlencode($loc);
		$s .= "<a href='$url_w3c'>$valide_xhtml</a>";
		$vals[] = $s;

		$table[] = $vals;
	}
	$largeurs = array('','','','','');
	$styles = array('arial11', 'arial11', 'arial1', 'arial1','arial1');
	echo afficher_liste($largeurs, $table, $styles);
	echo "</table>";
	echo "</div>\n";
	
	echo "$cpt_ok_access/".count($sitemap)." pages conforme selon la verification accessibilit&eacute; automatis&eacute;e<br/>";
	echo "$cpt_ok_xhtml/".count($sitemap)." pages conforme XHTML selon la validator.w3.org";

	fin_page();

}