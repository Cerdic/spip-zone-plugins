<?php

/**
 * Fonctions utiles au plugin jQuery Vector Maps.
 *
 * @plugin     jQuery Vector Maps
 *
 * @copyright  2015
 * @author     Teddy Payet
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

function jqvmap_format($texte = '')
{
    if ($texte == '' or empty($texte)) {
        return false;
    }
    if ($texte == 'null') {
        return 'null';
    }
    if (preg_match('/,/', $texte)) {
        $texte = explode(',', $texte);
        foreach ($texte as $key => $hexa) {
            $texte[$key] = "'".trim($hexa)."'";
        }

        return implode(', ', $texte);
    }

    return "'$texte'";
}
