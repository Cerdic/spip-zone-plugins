<?php
/**
 * Trad-lang v2
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil, kent1
 *
 * Fichier des options spécifiques du plugin
 *
 * @package SPIP\Tradlang\Options
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/lang_liste');

$GLOBALS['url_arbo_parents']['tradlang'] = array('id_tradlang_module','tradlang_module');

/**
 * Définition des priorités à utiliser par défaut
 * surchargeable dans un fichier config/mes_options.php
 */
if (!defined('_TRAD_PRIORITES')) {
	define('_TRAD_PRIORITES', '10. core;20. extensions;25. community sites;30. contribs');
}

/**
 * Définition de la priorité utilisée par défaut (notamment par salvatore si utilisé)
 * surchargeable dans un fichier config/mes_options.php
 */
if (!defined('_TRAD_PRIORITE_DEFAUT')) {
	define('_TRAD_PRIORITE_DEFAUT', '30. contribs');
}

function str_statut_revision($id_tradlang, $c = false) {
	include_spip('action/editer_tradlang');
	return tradlang_set($id_tradlang, $c);
}


function dir_module_to_basename($dir_module) {
	$s = '';
	if ($dir_module) {
		$s = explode('--', $dir_module, 2);
		$s = end($s);
		$s = explode('-', $s);
		array_pop($s);
		$s = implode('-', $s);
	}
	return $s;
}

function calculer_nom_module($module, $dir_module) {
	$s = dir_module_to_basename($dir_module);
	$nom = $module;
	if (strpos($nom, 'paquet-') === 0) {
		$nom = substr($nom, 7) . " (paquet.xml)";
		if ($s and $s !== $module) {
			$nom .= " [$s]";
		}
	}
	else {
		$nom .= " [" . ($s ? $s : '?') . "]";
	}
	return $nom;
}