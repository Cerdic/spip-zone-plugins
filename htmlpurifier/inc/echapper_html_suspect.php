<?php



function inc_echapper_html_suspect_dist($texte, $strict=true) {
	if (!$texte
		or strpos($texte, '<') === false or strpos($texte, '=') === false) {
		return $texte;
	}
  if (preg_match("@^</?[a-z]{1,5}(\s+class\s*=\s*['\"][a-z _\s-]+['\"])?\s?/?>$@iS", $texte)) return $texte;
  $texte = safehtml($texte); 
	return $texte;
}
