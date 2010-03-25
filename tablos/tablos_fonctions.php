<?php
/* Tablos est une surcharge de filtre_text_csv_dist */
define('_RACCOURCI_TH_SPAN', '\s*(:?{{[^{}]+}}\s*)?|<');
function filtre_text_csv($t){
	$virg = substr_count($t, ',');
	$pvirg = substr_count($t, ';');
	$tab = substr_count($t, "\t");
	if ($virg > $pvirg)
		{ $sep = ','; $hs = '&#44;';}
	else	{ $sep = ';'; $hs = '&#59;'; $virg = $pvirg;}
	if ($tab > $virg) {$sep = "\t"; $hs = "\t";}
	$t = preg_replace('/\r?\n/', "\n",
		preg_replace('/[\r\n]+/', "\n", $t));
	$t = preg_replace("/([\n$sep])\"\"\"/",'\\1"&#34#',$t);
	$t = str_replace('""','&#34#',$t);
	preg_match_all('/"[^"]*"/', $t, $r);
	foreach($r[0] as $cell)
		$t = str_replace($cell,
			str_replace($sep, $hs,
				str_replace("\n", "<br />",
					substr($cell,1,-1))),
			$t);
	list($entete, $corps) = explode("\n",$t,2);
	$caption = '';
	if (substr_count($entete, $sep) == strlen($entete)) {
		list($entete, $corps) = explode("\n",$corps,2);
	}
	if (preg_match("/^([^$sep]+)$sep+\$/", $entete, $l)) {
		$caption = "\n||" .  $l[1] . "|";
		list($entete, $corps) = explode("\n",$corps,2);
	}
	if (preg_match("/$sep$sep+/", $corps, $l)) {
		$tabcorps = explode ($sep,$entete);
		$rowspans = count ($tabcorps);
		$rowstotal = $rowspans-2;
		$rowfussion= "<|";
		$endoftherow= "<";
		$rowsinclus = str_repeat($rowfussion , $rowstotal);
		$corps = eregi_replace("$sep$sep+","$sep$rowsinclus$endoftherow",$corps);		
	}
	if ($entete[0] == $sep) $entete = ' ' . $entete;
	$lignes = explode("\n", $corps);
	while(preg_match("/^$sep*$/", $lignes[count($lignes)-1]))
		unset($lignes[count($lignes)-1]);
	$nbcols = array();
	$max = $mil = substr_count($entete, $sep);
	foreach($lignes as $k=>$v) {
	  if ($max <> ($nbcols[$k]= substr_count($v, $sep))) {
	    if ($max > $nbcols[$k])
	      $mil = $nbcols[$k];
	    else { $mil = $max; $max = $nbcols[$k];}
	  }
	}
	while(true) {
	  $nbcols =  ($entete[strlen($entete)-1]===$sep);
	  foreach($lignes as $v) $nbcols &= ($v[strlen($v)-1]===$sep);
	  if (!$nbcols) break;
	  $entete = substr($entete,0,-1);
	  foreach($lignes as $k=>$v) $lignes[$k] = substr($v,0,-1);
	}
	$corps = join("\n", $lignes) . "\n";
	$corps = $caption .
		"\n|{{" .
		str_replace($sep,'}}|{{',$entete) .
		"}}|" .
		"\n|" .
		str_replace($sep,'|',str_replace("\n", "|\n|",$corps));
	$corps = str_replace('&#34#','&#34;',$corps);
	include_spip('inc/texte');
	return propre($corps);
}
?>