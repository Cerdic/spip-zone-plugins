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
	return preg_replace(["/\r|\n/", '~\s~'], ['', '%20'], $texte);
}