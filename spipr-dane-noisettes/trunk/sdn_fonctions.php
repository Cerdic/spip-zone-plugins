<?php
/**
 * Fonctions utiles au plugin SPIPr-Dane-Noisettes
 *
 * @plugin     SPIPr-Dane-Noisettes
 * @copyright  2019
 * @author     Dominique Lepaisant
 * @licence    GNU/GPL
 * @package    SPIP\Sdn\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/*
 * Un fichier de fonctions permet de définir des éléments
 * systématiquement chargés lors du calcul des squelettes.
 *
 * Il peut par exemple définir des filtres, critères, balises, …
 * 
 */
function sdn_update_blocs_exclus() {
    include_spip('inc/config');
    
    if (!$exclus = lire_config('sdn/blocs_exclus')) {
        $blocs_exclus_sdn = serialize(array('head','head_js','header','footer','breadcrumb'));
        $sql = sql_updateq('spip_noizetier_pages', array('blocs_exclus' => $blocs_exclus_sdn));
        $all = sql_allfetsel('blocs_exclus', 'spip_noizetier_pages');
        foreach ($all as $a) {
            if ($a['blocs_exclus'] != $blocs_exclus_sdn) {
                $err = 1;
            }
        }
        if (!$err) {
            ecrire_config('sdn/blocs_exclus', unserialize($blocs_exclus_sdn));
            echo "OK";
        }
        else {
            echo $err;
        }
    }
    
    return;
}
