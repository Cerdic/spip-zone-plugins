<?php

function inc_echapper_html_suspect_dist($texte, $strict=true) {
	if (!$texte
		or strpos($texte, '<') === false or strpos($texte, '=') === false) {
		return $texte;
	}
	
  if (preg_match("@^<[a-z]{1,5}( class=['\"][a-z _-]+['\"])?>$@iS", $texte)) return $texte;
	// si le texte contient un modele ou une ressource : on protège avant filtrage
	$preg_modeles="@"._PREG_MODELE."@imsS";
	if (!$strict and preg_match($preg_modeles, $texte) ){
		$tid = creer_uniqid();
		$texte = echappe_html($texte, $tid, true, $preg_modeles);
		$texte = expanser_liens($texte);
		$texte = traiter_raccourcis($texte);
		$texte = safehtml($texte);
		$texte = echappe_retour($texte, $tid);
	} else {
		$texte = safehtml($texte); 
	}
	return $texte;
}
