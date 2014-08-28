<?php
/**
 * Fonctions utiles au plugin Sites pour projets
 *
 * @plugin     Sites pour projets
 * @copyright  2013-2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Projets_sites\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

function formater_tableau ($chaine)
{
    $listing = array();
    if (preg_match("/\n/", $chaine)) {
        $listing = explode("\n", $chaine);
        if (is_array($listing) and count($listing) > 0) {
            foreach ($listing as $cle => $valeur) {
                $listing[$cle] = formater_valeur($valeur);
            }
        }
    } else if (preg_match("/\|/", $chaine)){
        $listing[] = formater_valeur($chaine);
    }
    return $listing;
}

function formater_valeur ($valeur)
{
    $tableau = explode("|", $valeur);
    foreach ($tableau as $key => $value) {
        $tableau[$key] = trim($value);
    }
    return $tableau;
}
?>