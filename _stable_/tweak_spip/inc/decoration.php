<?php

/*
   Ce tweak decoration permet aux redacteurs d'un site spip de d'appliquer les styles souligné, barré, au dessus aux textes SPIP
*/

// cette fonction n'est pas appelee dans les balises html : cadre|code
function decoration_rempl($texte) {
	$texte = preg_replace("/(\<souligne\>)(.*?)(\<\/souligne\>)/", "<span style=\"text-decoration: underline;\">\\2</span>", $texte);
	$texte = preg_replace("/(\<barre\>)(.*?)(\<\/barre\>)/", "<span style=\"text-decoration: line-through;\">\\2</span>", $texte);
	$texte = preg_replace("/(\<dessus\>)(.*?)(\<\/dessus\>)/", "<span style=\"text-decoration: overline;\">\\2</span>", $texte);
	$texte = preg_replace("/(\<clignote\>)(.*?)(\<\/clignote\>)/", "<span style=\"text-decoration: blink;\">\\2</span>", $texte);
	$texte = preg_replace("/(\<fluo\>)(.*?)(\<\/fluo\>)/", "<span style=\"background-color: #ffff00;\">\\2</span>", $texte);
	$texte = preg_replace("/(\<sc\>)(.*?)(\<\/sc\>)/", "<span style=\"font-variant: small-caps\">\\2</span>", $texte);
	return $texte;  
}

function decoration_pre_typo($texte) {
	return tweak_exclure_balises('cadre|code', 'decoration_rempl', $texte);
}

?>