<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

if (!defined('_LANGONET_PATTERN_FICHIERS_LANG'))
	define('_LANGONET_PATTERN_FICHIERS_LANG', '_[a-z]{2,3}\.php$');
if (!defined('_LANGONET_PATTERN_CODE_LANGUE'))
	define('_LANGONET_PATTERN_CODE_LANGUE', '%_(\w{2,3})(_\w{2,3})?(_\w{2,4})?$%im');


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

	$retour = creer_selects($sel_l, array(), $exclure_paquet);
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
	$retour = creer_selects('0', $sel_d, true);
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
function creer_selects($sel_l='0',$sel_d=array(), $exclure_paquet=true) {
	// Recuperation des repertoires des plugins par défaut
	$rep_plugins = lister_dossiers_plugins();
	// Recuperation des repertoires des extensions : _DIR_PLUGINS_DIST à partir de SPIP 3
	$rep_extensions = lister_dossiers_plugins(_DIR_PLUGINS_DIST);
	// Recuperation des repertoires des plugins supplémentaires en mutualisation : _DIR_PLUGINS_SUPPL
	$rep_suppl = defined('_DIR_PLUGINS_SUPPL') ? lister_dossiers_plugins(_DIR_PLUGINS_SUPPL) : array();
	// Recuperation des repertoires squelettes perso
	$rep_perso = array();
	$perso = strlen($GLOBALS['dossier_squelettes']) ? explode(':', $GLOBALS['dossier_squelettes']) : array('squelettes');
	foreach($perso as $_rep) {
		if (is_dir(_DIR_RACINE . $_rep))
			$rep_perso[] = $_rep;
	}
	// Recuperation des repertoires SPIP
	$rep_spip[] = rtrim(_DIR_RESTREINT_ABS, '/');
	$rep_spip[] = 'prive';
	$rep_spip[] = 'squelettes-dist';
	$rep_scan = array_merge($rep_perso, $rep_plugins, $rep_suppl, $rep_extensions, $rep_spip);
	
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
	foreach ($rep_scan as $_rep) {
		if (in_array($_rep, $rep_plugins)) {
			$reel_dir = _DIR_PLUGINS . $_rep;
			$ou_fichier = str_replace('../', '', $reel_dir) . '/';
		}
		else if (in_array($_rep, $rep_extensions)) {
			$reel_dir = _DIR_PLUGINS_DIST . $_rep;
			$ou_fichier = str_replace('../', '', $reel_dir) . '/';
		}
		else if (in_array($_rep, $rep_suppl)) {
			$reel_dir = _DIR_PLUGINS_SUPPL . $_rep;
			$ou_fichier = str_replace('../', '', $reel_dir) . '/';
		}
		else {
			$reel_dir = _DIR_RACINE . $_rep;
			$ou_fichier = $_rep . '/';
		}
		if (is_dir($reel_dir . '/lang/')) {
			// on recupere tous les fichiers de langue directement places
			// dans lang/ sans parcourir d'eventuels sous-repertoires. On exclue si demandé ou par défaut
			// les fichiers de langue du paquet.xml
			$opt_lang = '';
			foreach ($fic_lang = preg_files($reel_dir . '/lang/', _LANGONET_PATTERN_FICHIERS_LANG, 250, false) as $le_module) {
				preg_match_all(_LANGONET_PATTERN_CODE_LANGUE, str_replace('.php', '', $le_module), $matches);
				$module = str_replace($matches[0][0].'.php', '', $le_module);
				$module = str_replace($reel_dir . '/lang/', '', $module);
				if (!$exclure_paquet
				OR ($exclure_paquet	AND (strtolower(substr($module, 0, 7)) != 'paquet-'))) {
					$liste[$module[1]] = dirname($_fichier) . '/';
					$langue = ltrim($matches[0][0], '_');
					$ou_langue = str_replace('../', '', $reel_dir) . '/lang/';
					$value = $_rep.':'.$module.':'.$langue.':'.$ou_langue;
					$opt_lang .= '<option value="' . $value;
					$opt_lang .= ($value == $sel_l) ? '" selected="selected">' : '">';
					$opt_lang .= str_replace('.php', '', str_replace($reel_dir . '/lang/', '', $le_module)) . '</option>' . "\n";
				}
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


function langonet_lister_modules($langue, $exclure_paquet=true) {
	$liste = array();

	foreach (preg_files(_DIR_RACINE, "/lang/[^/]+_${langue}\.php$") as $_fichier) {
		// On extrait le module
		if (preg_match(",/lang/([^/]+)_${langue}\.php$,i", $_fichier, $module)) {
			// On ajoute le module à la liste : l'index correspond au module et la valeur au dossier
			if (!$exclure_paquet OR ($exclure_paquet
			AND (strtolower(substr($module[1], 0, 7)) != 'paquet-'))) {
				$liste[$module[1]] = dirname($_fichier) . '/';
			}
		}
	}

	return $liste;
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

function langonet_identifier_reference($module, $ou_langue, &$tradlang) {
	$langue = 'fr';
	$tradlang=false;

	$rapport_xml = _DIR_RACINE . $ou_langue . $module . '.xml';
	if (file_exists($rapport_xml)) {
		$tradlang = true;
		if ($contenu = spip_file_get_contents($rapport_xml))
			if (preg_match(_LANGONET_PATTERN_REFERENCE, $contenu, $matches))
				$langue = $matches[1];
	}

	if (!file_exists($fichier_lang = _DIR_RACINE . $ou_langue . $module . '_' . $langue . '.php')) {
		$fichiers = preg_files(_DIR_RACINE . $ou_langue, "/lang/${module}_[^/]+\.php$");
		$langue = '';
		if ($fichiers[0])
			$langue = str_replace($module . '_', '', basename($fichiers[0], '.php'));
	}

	return $langue;
}

?>