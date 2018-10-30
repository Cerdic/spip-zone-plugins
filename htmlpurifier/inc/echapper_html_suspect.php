<?php

function inc_echapper_html_suspect_dist($texte, $strict=true) {
	if (!$texte
		or strpos($texte, '<') === false or strpos($texte, '=') === false) {
		return $texte;
	}
	// si le texte contient un modele ou une ressource : on protège avant filtrage
	$preg_modeles="@"._PREG_MODELE."@imsS";
	if (!$strict and preg_match($preg_modeles, $texte) ){
		$texte = echappe_html($texte, '', true, $preg_modeles);
		$texte = safehtml($texte);
		$texte = echappe_retour_modeles($texte, true);
	} else {
		$texte = safehtml($texte); 
	}
	return $texte;
}
