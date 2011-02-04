<?php

function retoursimple_pre_propre($texte){
	$texte = trim($texte); 
	$texte = preg_replace("/\n([\w\d])/", "\n<br />\\1", $texte); 
	$texte = preg_replace("@^ ?<br />@", "", $texte); 
	$texte = str_replace("<br />_ ","<br />",$texte);
	$texte = "\n".$texte;
	return $texte;	
}