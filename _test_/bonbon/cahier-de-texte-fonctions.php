<?php
// Fonctions de �Bonbon !� le cahier de texte pour Spip.
// R�alis� par Bertrand MARNE (bmarne � ac-creteil.fr)
// Sous licence GPL (enfin bon, c'est bonbonware...)
// CopyLeft en octobre 2007

//Cette fonction matches les id_articles qui sont dans le PS des s�ances
//Puis les mets dans une ch�ine (s�par�s par des virgules, en vue d'un
//explode)
function bonbon_matches_id_article ($a_matcher) {
	preg_match_all ("/->(\d+?)\]/",$a_matcher,$matches);
	$key = key($matches[1]);
	$val = current($matches[1]);
	while(list ($key, $val) = each ($matches[1])) {
	$sortie .= $val.",";
 	};
	return $sortie;
}
?>
