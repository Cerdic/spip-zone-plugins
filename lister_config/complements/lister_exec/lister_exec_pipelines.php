<?php
/**
 * Plugin Lister les pages de configurations
 * Licence GPL
 * Auteur : Teddy Payet
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}

function lister_exec_listermenu($liste) {

    $complement = array(
        'lister_exec' => array(
            'titre' => _T('lister_exec:titre_lister_exec'),
            'icone' => 'prive/themes/spip/images/lister_exec-16.png'
        )
    );
    $liste = array_merge($liste, $complement);

    return $liste;
}

?>