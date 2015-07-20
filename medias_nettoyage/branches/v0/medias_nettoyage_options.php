<?php

/**
 * Options principales du plugin "Nettoyer la médiathèque".
 *
 * @plugin     Nettoyer la médiathèque
 *
 * @copyright  2014-2015
 * @author     Teddy Payet
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

if (!defined('_MEDIAS_NETTOYAGE_PAQUET')) {
    define('_MEDIAS_NETTOYAGE_PAQUET', 200);
}

if (!defined('_MEDIAS_NETTOYAGE_REP_ORPHELINS')) {
    define('_MEDIAS_NETTOYAGE_REP_ORPHELINS', _DIR_IMG.'orphelins/');
}
