<?php

/**
 * Options du plugin Initialiser Zcore au chargement.
 *
 * @plugin     Initialiser Zcore
 *
 * @copyright  2015
 * @author     Teddy Payet
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}
if (!isset($GLOBALS['zinit_tables_exclues'])) {
    $GLOBALS['zinit_tables_exclues'] = array('spip_jobs', 'spip_types_documents', 'spip_messages', 'spip_depots', 'spip_plugins', 'spip_paquets');
}

if (!defined('_ZINIT_DIR_SQUELETTES')) {
    define('_ZINIT_DIR_SQUELETTES', _DIR_RACINE.'squelettes_zcore/');
}
if (!defined('_DIR_SQUELETTES')) {
    define('_DIR_SQUELETTES', _DIR_RACINE.'squelettes/');
}
