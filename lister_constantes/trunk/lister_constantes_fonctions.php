<?php

/**
 * Lister toutes les constantes définies dans l'instance de SPIP.
 * Les constantes fournies par les différents plugins actifs seront aussi prise en compte.
 *
 * @param  null|string $prefixe
 *         Préfixe de la constantes.
 *
 * @return array
 *         Si aucun préfixe, on listera toutes les constantes.
 *         Si un préfixe est identifié, on listera toutes les constantes avec ce préfixe.
 */
function lister_constantes_spip($prefixe = null)
{
    $constantes = get_defined_constants(true);

    $constantes_user = $constantes['user'];

    foreach ($constantes_user as $key => $value) {
        if ($constante = preg_split('/_/', $key, -1, PREG_SPLIT_NO_EMPTY)) {
            if ($constante[0] == '_') {
                $constantes_user[$constante[1]][$key] = $value;
            } else {
                $constantes_user[$constante[0]][$key] = $value;
            }
            unset($constantes_user[$key]);
        }
    }

    ksort($constantes_user);

    $resultat = $constantes_user;

    if ($prefixe) {
        // On pourrait faire aussi un contrôle avec array_key_exists()
        // Mais ça risque de fausser le résultat attendu.
        $resultat = array($prefixe => $constantes_user[$prefixe]);
    }

    return $resultat;
}
