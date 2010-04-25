<?php

/**
 * Creation du select des fichiers de langue
 *
 * @param string $sel_l
 * @return string
 */
function langonet_lister_fichiers_log($verification, $mode='recent') {

	// On cherche le dernier fichier de log de chaque fichier de langue
	$liste_tous = array();
	$logs = preg_files(_DIR_TMP . "langonet/$verification", '[^/]*_' . $verification[0] .'_[^/]*.log$');
	foreach ($logs as $_fichier) {
		preg_match(',([^/]*)_' . $verification[0] .'_[^/]*.log$,i', $_fichier, $matches);
		if ($matches[1]) {
			$liste_tous[$matches[1]][] = $_fichier;
		}
	}

	// On veut la liste complete des fichiers de verification
	if ($mode == 'recent') {
		// On ne veut que le log le plus recent pour chaque fichier de langue
		$liste_recents = array();
		foreach ($liste_tous as $_langue => $_fichiers) {
			$liste_recents[$_langue] = end($_fichiers);
		}
		return $liste_recents;
	}
	else
		return $liste_tous;
	
}

?>