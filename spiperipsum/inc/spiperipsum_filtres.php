<?php
function spiperipsum_afficher($langue, $jour, $lecture, $mode){
	include_spip('inc/spiperipsum_utils');

	if (!$jour) $jour = _SPIPERIPSUM_JOUR_DEFAUT;
	if (!$lecture) $lecture = _SPIPERIPSUM_LECTURE_DEFAUT;
	if (!$mode) $mode = _SPIPERIPSUM_MODE_DEFAUT;
	
	$nom_fichier = charger_lectures($langue, $jour);
	lire_fichier($nom_fichier,$tableau);
	$tableau = unserialize($tableau);
	
	$contexte = array();
	if ($tableau[$lecture])
		$contexte = $tableau[$lecture];
	$contexte = array_merge($contexte, array('mode' => $mode));
	
	if ($lecture == _SPIPERIPSUM_LECTURE_SAINT)
		$texte = recuperer_fond("modeles/saint", $contexte);
	else if ($lecture == _SPIPERIPSUM_LECTURE_COMMENTAIRE)
		$texte = recuperer_fond("modeles/commentaire", $contexte);
	else
		$texte = recuperer_fond("modeles/lecture", $contexte);
	
	return $texte;
}

function spiperipsum_lire($langue, $jour, $lecture, $info){
	include_spip('inc/spiperipsum_utils');

	if (!$jour) $jour = _SPIPERIPSUM_JOUR_DEFAUT;
	if (!$lecture) $lecture = _SPIPERIPSUM_LECTURE_DEFAUT;
	if (!$info) $info = _SPIPERIPSUM_INFO_DEFAUT;
	
	$nom_fichier = charger_lectures($langue, $jour);
	lire_fichier($nom_fichier, $tableau);
	$tableau = unserialize($tableau);
	
	return $tableau[$lecture][$info];
}
?>
