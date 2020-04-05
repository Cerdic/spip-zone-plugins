<?php

/**
 * Fichiers de fonctions complémentaires
 *
 * @plugin     Info Sites
 * @copyright  2014-2019
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_Sites\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function compiler_branches_logiciel($logiciel_nom = null) {
	include_spip('base/abstract_sql');
	$branches = array();

	$fichiers_releases = find_all_in_path(_DIR_TMP, '/releases_([a-z_]+)\.txt$');
	if (is_array($fichiers_releases) and count($fichiers_releases)) {
		foreach ($fichiers_releases as $fichier => $chemin) {
			$logiciel_found = preg_replace("/releases_/", '', $fichier);
			$logiciel_found = preg_replace("/\.txt/", '', $logiciel_found);
			$releases = file_get_contents($chemin);
			$releases = unserialize($releases);
			if (is_array($releases) and count($releases)) {
				foreach ($releases as $version) {
					$t = explode('.', $version);
					$branches[$logiciel_found][] = $t[0] . '.' . $t[1];
				}
			}
		}
	}
	$branches = array_map('array_unique', $branches);

	$branches = pipeline('compiler_branches_logiciel', array('args' => array(), 'data' => $branches));
	$branches = array_map('array_values', $branches);

	if (!is_null($logiciel_nom) and !empty($logiciel_nom)) {
		return (isset($branches[$logiciel_nom]) ? $branches[$logiciel_nom] : null);
	}

	return $branches;
}