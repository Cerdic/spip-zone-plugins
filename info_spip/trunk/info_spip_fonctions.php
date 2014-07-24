<?php
/**
 * Fonctions utiles au plugin Info SPIP
 *
 * @plugin     Info SPIP
 * @copyright  2013-2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_SPIP\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

function lister_noisettes_info_spip ($repertoire)
{
    $noisettes = find_all_in_path('inclure/' . $repertoire . '/', '.html$');

    if (is_array($noisettes) and count($noisettes) > 0) {
        foreach ($noisettes as $key => $value) {
            $noisettes[] = preg_replace("/.html$/", '', $key);
            unset($noisettes[$key]);
        }
        return $noisettes;
    }
    return array();
}

function lister_modules_apache ()
{
    if (function_exists('apache_get_modules')) {
        return apache_get_modules();
    }

    return array();
}

function lister_extensions_php ()
{
    if (function_exists('get_loaded_extensions')) {
        $extensions = get_loaded_extensions();
        natcasesort($extensions);
        return $extensions;
    }

    return array();
}
?>