<?php
/**
 * Fichier de fonctions pour le plugin Socicon
 *
 * @plugin     Socicon
 * @copyright  2017
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP/Socicon/Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Lister le nom des réseaux sociaux mis à disposition par la librairie Sosicon.
 *
 * @return array
 */
function lister_socicon() {
	$style_socicon = find_in_path('lib/socicon/style.css');
	$style_socicon = file_get_contents($style_socicon);

	preg_match_all("/\.socicon-(.+):before/", $style_socicon, $socicon);
	if (isset($socicon[1]) and is_array($socicon[1]) and count($socicon[1])) {
		return $socicon[1];
	}

	return array();
}