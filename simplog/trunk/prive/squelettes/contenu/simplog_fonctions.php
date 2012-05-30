<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function simplog_phraser_log($fichier) {
	$tableau = array();

	$contenu = spip_file_get_contents($fichier);
	if ($contenu) {
		$tableau = simplog_phraser_ligne(preg_split('/\r?\n/', $contenu));
	}

	return $tableau;
}


function simplog_phraser_ligne($contenu) {
	$tableau = array();

	foreach ($contenu as $_ligne) {
		if ($l = trim($_ligne) AND $l != '[-- rotate --]') {
			preg_match('#^(.*:\d\d)\s(.*)\s\(pid\s(.*)\)\s:([bipru]*):([^:]*):\s(.*)$#i', $_ligne, $matches);
			$ligne['date'] = date('Y-m-d H:i:s', strtotime($matches[1]));
			$ligne['ip'] = trim($matches[2]);
			$ligne['pid'] = trim($matches[3]);
			$ligne['hit'] = _T('simplog:info_hit_'. strtolower(trim($matches[4])));
			$ligne['gravite'] = strtolower(trim($matches[5]));
			$ligne['texte'] = trim($matches[6]);
			$tableau[] = $ligne;
		}
	}

	return $tableau;
}

?>
