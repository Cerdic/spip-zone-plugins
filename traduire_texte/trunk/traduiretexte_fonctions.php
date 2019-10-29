<?php
/**
 * Fichier de fonctions de traduiretexte
 *
 * @plugin     Traduire Texte
 * @copyright  2018
 * @author     Anne-lise Martenot
 * @licence    GNU/GPL
 * @package    SPIP\Traduire_texte\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/traduire_texte');

/**
 * Tester si le traducteur est disponible (ie configure) ou non
 * @return bool
 */
function traduire_texte_disponible() {
	$traducteur = TT_traducteur();
	if (!$traducteur){
		return false;
	}
	return true;
}