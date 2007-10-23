<?php
// Fonctions de «Bonbon !» le cahier de texte pour Spip.
// Réalisé par Bertrand MARNE (bmarne à ac-creteil.fr)
// Sous licence GPL (enfin bon, c'est bonbonware...)
// CopyLeft en octobre 2007

//Cette fonction matches les id_articles qui sont dans le PS des séances
//Puis les mets dans une châine (séparés par des virgules, en vue d'un
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
