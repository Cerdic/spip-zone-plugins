<?php

/**
 * Creation de la liste des fichiers de log de verification
 *
 * @param string $verification
 * @param string $mode
 * @return array
 */
 
// $verification  => type de verification demandee ('definition' / 'utilisation')
// $mode  => mode de calul de la liste:
// 				- 'recent' pour la liste des derniers logs de chaque langue
// 				- 'tous' pour la liste de tous les logs
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

/**
 * Creation de la liste des fichiers de langue generes
 *
 * @return array
 */
 
function langonet_lister_fichiers_lang() {

	// On cherche le dernier fichier de log de chaque fichier de langue
	$liste_tous = array();
	$langues = preg_files(_DIR_TMP . "langonet/generation", '[^/]*_[\w{2,3}]*.php$');
	foreach ($langues as $_fichier) {
		$liste_tous[basename($_fichier, '.php')] = $_fichier;
	}

	return $liste_tous;
}

?>