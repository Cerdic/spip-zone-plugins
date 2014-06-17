<?php
/**
 * Utilisations de pipelines par foundation-4-spip
 *
 * @plugin     foundation-4-spip
 * @copyright  2013
 * @author     Phenix
 * @licence    GNU/GPL
 * @package    SPIP\Foundation\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/*
*   Pipeline Insert_head
*/
function foundation_insert_head ($flux) {

    // On lit la configuration du plugin pour savoir quel version de Foundation charger.
    $config = lire_config('foundation');

    // On renvoie le flux head avec le squelette foundation correspondant.
    if ($config['variante'] == '3') $flux = $flux.recuperer_fond('inclure/head-foundation-3');
    elseif ($config['variante'] == '4') $flux = $flux.recuperer_fond('inclure/head-foundation-4');
    elseif ($config['variante'] == '5') $flux = $flux.recuperer_fond('inclure/head-foundation-5');
    // Si foundation est désactivé, on revoie directement le flux, sans aller chercher le head-foundation.
    elseif ($config['variante'] == '0') return $flux;

    // Charger le head commun a foundation
    $flux = $flux.recuperer_fond('inclure/head-foundation');

    return $flux;
}

/*
*   Pipeline Insert_head_css
*/
function foundation_insert_head_css ($flux) {

    // On lit la configuration du plugin pour savoir quel version de Foundation charger.
    $config = lire_config('foundation');

    // On renvoie le flux head avec le squelette foundation correspondant.
    if ($config['variante'] == '3') $flux = $flux.recuperer_fond('inclure/css/head-foundation-3');
    elseif ($config['variante'] == '4') $flux = $flux.recuperer_fond('inclure/css/head-foundation-4');
    elseif ($config['variante'] == '5') $flux = $flux.recuperer_fond('inclure/css/head-foundation-5');
    // Si foundation est désactivé, on revoie directement le flux, sans aller chercher le head-foundation.
    elseif ($config['variante'] == '0') return $flux;

    return $flux;
}


?>