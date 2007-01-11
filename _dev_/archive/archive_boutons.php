<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_ARCHIVE',(_DIR_PLUGINS.end($p)));

function archive_ajouter_boutons($boutons_admin) {
  // administrator only
  if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
    
    // See button in the 'configuration' 
    $boutons_admin['configuration']->sousmenu["archive_configuration"]= 
       new Bouton("../"._DIR_PLUGIN_ARCHIVE."/archive.jpg",  // icon
		  _L("archive:archive_configuration"));                                     // title
  }
  return $boutons_admin;
}
?>
