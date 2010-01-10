<?php
/**
 * Creation des deux <select> de choix des fichiers :
 *   - le fichier de langue dont on veut verifier les items
 *   - le repertoire dont l'arborescence sera scannee
 *
 * @param string $sel_l
 * @param string $sel_d
 * @param string $err_l
 * @param string $err_d
 * @return string
 */

// $sel_l  => option du select des langues
// $sel_d  => option du select des repertoires
// $err_l  => affecte si pas de choix de fichier
// $err_d  => affecte si pas de choix de repertoire
function langonet_creer_selects($sel_l = '0', $sel_d = '0', $err_l, $err_d) {
	include_spip('inc/plugin');
	$rep_normal = liste_plugin_files();
	if (strlen($GLOBALS['dossier_squelettes'])) {
		$rep_complet = explode(':', $GLOBALS['dossier_squelettes']);
	}
	else {
		$rep_complet[] = 'squelettes';
	}
	$rep_complet[] = rtrim(_DIR_RESTREINT_ABS, '/');
	$rep_complet[] = 'prive';
	$rep_complet[] = 'squelettes-dist';
	$rep_scan = array_merge($rep_complet, $rep_normal);

	// construction des <select>
	$sel_lang = '<p class='.($err_l?'"erreur_message"':'"explication"').'>'.
				"\n"._T('langonet:message_choisir_langue')."</p>\n".
				'<select name="fichier_langue" id="fichier_langue" style="margin-bottom:1em;">'."\n";
	$sel_dossier = '<p class='.($err_d?'"erreur_message"':'"explication"').'>'.
				"\n"._T('langonet:message_choisir_dossier')."</p>\n".
				'<select name="dossier_scan" id="dossier_scan">' . "\n";
	$sel_lang .= '<option value="0"';
	$sel_dossier .= '<option value="0"';
	$sel_lang .= ($sel_l == '0') ? ' selected="selected">' : '>';
	$sel_dossier .= ($sel_d == '0') ? ' selected="selected">' : '>';
	$sel_lang .= _T('langonet:message_choisir_langue') . '</option>' . "\n";
	$sel_dossier .= _T('langonet:message_choisir_dossier') . '</option>' . "\n";

	// la liste des options :
	// value (fichier_langue) =>
	//     $rep (nom du repertoire parent de lang/)
	//     $module (prefixe fichier de langue)
	//     $langue (index nom de langue)
	//     $ou_lang (chemin relatif vers fichier de langue a verifier)
	// value (dossier_scan) =>
	//     $ou_fichier (chemin relatif vers racine de l'arborescence a verifier)
	foreach ($rep_scan as $rep) {
		if (in_array($rep, $rep_normal)) {
			$reel_dir = _DIR_PLUGINS . $rep;
			$ou_fichier = str_replace('../', '', $reel_dir) . '/';
		}
		else {
			$reel_dir = _DIR_RACINE . $rep;
			$ou_fichier = $rep . '/';
		}
		if (is_dir($reel_dir . '/lang/')) {
			$sel_lang .= '<optgroup label="' . str_replace('../', '', $reel_dir) . '/">' . "\n";
			// on recupere tous les fichiers de langue directement places
			// dans lang/ sans parcourir d'eventuels sous-repertoires
			foreach ($fic_lang = preg_files($reel_dir . '/lang/', '.php$', 250, false) as $le_module) {
				preg_match_all("%_(\w{2,3})(_\w{2,3})?(_\w{2,4})?$%im", str_replace('.php', '', $le_module), $matches);
				$module = str_replace($matches[0][0].'.php', '', $le_module);
				$module = str_replace($reel_dir . '/lang/', '', $module);
				$langue = ltrim($matches[0][0], '_');
				$ou_langue = str_replace('../', '', $reel_dir) . '/lang/';
				$value = $rep.':'.$module.':'.$langue.':'.$ou_langue;
				$sel_lang .= '<option value="' . $value;
				$sel_lang .= ($value == $sel_l) ? '" selected="selected">' : '">';
				$sel_lang .= str_replace('.php', '', str_replace($reel_dir . '/lang/', '', $le_module)) . '</option>' . "\n";
			}
		}
		$sel_dossier .= '<option value="' . $ou_fichier;
		$sel_dossier .= ($sel_d == $ou_fichier) ? '" selected="selected">' : '">';
		$sel_dossier .= str_replace('../', '', $reel_dir) . '/</option>' . "\n";
	}

	$sel_lang .= '</select>' . "\n";
	$sel_dossier .= '</select>' . "\n";

	return $sel_lang . $sel_dossier;
}
?>