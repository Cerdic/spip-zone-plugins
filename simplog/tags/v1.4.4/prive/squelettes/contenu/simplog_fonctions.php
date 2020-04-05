<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_SIMPLOG_LIGNE')) {
	define('_SIMPLOG_LIGNE', '#^(.*:\d\d)\s(.*)\s\(pid\s(.*)\)\s(?:(.+):L(\d+):(\w+\(\)):)?:([bipru]*):([^:]*):\s(.*)$#i');
}

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
		if ($l = trim($_ligne) and $l != '[-- rotate --]') {
			preg_match(_SIMPLOG_LIGNE, $_ligne, $matches);
			if (!isset($matches[1])	or !$matches[1]) {
				// Ce n'est pas une nouvelle ligne mais la suite du texte de la ligne en cours
				$tableau[count($tableau) - 1]['texte'] .= "\n" . trim($_ligne);
			} else {
				$ligne['date'] = date('Y-m-d H:i:s', strtotime($matches[1]));
				$ligne['ip'] = trim($matches[2]);
				$ligne['pid'] = trim($matches[3]);
				$ligne['hit'] = _T('simplog:info_hit_' . strtolower(trim($matches[7])));
				$ligne['gravite'] = strtolower(trim($matches[8]));
				$ligne['texte'] = contruire_texte(trim($matches[9]), trim($matches[4]), trim($matches[5]), trim($matches[6]));
				$ligne['index'] = count($tableau);
				$tableau[] = $ligne;
			}
		}
	}

	return $tableau;
}

function contruire_texte($message, $fichier, $ligne, $fonction) {
	$label_fichier = _T('simplog:label_fichier');
	$label_ligne = _T('simplog:label_ligne');
	$label_fonction = _T('simplog:label_fonction');
	$texte = '<ul>'
			 . ($fichier ? "<li><em>$label_fichier</em> : $fichier</li>" : '')
			 . ($ligne ? "<li><em>$label_ligne</em> : $ligne</li>" : '')
			 . ($fonction ? "<li><em>$label_fonction</em> : $fonction</li>" : '')
			 . ($message ? "<li>$message</li>" : '')
			 . '</ul>';

	return $texte;
}
