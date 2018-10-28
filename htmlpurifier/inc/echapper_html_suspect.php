<?php

function inc_echapper_html_suspect_dist($texte, $strict=true) {
	if (!$texte
		or strpos($texte, '<') === false or strpos($texte, '=') === false) {
		return $texte;
	}
	// quand c'est du texte qui passe par propre on est plus coulant si c'est un modele ou une ressource
	if (!$strict and preg_match("@^"._PREG_MODELE."$@imsS", $texte) ){    
		return $texte;
	}

  $stexte = safehtml($texte); 
  return $stexte;
}
