<?php
function parse_plugin_xml($texte){
	$out = array();
  // enlever les commentaires
  $txt = preg_replace(',<!--(.*?)-->,is','',$texte);
//echo htmlentities($txt);

	// tant qu'il y a des tags
	while(preg_match("{<([^>]*?)>}s",$txt)){
		// tag ouvrant
		$chars = preg_split("{<([^>]*?)>}s",$txt,2,PREG_SPLIT_OFFSET_CAPTURE|PREG_SPLIT_DELIM_CAPTURE);
	
		// $before doit etre vide ou des espaces uniquements!
		$before = trim($chars[0][0]);
	#echo "before:$before:<br/>";
		if (strlen($before)>0) exit;
	
		$tag = $chars[1][0];
		$txt = $chars[2][0];
	#echo "tag:$tag:<br/>";
	
		// tag fermant
		$chars = preg_split("{(</$tag>)}s",$txt,2,PREG_SPLIT_OFFSET_CAPTURE|PREG_SPLIT_DELIM_CAPTURE);
		if (!isset($chars[1])) {$out[$tag][]="erreur : tag fermant $tag manquant"; return $out;} // tag fermant manquant
		$content = $chars[0][0];
		$txt = trim($chars[2][0]);
		$out[$tag][]=parse_plugin_xml($content);
	#echo "/tag:$tag:<br/>";

	}
	if (count($out))
		return $out;
	else{
	  #echo "inside:$txt<br/>";
		return $txt;
	}
}

$texte = file_get_contents("plugin.xml");

$arbre = parse_plugin_xml($texte);

var_dump($arbre['plugin']);

var_dump($arbre['plugin']);

?>