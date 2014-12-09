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
    $noisettes = find_all_in_path('infos_spip/' . $repertoire . '/', '.html$');

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

function sgbd_character_set_name()
{
    include_spip('base/abstract_sql');
    $character_set = sql_fetsel('default_character_set_name', 'information_schema.SCHEMATA', 'schema_name=' . sql_quote($GLOBALS['db_ok']['db']));
    return (isset($character_set['default_character_set_name'])) ? $character_set['default_character_set_name'] : false;
}

function sgbd_collation_name()
{
    include_spip('base/abstract_sql');
    $collation = sql_fetsel('default_collation_name', 'information_schema.SCHEMATA', 'schema_name=' . sql_quote($GLOBALS['db_ok']['db']));
    return (isset($collation['default_collation_name'])) ? $collation['default_collation_name'] : false;
}

?>