<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Creation de la liste des fichiers de log de verification
 *
 * @param string $verification
 * @param string $mode
 * @return array
 */
 
// $verification  => type de verification demandee ('definition' / 'utilisation' / 'fonction_l')
// $mode  => mode de calul de la liste:
// 				- 'recent' pour la liste des derniers logs de chaque langue
// 				- 'tous' pour la liste de tous les logs
function langonet_lister_fichiers_log($verification, $mode='recent') {

	// On cherche le dernier fichier de log de chaque fichier de langue
	$liste_tous = array();
	$logs = preg_files(_DIR_TMP . "langonet/verification/$verification", '[^/]*[_%]' . $verification[0] .'_[^/]*.log$');
	foreach ($logs as $_fichier) {
		preg_match(',([^/]*)[_%]' . $verification[0] .'_[^/]*.log$,i', $_fichier, $matches);
		if ($matches[1]) {
			if ($verification != 'fonction_l') {
				$cle = $matches[1];
			}
			else {
				preg_match(",[^%]+$,i", $matches[1], $retour);
				$cle = $retour[0] . "/";
			}
			$liste_tous[$cle][] = $_fichier;
		}
	}

	// On veut la liste complete des fichiers de verification
	if ($mode == 'recent') {
		// On ne veut que le log le plus recent pour chaque fichier de langue
		$liste_recents = array();
		foreach ($liste_tous as $_cle => $_fichiers) {
			$liste_recents[$_cle] = end($_fichiers);
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


/**
 * Bulle d'information des liens de telechargement 
 *
 * @param string $fichier
 * @param string $type
 * @return array
 */
 
function langonet_creer_bulle_fichier($fichier, $type='log', $action='telecharger') {

	// Date du fichier formatee
	$date = affdate_heure(date('Y-m-d H:i:s', filemtime($fichier)), 'Y-m-d H:i:s');
	// Bulle d'information suivant le type de fichier (log ou langue)
	$bulle = _T('langonet:bulle_' . $action . '_fichier_' . $type, array('date' => $date));

	return $bulle;
}

// Conversion d'un texte en utf-8
function entite2utf($sujet) {
	if (!$sujet) return;
	include_spip('inc/charsets');
	return unicode_to_utf_8(html_entity_decode(preg_replace('/&([lg]t;)/S', '&amp;\1', $sujet), ENT_NOQUOTES, 'utf-8'));
}
?>