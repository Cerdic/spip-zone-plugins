<?php
/**
* Plugin SPIP-Immo
*
* @author: CALV V3
* @author: Pierre KUHN V4
*
* Copyright (c) 2007-12
* Logiciel distribue sous licence GPL.
*
**/

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insertion des css du plugin dans les pages publiques
 *
 * @param $flux
 * @return mixed
 */

function spipimmo_header_prive($flux){
		$flux .= '<link rel="stylesheet" href="' ._DIR_PLUGIN_SPIPIMMO_IMG_PACK . 'spipimmo.css" type="text/css" />' . "\n";
		$flux .= '<script src="' ._DIR_PLUGIN_SPIPIMMO_IMG_PACK . 'spipimmo.js" type="text/javascript"></script>' . "\n";
		return $flux;
}

?>
