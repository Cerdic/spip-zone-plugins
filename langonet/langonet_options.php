<?php
// Les REGEXP de recherche de l'item de langue (voir le fichier regexp.txt)
// -- pour les fichiers .html et .php
define("_LANGONET_TROUVER_ITEM_HP", ",(?:<:|_[L|T|U]\(['\"])(?:([a-z0-9_]+):)?((?:\\$|[\"\']\s*\.\s*\\$*)?[a-z0-9_]+)((?:{(?:[^\|=>]*=[^\|>]*)})?(?:(?:\|[^>]*)?)(?:['\"]\s*\.\s*[^\s]+)?),iS");
// -- pour les fichiers .xml
define("_LANGONET_TROUVER_ITEM_X", ",<[a-z0-9_]+>[\n|\t|\s]*([a-z0-9_]+):([a-z0-9_]+)[\n|\t|\s]*</[a-z0-9_]+()>,iS");


/**
 * Creation du select des fichiers de langue
 *
 * @param string $sel_l
 * @return array
 */
function langonet_creer_select_langues($sel_l='0') {

	$retour = creer_selects($sel_l, '0');
	return $retour['fichiers'];
}

/**
 * Creation du select des arborescences a scanner
 *
 * @param string $sel_d
 * @return array
 */
function langonet_creer_select_dossiers($sel_d='0') {

	$retour = creer_selects('0', $sel_d);
	return $retour['dossiers'];
}

/**
 * Creation d'un tableau des selects:
 * - des fichiers de langue
 * - des arborescences a scanner
 *
 * @param string $sel_l
 * @param string $sel_d
 * @return array
 */

// $sel_l  => option du select des langues
// $sel_d  => option du select des repertoires
function creer_selects($sel_l='0',$sel_d='0') {
	

	// Recuperation des repertoires des plugins
	$rep_plugins = lister_dossiers_plugins();
	// Recuperation des repertoires des extensions
	$rep_extensions = lister_dossiers_plugins(_DIR_EXTENSIONS);
	// Recuperation des repertoires SPIP et squelettes
	if (strlen($GLOBALS['dossier_squelettes'])) {
		$rep_complet = explode(':', $GLOBALS['dossier_squelettes']);
	}
	else {
		$rep_complet[] = 'squelettes';
	}
	$rep_complet[] = rtrim(_DIR_RESTREINT_ABS, '/');
	$rep_complet[] = 'prive';
	$rep_complet[] = 'squelettes-dist';
	$rep_scan = array_merge($rep_complet, $rep_plugins);
	$rep_scan = array_merge($rep_scan, $rep_extensions);
	
	// construction des <select>
	// -- les fichiers de langue
	$sel_lang = '<select name="fichier_langue" id="fichier_langue" style="margin-bottom:1em;">'."\n";
	$sel_lang .= '<option value="0"';
	$sel_lang .= ($sel_l == '0') ? ' selected="selected">' : '>';
	$sel_lang .= _T('langonet:option_aucun_fichier') . '</option>' . "\n";
	// -- les racines des arborescences a scanner
	$sel_dossier = '<select name="dossier_scan" id="dossier_scan">' . "\n";
	$sel_dossier .= '<option value="0"';
	$sel_dossier .= ($sel_d == '0') ? ' selected="selected">' : '>';
	$sel_dossier .= _T('langonet:option_aucun_dossier') . '</option>' . "\n";

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
			$reel_dir = _DIR_EXTENSIONS . $rep;
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
			foreach ($fic_lang = preg_files($reel_dir . '/lang/', '.php$', 250, false) as $le_module) {
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
		$sel_dossier .= ($sel_d == $ou_fichier) ? '" selected="selected">' : '">';
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