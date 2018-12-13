<?php

function inc_echapper_html_suspect_dist($texte, $strict=true) {
	if (!$texte
		or strpos($texte, '<') === false or strpos($texte, '=') === false) {
		return $texte;
	}
  if ( preg_match("@^(</?(?!script)[a-z]+(\s+class\s*=\s*['\"][a-z _\s-]+['\"])?\s?/?>[\w\s]*)+$@iS", $texte) ){
    return $texte; // input non filtré, $texte doit être safe !
  }
  $texte = safehtml($texte); 
	return $texte;
}
