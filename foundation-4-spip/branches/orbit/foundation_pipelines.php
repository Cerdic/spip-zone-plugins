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

    // On va chercher le bon squelette
    return $flux.recuperer_fond('inclure/insert_head');
}

?>