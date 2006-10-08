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

function write_state($filestatename,$access_valid,$access_valid_date,$xhtml_valid,$xhtml_valid_date){

	// reecriture du fichier d'etat de la validation
	$filetexte="<"."?php\n";
	if (count($access_valid)>0)
		foreach ($access_valid as $key=>$value)
		  $filetexte.='$access_valid["'.$key.'"]="'.$value.'";'."\n";
	if (count($access_valid_date)>0)
		foreach ($access_valid_date as $key=>$value)
		  $filetexte.='$access_valid_date["'.$key.'"]="'.$value.'";'."\n";
	if (count($xhtml_valid)>0)
		foreach ($xhtml_valid as $key=>$value)
		  $filetexte.='$xhtml_valid["'.$key.'"]="'.$value.'";'."\n";
	if (count($xhtml_valid_date)>0)
		foreach ($xhtml_valid_date as $key=>$value)
		  $filetexte.='$xhtml_valid_date["'.$key.'"]="'.$value.'";'."\n";
	$filetexte.="?".">\n";
	file_put_contents($filestatename,$filetexte);
}

function exec_valide_site(){
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

	ini_set("zlib.output_compression","0"); // pour permettre l'affichage au fur et a mesure
	$filestatename=_DIR_SESSIONS.'/valide_state.php';

	$w3cvalidator='http://validator.w3.org/check?uri=%s';
	$maxerrors=10;
	$maxtimeout=5;
	$counttimeout=0;

	$access_valid=array();
	$access_valid_date=array();
	$xhtml_valid=array();
	$xhtml_valid_date=array();

	if (isset($_GET['reset']))
	{
		write_state($filestatename,$access_valid,$xhtml_valid_date,$xhtml_valid,$xhtml_valid_date);
		$url=generer_url_ecrire("valide_site");
		redirige_par_entete($url);
	}
	
	debut_page(_L("Validation Site W3C"), "w3c", "w3c");
	//debut_gauche();
	//debut_droite();

	if (file_exists($filestatename)) include($filestatename);
	
	$sitemappath=generer_url_public("sitemap");
	$sitemap=simplexml_load_file($sitemappath);

	
	$tot_erreur1=0;
	$tot_erreur2=0;
	$tot_erreur3=0;
	$tot_xhtml_erreur=0;

	$urlcount=0;
	$table_url=array();
	$baseurl="";
	foreach($sitemap->url as $url){
		$loc=$url->loc;
		$table_url[]=$loc;
		if (preg_match('{index\.php}',$loc))
		  $baseurl=str_replace('index.php',"",$loc);
		$urlcount++;
	}
	$table_url[]=generer_url_public("recherche","recherche=conseil");	$urlcount++;
	$table_url[]=generer_url_public("recherche","recherche=municipal"); $urlcount++;
	$table_url[]=generer_url_public("recherche","recherche=ecole");	$urlcount++;
	$table_url[]=generer_url_public("recherche","recherche=mairie");	$urlcount++;
	$table_url[]=generer_url_public("recherche","recherche=permis");	$urlcount++;
	$table_url[]=generer_url_public("article","id_article=6");	$urlcount++;

	echo "Fichier $sitemappath charg&eacute; :$urlcount pages<br/>";
	echo "<a href='".generer_url_ecrire("valide_site",'reset=1')."'>forcer une revalidation</a>";
	echo "<br/>";

	echo "<table>\n";
	echo "<tr><th>No</th><th>Page</th><th>Accesibilit&eacute;<br/><a href='http://validateur-accessibilite.apinc.org/'>validateur-accessibilite.apinc.org</a></th><th>XHTML 1.0<br/><a href='http://validator.w3.org/'>validator.w3.org</a></th></tr>\n";
	$maxiter=$urlcount+1;
	$urlcount=0;
	include "valide_access.php";
	global $erreur1;
	global $erreur2;
	global $erreur3;

	foreach($table_url as $loc){
		echo "<tr>";
		$urlcount++;
		echo "<td width='5%'>$urlcount</td>";
	
		echo "<td width='50%'>";
		echo "<a href='$loc'>$loc</a>";
	
		echo "</td>";
		// accessibilité
		echo "<td width='15%' style='text-align:left'>";
		unset($xerreur);
		unset($urlValide);

		// validation accessibilité
		if ((isset($access_valid["$loc"]))&&($access_valid["$loc"]=='1'))
		{
	 	}
	 	else
	 	{
			$_GET['urlAVerif']=$loc;
			if (!isset($urlValide)) $urlValide = @file ($_GET['urlAVerif']);
			if ($urlValide){
				ob_start();
				exec_valide_access();
				ob_end_clean();
	
				if($erreur1=='0' && $erreur2=='0' && $erreur3=='0')
					$access_valid["$loc"]='1';
				else
					$access_valid["$loc"]='0';
	
				$tot_erreur1+=$erreur1;
				$tot_erreur2+=$erreur2;
				$tot_erreur3+=$erreur3;
			}
			else
				$access_valid["$loc"]='-1';
		}

		if ($access_valid["$loc"]=='1')
		{
			if (isset($access_valid_date["$loc"]))
			  echo "OK (".$access_valid_date["$loc"].")";
			else{
				$access_valid_date["$loc"]=date('Y-m-d H:i:s');
			  echo "<span style='background:#00FF00'>OK</span>";
	  		write_state($filestatename,$access_valid,$xhtml_valid_date,$xhtml_valid,$xhtml_valid_date);
			}
	 	}
		else if ($access_valid["$loc"]=='-1')
		  echo "<span style='background:#FF9999'>Url invalide</span>";
		else
		{
		  if ($erreur1>0) echo "<span style='background:#FF0000'>Niveau 1:$erreur1 erreurs</span> ";
		  if ($erreur2>0) echo "<span style='background:#FF0000'>Niveau 2:$erreur2 erreurs</span> ";
		  if ($erreur3>0) echo "<span style='background:#FF0000'>Niveau 3:$erreur3 erreurs</span> ";
		  echo "<a href='".generer_url_ecrire('valide_access',"urlAVerif=".urlencode($loc))."'>voir...</a>";
	 	}
		echo "</td>";
		ob_flush();flush();
		echo "<td width='15%' style='text-align:left'>";

		// validation W3C
		if ((isset($xhtml_valid["$loc"]))&&($xhtml_valid["$loc"]=='1'))
		{
	 	}
	 	else
	 	{
	    if($counttimeout<$maxtimeout)
	    {
				$_GET['urlAVerif']=$loc;
				if (!isset($urlValide)) $urlValide = @file ($_GET['urlAVerif']);
				if ($urlValide){
					$url=str_replace('%s',urlencode($loc),$w3cvalidator);
					$test=@file_get_contents($url);
					if ($test!==FALSE){
						if (preg_match('/passed validation/is',$test))
							$xhtml_valid["$loc"]='1';
						else{
							$xhtml_valid["$loc"]='0';
							$xerreur=preg_replace('/.*?([0-9]*)\s+error[s]?.*/is','\\1',$test);
							$tot_xhtml_erreur++;
						}
					}
					else{
						$xhtml_valid["$loc"]='-2';
						$counttimeout++;
					}
				}
				else
					$xhtml_valid["$loc"]='-1';
			}
			else
				$xhtml_valid["$loc"]='-2';
		}
		if ($xhtml_valid["$loc"]=='1')
		{
			if (isset($xhtml_valid_date["$loc"]))
			  echo "OK (".$xhtml_valid_date["$loc"].")";
			else{
				$xhtml_valid_date["$loc"]=date('Y-m-d H:i:s');
			  echo "<span style='background:#00FF00'>OK</span>";
				write_state($filestatename,$access_valid,$xhtml_valid_date,$xhtml_valid,$xhtml_valid_date);
			}
	 	}
		else if ($xhtml_valid["$loc"]=='-1')
		  echo "<span style='background:#FF9999'>Url invalide</span>";
		else if ($xhtml_valid["$loc"]=='-2')
		  echo "<span style='background:#FF9999'>Echec du test</span>";
		else
		{
		  if (isset($xerreur)) echo "<span style='background:#FF0000'>$xerreur erreurs</span> ";
		  echo "<a href='".str_replace('%s',urlencode($loc),$w3cvalidator)."'>voir...</a>";
	 	}
		//echo "\t";
		echo "</td>";

		echo "</tr>";
	
		ob_flush();flush();
	
		if ($maxiter--==0){
			echo "</table>";
			exit;
		}
		if (($tot_xhtml_erreur>$maxerrors)||($tot_erreur1+$tot_erreur2+$tot_erreur3>$maxerrors)){
			echo "</table>";
			exit;
	 	}
	}
	echo "</table>";

	echo "<h2>Site http://$host$path</h2>";
	echo "$urlcount pages v&eacute;rifi&eacute;es<br/>";
	echo "<h3>Conformit&eacute; W3C</h3>";
	echo "avec http://validator.w3.org/<br/>";
	if ($tot_xhtml_erreur==0)
		echo "<span style='background:#00FF00'>Toutes les pages du site sont conformes W3C XHTML 1.0</span><br/>";
	else
		echo "<span style='background:#FF0000'>$tot_xhtml_erreur pages non conformes W3C XHTML 1.0</span><br/>";
	echo "<h3>Accessibilit&eacute; :</h3>";
	echo "avec http://validateur-accessibilite.apinc.org/<br/>";
	if ($tot_erreur1>0) echo "<span style='background:#FF0000'>Niveau 1:$tot_erreur1 erreurs</span><br/>";
	if ($tot_erreur2>0) echo "<span style='background:#FF0000'>Niveau 2:$tot_erreur2 erreurs</span><br/>";
	if ($tot_erreur3>0) echo "<span style='background:#FF0000'>Niveau 3:$tot_erreur3 erreurs</span><br/>";
	if ($tot_erreur1+$tot_erreur2+$tot_erreur3==0) echo "<span style='background:#00FF00'>Toutes les pages du site respectent les pr&eacute;conisations W3C v&eacute;rifiables de mani&egrave;re automatis&eacute;es concernant l'accessibilit&eacute;</span><br/>";
	fin_page();

}