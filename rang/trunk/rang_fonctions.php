<?php
/**
 * Fonctions utiles au plugin Rang
 *
 * @plugin     Rang
 * @copyright  2016
 * @author     Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\Rang\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/rang_api');

/**
 * Surcharge de la balise `#RANG`
 * 
 * Appelle balise_RANG_dist(), mais renvoie une chaine vide si le rang est null ou zéro
 * 
 */
function balise_RANG($p) {
	$p = balise_RANG_dist($p);
	
	$p->code = "(intval($p->code) == 0 ? '' : $p->code)";
	
	return $p;
}