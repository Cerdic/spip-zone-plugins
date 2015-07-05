<?php

/**
 * Plugin Lister les plugins nécessaires à votre site.
 * Licence GPL
 * Auteur : Teddy Payet.
 */
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

function lister_plugins_listermenu($flux)
{
    $flux['data']['lister_plugins'] = array(
        'titre' => _T('lister_plugins:titre_lister_plugins'),
        'icone' => 'prive/themes/spip/images/lister_plugins-16.png',
    );

    return $flux;
}
