<?php
/**
 * Fonctions utiles au plugin Material Icônes
 *
 * @plugin     Material Icônes
 * @copyright  2019
 * @author     chankalan
 * @licence    GNU/GPL
 * @package    SPIP\Materialicons\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


// retirer le xml au debut des svg
function svg_retirer_xml($svg){
        $svg = str_replace('<?xml version="1.0" encoding="UTF-8"?>','',$svg);
        return $svg;
}
