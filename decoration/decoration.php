<?php

/*
  * Le filtre decoration permet aux redacteurs d'un site spip de d'appliquer les styles souligné, barré, au dessus à  une phrase , un  mot, parapraphe. 
 */

class decoration{

	function pre_typo($texte) {
		$texte = preg_replace("/(\[souligne\])(.*?)(\[\/souligne\])/", "<span style=\"text-decoration: underline;\">\\2</span>", $texte);
		$texte = preg_replace("/(\[barre\])(.*?)(\[\/barre\])/", "<span style=\"text-decoration: line-through;\">\\2</span>", $texte);
		$texte = preg_replace("/(\[over\])(.*?)(\[\/over\])/", "<span style=\"text-decoration: overline;\">\\2</span>", $texte);
		$texte = preg_replace("/(\[blink\])(.*?)(\[\/blink\])/", "<span style=\"text-decoration: blink;\">\\2</span>", $texte);
		$texte = preg_replace("/(\[fluo\])(.*?)(\[\/fluo\])/", "<span style=\"background-color: #ffff00;\">\\2</span>", $texte);

	return $texte;  
}
}

?>

