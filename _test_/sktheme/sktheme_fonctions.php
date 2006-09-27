<?php
  // ---------------------------------------------------------------------
  //
  // Sktheme : manage themes under SPIP (squelettes + habillages)
  //
  // Copyright (c) 2006 - Jerome RICHARD
  //
  // This program is free software; you can redistribute it and/or modify
  // it under the terms of the GNU General Public License as published by
  // the Free Software Foundation; either version 2 of the License, or
  // (at your option) any later version.
  //
  // You should have received a copy of the GNU General Public License
  // along with this program; 
  //
  // ---------------------------------------------------------------------

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_SKTHEME',(_DIR_PLUGINS.end($p)));

// Add private area button
function sktheme_ajouter_boutons($boutons_admin) {
  // administrator only
  if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
    
    // See button in the 'configuration' 
    $boutons_admin['configuration']->sousmenu["sktheme_public_choice"]= 
       new Bouton("../"._DIR_PLUGIN_SKTHEME."/img_pack/sktheme_icon.png",  // icon
		  _L("sktheme:manage_theme"));                                     // title
  }
  return $boutons_admin;
}

?>
