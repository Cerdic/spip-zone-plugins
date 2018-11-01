<?php

function inc_echapper_html_suspect_dist($texte, $strict=true) {
	if (!$texte
		or strpos($texte, '<') === false or strpos($texte, '=') === false) {
		return $texte;
	}
	
  if (preg_match("@^<[/]?[a-z]{1,5}(\s+class=['\"][a-z _-]+['\"])?\s?[/]?>$@iS", $texte)) return $texte;
  
  $tid = creer_uniqid();
  $texte = echappe_html($texte, $tid, true);
  $texte = expanser_liens($texte);
  $texte = traiter_raccourcis($texte);
  $texte = safehtml($texte);
  $texte = echappe_retour($texte, $tid);
  
	return $texte;
}
