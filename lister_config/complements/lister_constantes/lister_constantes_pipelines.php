<?php

/**
 * Plugin Lister les constantes PHP de SPIP.
 * Licence GPL
 * Auteur : Teddy Payet.
 */
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

function lister_constantes_listermenu($flux)
{
    $flux['data']['lister_constantes'] = array(
        'titre' => _T('lister_constantes:titre_lister_constantes'),
        'icone' => 'prive/themes/spip/images/lister_constantes-16.png',
    );

    return $flux;
}
