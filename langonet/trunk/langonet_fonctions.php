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

/**
 * Creation du select des fichiers de langue
 *
 * @param string $sel_l
 * @return array
 */
function langonet_creer_select_langues($sel_l='0', $exclure_paquet=true) {
	include_spip('inc/langonet_utils');
	$retour = creer_selects($sel_l, array(), $exclure_paquet);
	return $retour['fichiers'];
}

/**
 * Creation du select des arborescences a scanner
 *
 * @param string $sel_d
 * @return array
 */
function langonet_creer_select_dossiers($sel_d=array(), $multiple=true) {
	include_spip('inc/langonet_utils');
	if (is_string($sel_d)) $sel_d = array($sel_d);
	$retour = creer_selects('0', $sel_d, true, $multiple);
	return $retour['dossiers'];
}


function langonet_cadrer_expression($expression, $colonne, $ligne, $cadre=4) {
	$affiche = '';

	if ($ligne) {
		$debut = max($colonne-$cadre, 0);
		// il faut calculer la taille exacte du préfixe : $colonne-$debut
		$affiche = substr($ligne, $debut, $colonne - $debut + strlen($expression) + $cadre);

		// Si la taille précisée excède la distance entre le $debut et la fin de la ligne, substr() coupe
		// avant sans retourner d'erreur. Il faut donc calculer l'index final
		$index_fin = $debut + strlen($affiche);

		// On encadre l'expression par des points avant et après sauf si on a déjà atteint le bout
		$affiche = ($debut > 0 ? '&#8230;' : '') . $affiche . ($index_fin < strlen($ligne)-1 ? '&#8230;' : '');
	}

	return $affiche;
}

?>