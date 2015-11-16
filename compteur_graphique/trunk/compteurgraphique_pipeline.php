<?php

// $p = explode(basename(_DIR_PLUGINS).'/', str_replace('\\', '/', realpath(dirname(__FILE__))));
// define('_DIR_PLUGIN_COMPTEURGRAPHIQUE', (_DIR_PLUGINS.end($p)));

function compteurgraphique_declarer_tables_interfaces($interface)
{
    $interface['table_des_tables']['compteurgraphique'] = 'compteurgraphique';

    return $interface;
}

function compteurgraphique_affiche_gauche($flux)
{
    $exec = $flux['args']['exec'];
    $test_configuration = sql_select('id_compteur', 'spip_compteurgraphique', 'statut = 9');
    $tab_configuration = sql_fetch($test_configuration);
    $res_configuration = $tab_configuration['id_compteur'];
    if (!isset($res_configuration) or ($GLOBALS['connect_statut'] == '0minirezo')) {
        if ((($exec == 'article_edit') or ($exec == 'article')) and (_request('new') != 'oui')) {
            $flux['data'] .= recuperer_fond('prive/squelettes/navigation/article_compteurgraphique', array('id_article' => $flux['args']['id_article']));
        }
        if ((($exec == 'rubrique_edit') or ($exec == 'rubrique')) and (_request('new') != 'oui')) {
            $flux['data'] .= recuperer_fond('prive/squelettes/navigation/rubrique_compteurgraphique', array('id_rubrique' => $flux['args']['id_rubrique']));
        }
    }

    return $flux;
}
