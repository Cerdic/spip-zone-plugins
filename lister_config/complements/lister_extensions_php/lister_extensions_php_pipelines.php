<?php

/**
 * Plugin Lister les extensions de PHP
 * Licence GPL
 * Auteur : Teddy Payet.
 */
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

function lister_extensions_php_listermenu($flux)
{
    $flux['data']['lister_extensions_php'] = array(
        'titre' => _T('lister_extensions_php:titre_lister_extensions_php'),
        'icone' => 'prive/themes/spip/images/lister_extensions_php-16.png',
    );

    return $flux;
}
