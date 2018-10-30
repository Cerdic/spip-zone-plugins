<?php

/**
 * Fonctions utiles pour la wheel echappe-js
 *
 * @SPIP\Textwheel\Wheel\SPIP\Fonctions
 **/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function echappe_anti_xss($match) {
    static $safehtml;
    if (!is_array($match) or !strlen($match[0])) {
        return "";
    }
    $texte = &$match[0];    
    if (!isset($safehtml)) {
        $safehtml = charger_fonction('safehtml', 'inc', true);
    }
    $stexte = $safehtml($texte);
    return $stexte;
}
