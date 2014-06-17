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



    if ($config['foundation_version'] == '4.1.2') return $flux.recuperer_fond('inclure/head-foundation-4.1.2');
    elseif ($config['foundation_version'] == '3.2.5') return $flux.recuperer_fond('inclure/head-foundation-3.2.5');
}

?>