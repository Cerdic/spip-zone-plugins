<?php

// fichier : tabledata_pipelines.php
// version : 2.1.1
// date : 10 mai 2009

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_DIR_PLUGIN_TABLEDATA'))
{ // definie automatiquement en 1.9.2
    $p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
    define('_DIR_PLUGIN_TABLEDATA',(_DIR_PLUGINS.end($p)));
}

/**
 * Ajouter le bouton de menu config si on a le droit
 *
 * @param unknown_type $boutons_admin
 * @return unknown
 */
function tabledata_ajouterBoutons($boutons_admin)
{
    // si on est admin
    if (autoriser('administrer','zone')) {
      // on voit le bouton dans la barre "naviguer"
        $boutons_admin['configuration']->sousmenu['tabledata']= new Bouton(
        _DIR_PLUGIN_TABLEDATA."img_pack/tabledata.gif",  // icone
        _T('tabledata:tabledata'));
    }
    return $boutons_admin;
}


?>