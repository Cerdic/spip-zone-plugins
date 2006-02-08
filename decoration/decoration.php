<?php

/*
  * Le plugin decoration permet aux redacteurs d'un site spip de d'appliquer les styles soulign�, barr�, au dessus �  une phrase , un  mot, parapraphe. 
 */

class decoration{

	function pre_typo($texte) {
		$texte = preg_replace("/(\<souligne\>)(.*?)(\<\/souligne\>)/", "<span style=\"text-decoration: underline;\">\\2</span>", $texte);
		$texte = preg_replace("/(\<barre\>)(.*?)(\<\/barre\>)/", "<span style=\"text-decoration: line-through;\">\\2</span>", $texte);
		$texte = preg_replace("/(\<over\>)(.*?)(\<\/over\>)/", "<span style=\"text-decoration: overline;\">\\2</span>", $texte);
		$texte = preg_replace("/(\<clignote\>)(.*?)(\<\/clignote\>)/", "<span style=\"text-decoration: blink;\">\\2</span>", $texte);
		$texte = preg_replace("/(\<fluo\>)(.*?)(\<\/fluo\>)/", "<span style=\"background-color: #ffff00;\">\\2</span>", $texte);


// Raccourci typographique <sc></sc>

$texte = str_replace("<sc>",
			"<span style=\"font-variant: small-caps\">", $texte);
		$texte = str_replace("</sc>", "</span>", $texte);

	return $texte;  
}




}

?>

