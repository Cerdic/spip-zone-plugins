<?php
// Fichier inséré uniquement en cas de nécessité, lorsqu'une fonction non définie en version 1.9.2 est utilisée
// SPIP 1.9.2 ne contenait pas encore la fonction spip_xml_match_nodes() ni la fonction spip_xml_decompose_tag(), ni spip_xml_tagname(),
// ni touch_meta()

// https://code.spip.net/@spip_xml_tagname
function spip_xml_tagname($tag){
	if (preg_match(',^([a-z][\w:]*),i',$tag,$reg))
		return $reg[1];
	return "";
}

// https://code.spip.net/@spip_xml_decompose_tag
function spip_xml_decompose_tag($tag){
	$tagname = spip_xml_tagname($tag);
	$liste = array();
	$p=strpos($tag,' ');
	$tag = substr($tag,$p);
	$p=strpos($tag,'=');
	while($p!==false){
		$attr = trim(substr($tag,0,$p));
		$tag = ltrim(substr($tag,$p+1));
		$quote = $tag{0};
		$p=strpos($tag,$quote,1);
		$cont = substr($tag,1,$p-1);
		$liste[$attr] = $cont;
		$tag = substr($tag,$p+1);
		$p=strpos($tag,'=');
	}
	return array($tagname,$liste);
}

// https://code.spip.net/@spip_xml_match_nodes
function spip_xml_match_nodes($regexp,&$arbre,&$matches){
	if(is_array($arbre) && count($arbre))
		foreach(array_keys($arbre) as $tag){
			if (preg_match($regexp,$tag))
				$matches[$tag] = &$arbre[$tag];
			if (is_array($arbre[$tag]))
				foreach(array_keys($arbre[$tag]) as $occurences)
					spip_xml_match_nodes($regexp,$arbre[$tag][$occurences],$matches);
		}
	return (count($matches));
}

// touch_meta() n'existe que depuis rev. 11125
if (!is_callable("touch_meta")) {
  function touch_meta($antidate= false){  
  	if (!$antidate OR !@touch(_FILE_META, $antidate)) {
  		$r = $GLOBALS['meta'];
  		unset($r['alea_ephemere']);
  		unset($r['alea_ephemere_ancien']);
  		unset($r['secret_du_site']);
  		if ($antidate) $r['touch']= $antidate;
  		ecrire_fichier(_FILE_META, serialize($r));
  	}
  }
}

?>