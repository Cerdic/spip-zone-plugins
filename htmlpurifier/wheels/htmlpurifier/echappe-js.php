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
    if (preg_match("@^<[a-z]{1,5}( class=['\"][a-z _-]+['\"])?>$@iS", $texte)){
      return $texte;
    }    
    if (!isset($safehtml)) {
        $safehtml = charger_fonction('safehtml', 'inc', true);
    }
    $texte = $safehtml($texte);
    return $texte;
}
