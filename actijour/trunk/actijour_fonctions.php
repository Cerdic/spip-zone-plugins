<?php
/**
 * Les fonctions du plugin Activités du jour
 *
 * @plugin     Activités du jour
 * @copyright  2015
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Actijour\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


// repertoire icones ACTIJOUR
if (! defined("_DIR_IMG_ACJR")) {
    define('_DIR_IMG_ACJR', _DIR_PLUGIN_ACTIJOUR . '/prive/themes/spip/images/');
}
include_spip('inc/func_acj');