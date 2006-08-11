<?php

function tester_repertoire($base, $subdir) {
	$baser=$base;
	if (substr($baser,-1,1)!="/")
		$baser.="/";
	if (@file_exists("$baser.plat")) return '';
	$path = $base.$subdir;
	if (@file_exists($path)) return "$subdir/";

	@mkdir($path, 0777);
	@chmod($path, 0777);
	$ok = false;
	if ($f = @fopen("$path/.test", "w")) {
		@fputs($f, '<'.'?php $ok = true; ?'.'>');
		@fclose($f);
		include("$path/.test");
	}
	if (!$ok) {
		$f = @fopen("$base/.plat", "w");
		if ($f)
			fclose($f);
		//else {
			//redirige_par_entete("spip_test_dirs.php");
		//}
	}
	return ($ok? "$subdir/" : '');
}

function chart_avant_propre ($letexte)
{
	global $id_article;
	// Directives XML <chart>
	// C.MORIN le 21/07/2005
	$regex="<[\s]*chart([\s]*[^>]*[^/>])?>(.*?)</chart(?:[\s][^>]*)?>";
	$match=preg_match("{".$regex."}is",$letexte);
	$nbchart=0;
	while (($match=preg_match("{".$regex."}is",$letexte))&&($nbchart<100))
	{
		$id="id$id_article-$nbchart";
		$nbchart++;
		// parametres de la balise <chart> pouvant être redefinis dans l'entete
		$width=400;
		$height=250;
		$align="";
		$bgcolor="666666";

		// recuperation
		$entete=preg_replace("{(?:.*?)".$regex."(?:.*)}is","\\1",$letexte,1);
		//echo "entete::$entete::";
		//$letexte.="En tete:[$entete]<br/>";
		$test="/.*width=[\s]*[\"']([0-9]+)[\"'].*/i";
		if (preg_match($test,$entete))
			$width=preg_replace($test,"\\1",$entete);
		$test="/.*height=[\s]*[\"']([0-9]+)[\"'].*/i";
		if (preg_match($test,$entete))
			$height=preg_replace($test,"\\1",$entete);
		$test="/.*align=[\s]*[\"'](left|center|right)[\"'].*/i";
		if (preg_match($test,$entete))
			$align=preg_replace($test,"\\1",$entete);
		$test="/.*bgcolor=[\s]*[\"']#?([[:xdigit:]]{6})['\"].*/i";
		if (preg_match($test,$entete))
			$bgcolor=preg_replace($test,"\\1",$entete);

		// d'un interet tres relatif ...
		// $test="/.*id=[\s]*[\"']([^\s']+)[\"'].*/i";
		// if (preg_match($test,$entete))
		//	$id=preg_replace($test,"\\1",$entete);


		// export du descriptif xml <chart> ... </chart> dans un fichier
		$localpath=$_SERVER["SCRIPT_FILENAME"];
		$localpath=preg_replace("{(.*)/[^/]*php}i","\\1",$localpath)."/";
		$pathtoimg=$localpath._DIR_IMG;

		// cree le repertoire xml si n'existe pas
		$insert="";
		if (strcmp(tester_repertoire($pathtoimg,"xml"),"xml/")==0)
		{
			$filename="xml/chart".$id.".xml";
			$filecontent=preg_replace("{(?:.*?)".$regex."(?:.*)}is","\\2",$letexte,1);
			$filecontent="<chart>".$filecontent."</chart>";

			//$fp=@fopen(_DIR_IMG.$filename,'wt');
			$fp=@fopen(_DIR_IMG."xml/charttemp.xml",'wt');
			@fwrite($fp,$filecontent);
			@fclose($fp);
			@rename(_DIR_IMG."xml/charttemp.xml",_DIR_IMG.$filename);
			$insert="<chart width='$width' height='$height' align='$align' id='$id' bgcolor='$bgcolor' data='$filename' />";

		}

		$letexte=preg_replace("{".$regex."}is",$insert,$letexte,1);
	}

	return $letexte;

}

function chart_apres_propre($letexte)
{
	global $chart_license;
	// Directives XML <chart>
	// C.MORIN le 21/07/2005
 	$regex="<[\s]*chart([\s][^>]*)?/>";
	$match=preg_match("{".$regex."}is",$letexte);
	$nbchart=0;
	while (($match=preg_match("{".$regex."}is",$letexte))&&($nbchart<100))
	{
		$id="id".$nbchart;$nbchart++;
		// parametres de la balise <chart> pouvant être redefinis dans l'entete
		$width=400;
		$height=250;
		$align="";
		$bgcolor="666666";
		$data="";

		// recuperation
		$entete=preg_replace("{(?:.)*?".$regex."(?:.)*}is","\\1",$letexte);

		$test="/.*width=[\s]*[\"']([0-9]+)[\"'].*/i";
		if (preg_match($test,$entete))
			$width=preg_replace($test,"\\1",$entete);
		$test="/.*height=[\s]*[\"']([0-9]+)[\"'].*/i";
		if (preg_match($test,$entete))
			$height=preg_replace($test,"\\1",$entete);
		$test="/.*align=[\s]*[\"'](left|center|right)[\"'].*/i";
		if (preg_match($test,$entete))
			$align=preg_replace($test,"\\1",$entete);
		if ($align!='')
		  $align = "style='float:$align;'";
		$test="/.*bgcolor=[\s]*[\"']#?([[:xdigit:]]{6})['\"].*/i";
		if (preg_match($test,$entete))
			$bgcolor=preg_replace($test,"\\1",$entete);
		$test="/.*id=[\s]*[\"']([^\s']+)[\"'].*/i";
		if (preg_match($test,$entete))
			$id=preg_replace($test,"\\1",$entete);
		$test="/.*data=[\s]*[\"']([^\s']+)[\"'].*/i";
		if (preg_match($test,$entete))
			$data=preg_replace($test,"\\1",$entete);

		$localpath=$_SERVER["SCRIPT_NAME"];
		$localpath=preg_replace("{(.*)/[^/]*php}i","\\1",$localpath)."/";
		$pathtoimg=$localpath._DIR_IMG;

		$swf=$localpath._DIR_PREFIX1."charts.swf";
    $xml="library_path=".$localpath._DIR_PREFIX1."charts_library&amp;xml_source=".$pathtoimg.$data;
		if (isset($$chart_license)){
			$xml = "license=$$chart_license&amp;" . $xml;
		}
		$insert=<<<chartcode
<object type="application/x-shockwave-flash"
data="$swf" width="$width" height="$height" id="$id" $align>
<param name="movie" value="$swf" />
<param name="FlashVars" value="$xml" />
<param name="quality" value="high" />
<param name="bgcolor" value="#$bgcolor" />
</object>
chartcode;
		$letexte=preg_replace("{".$regex."}is",$insert,$letexte,1);
	}

  return $letexte;
}
?>
