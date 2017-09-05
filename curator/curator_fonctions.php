<?php
/**
 * Fonctions utiles au plugin curator
 *
 * @plugin     curator
 * @copyright  2014
 * @author     ydikoi
 * @licence    GNU/GPL
 * @package    SPIP\Curator\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function filtre_bookmarklet($texte) {
	return preg_replace(array("/\r|\n/", '~\s~'), array('', '%20'), $texte);
}

// habiller la page share avec minipres
function filtre_curator_minipres_dist($contenu) {
	include_spip('inc/minipres');
 	$contenu = minipres( $GLOBALS['meta']['nom_site'] . " - " . _T('share'), $contenu);
 	return $contenu;
}