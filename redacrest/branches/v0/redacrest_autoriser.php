<?php
/**
 * Class and Function List:
 * Function list:
 * - redacrest_autoriser()
 * - autoriser_rubrique_creerarticledans()
 * Classes list:
 */

/**
 * Plugin Rédacteurs restreints
 * Licence GPL (c) 2015 Teddy Payet
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}

/* Pour que le pipeline de rale pas ! */
function redacrest_autoriser()
{
}

if (!function_exists('autoriser_rubrique_creerarticledans')) {
    function autoriser_rubrique_creerarticledans($faire, $type, $id, $qui, $opt)
    {
        include_spip('base/abstract_sql');
        include_spip('inc/rubriques');
        if (is_array($qui['restreint']) and count($qui['restreint']) > 0 and in_array($qui['statut'], array('1comite')) and $type == 'rubrique') {
            $branche = array();
            foreach ($qui['restreint'] as $rubrique) {
                $branche = array_merge($branche, explode(',', calcul_branche_in($rubrique)));
            }
            if (in_array($id, $branche)) {
                return true;
            } else {
                return false;
            }
        }
        return autoriser_rubrique_creerarticledans_dist($faire, $type, $id, $qui, $opt);
    }
}
?>