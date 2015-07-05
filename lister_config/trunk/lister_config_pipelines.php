<?php

/**
 * Plugin Lister les pages de configurations
 * Licence GPL
 * Auteur : Teddy Payet.
 */
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}
function lister_config_listermenu($flux)
{
    $flux['data']['lister_config'] = array(
        'titre' => _T('lister_config:titre_lister_config'),
        'icone' => 'prive/themes/spip/images/lister_config-16.png',
    );

    return $flux;
}
