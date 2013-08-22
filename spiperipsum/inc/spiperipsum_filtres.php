<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function spiperipsum_afficher($langue, $jour, $lecture, $mode){
	include_spip('inc/spiperipsum_utils');

	if (!$jour) $jour = _SPIPERIPSUM_JOUR_DEFAUT;
	if (!$lecture) $lecture = _SPIPERIPSUM_LECTURE_DEFAUT;
	if (!$mode) $mode = _SPIPERIPSUM_MODE_DEFAUT;
	
	$nom_fichier = charger_lectures($langue, $jour);
	lire_fichier($nom_fichier,$tableau);
	$tableau = unserialize($tableau);
	
	$contexte = array();

	if (($lecture == _SPIPERIPSUM_LECTURE_DATE_TITRE)
	OR ($lecture == _SPIPERIPSUM_LECTURE_DATE_ISO)
	OR ($lecture == _SPIPERIPSUM_LECTURE_DATE_LITURGIQUE)) {
		$contexte = $tableau['date'];
		$contexte = array_merge($contexte, array('lecture' => $lecture, 'mode' => $mode));
		$texte = recuperer_fond("modeles/date", $contexte);
	}
	else {
		if ($tableau[$lecture])
			$contexte = $tableau[$lecture];
		$contexte = array_merge($contexte, array('lecture' => $lecture, 'mode' => $mode));

		if ($lecture == _SPIPERIPSUM_LECTURE_SAINT)
			$texte = recuperer_fond("modeles/saint", $contexte);
		else if ($lecture == _SPIPERIPSUM_LECTURE_COMMENTAIRE)
			$texte = recuperer_fond("modeles/commentaire", $contexte);
		else
			$texte = recuperer_fond("modeles/lecture", $contexte);
	}

	return $texte;
}

function spiperipsum_lire($langue, $jour, $lecture, $info){
	include_spip('inc/spiperipsum_utils');

	if (!$jour) $jour = _SPIPERIPSUM_JOUR_DEFAUT;
	if (!$lecture) $lecture = _SPIPERIPSUM_LECTURE_DEFAUT;
	if (!$info) $info = _SPIPERIPSUM_INFO_DEFAUT;

	// Pour la date, on peut utiliser au choix
	// - lecture = date et info = iso, liturgique et titre
	// - lecture = date_titre, date_iso et date_liturgique
	// Dans le deuxième cas il faut un traitement préalable pour rétablir la structure d'index standard
	if (substr($lecture, 0, 5) === 'date_') {
		$parties = explode('_', $lecture);
		$lecture = 'date';
		$info = $parties[1];
	}

	$nom_fichier = charger_lectures($langue, $jour);
	lire_fichier($nom_fichier, $tableau);
	$tableau = unserialize($tableau);
	
	return $tableau[$lecture][$info];
}
?>
