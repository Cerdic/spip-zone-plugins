<?php
function supprimer_verb($code){
	$texte = $code[0];
	$array = array();
	preg_match_all("#<span class=\"base64\" title=\"(.*)\"></span>#",$texte,$array);
	
	foreach ($array[1] as $i){
		$texte = str_replace("<span class=\"base64\" title=\"$i\"></span>",base64_decode($i),$texte);
		
	}
	$array = array();
	
	preg_match_all('#verb\?(.*)\?#',$texte,$array,PREG_SET_ORDER);
	foreach ($array as $i){
		$texte = str_replace("\\".$i[0],$i[1],$texte);	
	}

	return $texte;
}
?>