<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Lit la liste des jeux de composants ACS
 * @return array
 */
function list_sets(){
	$squelettes = array();
	$dir_sets = _DIR_PLUGIN_ACS.'sets';
	if ($d = @opendir($dir_sets)) {
		while (false !== ($file = @readdir($d))) {
			if ($file != "." && $file != ".." && substr($file, 0, 1) != '.' && @is_dir($dir_sets.'/'.$file)) {
				$squelettes[] = array('set' => $file);
			}
		}
		closedir($d);
		sort($squelettes);
		return $squelettes;
	}
	else {
		return array('erreur' => 'Impossible d\'ouvrir le jeu de composants "'.$dir_sets.'"');
	}
}
/**
 * Traitement post du formulaire formulaires/f_cfg_acs_set.html
 * @return array
 */
function formulaires_f_cfg_acs_set_traiter_dist() {
	if (
			($GLOBALS['meta']['acsSet'] != _request('acsSet')) ||
			($GLOBALS['meta']['acsSqueletteOverACS'] != _request('acsSqueletteOverACS'))
	) {
		//refuser_traiter_formulaire_ajax(); // a debugguer because pb reset.css
		ecrire_meta('acsSet', _request('acsSet'));
		ecrire_meta('acsSqueletteOverACS', _request('acsSqueletteOverACS'));
		$GLOBALS['dossier_squelettes'] = (isset($GLOBALS['meta']['acsSqueletteOverACS']) ? $GLOBALS['meta']['acsSqueletteOverACS'].':' : '')._DIR_PLUGIN_ACS.'sets/'._request('acsSet');
		ecrire_meta("acsDerniereModif", time());
		ecrire_metas();
		return array('message_ok' => _T('plugin_info_upgrade_ok'));
	}
}
?>