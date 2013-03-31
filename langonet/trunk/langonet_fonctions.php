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
function langonet_creer_select_langues($sel_l='0') {

	$retour = creer_selects($sel_l, array());
	return $retour['fichiers'];
}

/**
 * Creation du select des arborescences a scanner
 *
 * @param string $sel_d
 * @return array
 */
function langonet_creer_select_dossiers($sel_d=array()) {
	if (is_string($sel_d)) $sel_d = array($sel_d);
	$retour = creer_selects('0', $sel_d);
	return $retour['dossiers'];
}

/**
 * Creation d'un tableau des selects:
 * - des fichiers de langue
 * - des arborescences a scanner
 *
 * @param string $sel_l option du select des langues 
 * @param array $sel_d option(s) du select des repertoire
 * @return array
 */
function creer_selects($sel_l='0',$sel_d=array()) {
	// Recuperation des repertoires des plugins
	$rep_plugins = lister_dossiers_plugins();
	// Recuperation des repertoires des extensions
	$rep_extensions = lister_dossiers_plugins(defined('_DIR_PLUGINS_DIST')?_DIR_PLUGINS_DIST:_DIR_EXTENSIONS);
	// Recuperation des repertoires SPIP et squelettes
	if (strlen($GLOBALS['dossier_squelettes'])) {
		$rep_perso = explode(':', $GLOBALS['dossier_squelettes']);
	}
	else {
		$rep_perso[] = 'squelettes';
	}
	$rep_spip[] = rtrim(_DIR_RESTREINT_ABS, '/');
	$rep_spip[] = 'prive';
	$rep_spip[] = 'squelettes-dist';
	$rep_scan = array_merge($rep_perso, $rep_extensions, $rep_plugins, $rep_spip);
	
	// construction des <select>
	// -- les fichiers de langue
	$sel_lang = '<select name="fichier_langue" id="fichier_langue" style="margin-bottom:1em;">'."\n";
	$sel_lang .= '<option value="0"';
	$sel_lang .= ($sel_l == '0') ? ' selected="selected">' : '>';
	$sel_lang .= _T('langonet:option_aucun_fichier') . '</option>' . "\n";
	// -- les racines des arborescences a scanner
	$sel_dossier = '<select name="dossier_scan[]" id="dossier_scan" multiple="multiple">' . "\n";
	//$sel_dossier .= '<option value="0"';
	//$sel_dossier .= (count($sel_d) == '0') ? ' selected="selected">' : '>';
	//$sel_dossier .= _T('langonet:option_aucun_dossier') . '</option>' . "\n";

	// la liste des options :
	// value (fichier_langue) =>
	//     $rep (nom du repertoire parent de lang/)
	//     $module (prefixe fichier de langue)
	//     $langue (index nom de langue)
	//     $ou_lang (chemin relatif vers fichier de langue a verifier)
	foreach ($rep_scan as $rep) {
		if (in_array($rep, $rep_plugins)) {
			$reel_dir = _DIR_PLUGINS . $rep;
			$ou_fichier = str_replace('../', '', $reel_dir) . '/';
		}
		else if (in_array($rep, $rep_extensions)) {
			$reel_dir = (defined('_DIR_PLUGINS_DIST')?_DIR_PLUGINS_DIST:_DIR_EXTENSIONS) . $rep;
			$ou_fichier = str_replace('../', '', $reel_dir) . '/';
		}
		else {
			$reel_dir = _DIR_RACINE . $rep;
			$ou_fichier = $rep . '/';
		}
		if (is_dir($reel_dir . '/lang/')) {
			// on recupere tous les fichiers de langue directement places
			// dans lang/ sans parcourir d'eventuels sous-repertoires
			$opt_lang = '';
			foreach ($fic_lang = preg_files($reel_dir . '/lang/', '_[a-z]{2,3}\.php$', 250, false) as $le_module) {
				preg_match_all("%_(\w{2,3})(_\w{2,3})?(_\w{2,4})?$%im", str_replace('.php', '', $le_module), $matches);
				$module = str_replace($matches[0][0].'.php', '', $le_module);
				$module = str_replace($reel_dir . '/lang/', '', $module);
				$langue = ltrim($matches[0][0], '_');
				$ou_langue = str_replace('../', '', $reel_dir) . '/lang/';
				$value = $rep.':'.$module.':'.$langue.':'.$ou_langue;
				$opt_lang .= '<option value="' . $value;
				$opt_lang .= ($value == $sel_l) ? '" selected="selected">' : '">';
				$opt_lang .= str_replace('.php', '', str_replace($reel_dir . '/lang/', '', $le_module)) . '</option>' . "\n";
			}
			if ($opt_lang) {
				$sel_lang .= '<optgroup label="' . str_replace('../', '', $reel_dir) . '/">' . "\n";
				$sel_lang .= $opt_lang;
				$sel_lang .= '</optgroup>' . "\n";
			}
		}
		$sel_dossier .= '<option value="' . $ou_fichier;
		$sel_dossier .= (in_array($ou_fichier,$sel_d)) ? '" selected="selected">' : '">';
		$sel_dossier .= str_replace('../', '', $reel_dir) . '/</option>' . "\n";
	}

	$sel_lang .= '</select>' . "\n";
	$sel_dossier .= '</select>' . "\n";

	return $retour = array('fichiers' => $sel_lang, 'dossiers' => $sel_dossier);
}

/**
 * Lister tous les plugins
 *
 * @param string $rep_base
 * @return array
 */

// $rep_base  => le repertoire de depart de l'arboresence a scanner
function lister_dossiers_plugins($rep_base=null) {
	include_spip('inc/plugin');
	// liste_plugin_files() integre les repertoires supplementaires de plugins
	// dans le cadre de la mutualisation
	$liste_rep = liste_plugin_files($rep_base);
	if (is_null($rep_base))
		$rep_base = _DIR_PLUGINS;
	$dossiers = array();
	foreach ($liste_rep as $rep) {
		$reel_dir = $rep_base . $rep;
		$dossiers[] = $rep;
		if ($sous_rep = glob($reel_dir.'/*/lang', GLOB_ONLYDIR)) {
			for ($i = 0; $i < count($sous_rep); $i++) {
    			$dossiers[] = str_replace($rep_base, '', str_replace('/lang', '', $sous_rep[$i]));
			}
		}
	}
	return $dossiers;
}

?>