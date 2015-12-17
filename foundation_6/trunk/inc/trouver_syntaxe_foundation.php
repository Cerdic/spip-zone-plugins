<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Cette fonction va lire la configuration de foundation et determiner quel syntaxe doit être utilisé.
 * @param  int|array $nombre_colonnes Nombre de colonne désiré
 * @param  string $type            Foundation 4/5, type de colonne (large, medium, small)
 * @return string                  class foundation applicable directement.
 */
function trouver_syntaxe_foundation($nombre_colonnes, $type) {

    // On récupère la configuration
    $config = lire_config('foundation');

    // Version qui utilise un système large-X ou small-X. J'appel ce groupe les colnum.
    $colnum = array(4,5);

    // Les versions qui utilise des lettres => les colletr
    $colettr = array(2,3);

    // Si la première variable est un tableau, on va le convertir en class
    // On limite ce système a foundation >= 4
    if (is_array($nombre_colonnes)
    and in_array($config['variante'], $colnum)) {
        $class= '';
        foreach ($nombre_colonnes as $key => $value) {
            // Utiliser un tableau large => 4 plutôt que 4 => large
            // On est donc plus logique
            if (is_numeric($value)) {
                $class .= $key.'-'.$value.' ';
            }
            // Ancienne écriture, au cas ou
            else {
                include_spip('inc/utils');
                erreur_squelette(_T('foundation:syntaxe_deprecie'));
                $class .= $value.'-'.$key.' ';
            }
        }
        return $class;
    }
    else {
        // Si on est dans une vesion numérique de foundation, on retourne la syntaxe
        if (in_array($config['variante'], $colnum))
            return $type.'-'.$nombre_colonnes.' ';
        // Sinon, on démarrer le moteur de conversion de nombre, et on renvoie la bonne class
        elseif (in_array($config['variante'], $colettr)) {

            // Dans le cas ou un tableau est passé, c'est la colonne la plus large du tableau qui sera utilisée
            if (is_array($nombre_colonnes)) {
                $nombre_colonnes = array_keys($nombre_colonnes);
                return toWords(max($nombre_colonnes)).' ';
            }
            else
                return toWords($nombre_colonnes).' ';
        }
    }
}
