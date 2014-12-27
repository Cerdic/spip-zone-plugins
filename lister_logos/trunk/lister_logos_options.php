<?php
/**
 * Options du plugin Lister les logosau chargement
 *
 * @plugin     Lister les logos
 * @copyright  2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Lister_logos\Options
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

if (
    !defined('_LOGO_MAX_SIZE')
    and ini_get('upload_max_filesize')
) {
    // Si on a `upload_max_filesize` renseigné dans le fichier php.ini,
    // autant s'en servir pour la constante.
    $upload_max_filesize = floatval(ini_get('upload_max_filesize')) * 1024;
    define('_LOGO_MAX_SIZE', $upload_max_filesize);
}

?>