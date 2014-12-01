<?php

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

include_spip('inc/chercher_logo');
include_spip('base/objets');

/**
 * Récupérer les infos à partir du fichier de logo
 *
 * @param  string  $fichier
 *         Chemin ou nom du fichier de logo
 * @param  int     $index
 *         - Si l'index est `null`, on affichera un tableau représentant le résultat de preg_match()
 *         - `$index = 0` : retourne `$fichier` ;
 *         - `$index = 1` : le type de logo de l'objet. cf. art, rub, mot, etc.
 *         - `$index = 2` : l'état du logo. cf. `on` pour normal, `off` pour survol.
 *         - `$index = 3` : l'extension du fichier.
 * @return array|string
 *         Si l'index est `null`, on retournera le résultat que `preg_match()`, soit un tableau.
 *         Si l'index est une valeur numérique (<4), on retourne la valeur du tableau correspondant à l'index.
 */
function logo_infos($fichier, $index = null)
{
    // Fonction one ne peut plus simple.
    preg_match("/\/(\w+)(on|off)(\d+).(\w+)$/", $fichier, $r);
    if (isset($index) and intval($index)) {
        return $r[$index];
    }
    return $r;
}

/**
 * Avoir l'état du logo
 *
 * @uses   logo_infos()
 * @param  string $fichier
 *         Le fichier du logo.
 * @return string
 *         Retourne l'état du logo :
 *         - Logo normal (on) ;
 *         - Logo de survol (off).
 */
function logo_etat($fichier)
{
    $infos = logo_infos($fichier);

    if ($infos[2] == 'on') {
        return _T('lister_logos:logo_normal');
    } else {
        return _T('ecrire:logo_survol');
    }

}

?>