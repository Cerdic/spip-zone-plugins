<?php
function spiperipsum_lire($langue, $jour, $lecture){
	include_spip('inc/spiperipsum_utils');

	if (!$jour) $jour = _SPIPERIPSUM_JOUR_DEFAUT;
	if (!$lecture) $lecture = _SPIPERIPSUM_LECTURE_DEFAUT;
	
	$nom_fichier = charger_lectures($langue, $jour);
	lire_fichier($nom_fichier,$tableau);
	$tableau = unserialize($tableau);
	
	$contexte = $tableau[$lecture];
	$texte = recuperer_fond("modeles/lecture", $contexte);

	return $texte;
}
?>
