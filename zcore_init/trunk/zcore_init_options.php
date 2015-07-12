<?php

/**
 * Options du plugin Initialiser ses squelettes Zcoreau chargement.
 *
 * @plugin     Initialiser ses squelettes Zcore
 *
 * @copyright  2015
 * @author     Teddy Payet
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}
if (!isset($GLOBALS['zi_tables_exclues'])) {
    $GLOBALS['zi_tables_exclues'] = array('spip_jobs', 'spip_types_documents', 'spip_messages', 'spip_depots', 'spip_plugins', 'spip_paquets');
}

if (!defined('_ZI_REP_SKEL')) {
    define('_ZI_REP_SKEL', _DIR_RACINE.'squelettes_zcore/');
}
if (!defined('_DIR_SQUELETTES')) {
    define('_DIR_SQUELETTES', _DIR_RACINE.'squelettes/');
}
