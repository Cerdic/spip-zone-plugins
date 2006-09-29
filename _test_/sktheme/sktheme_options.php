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
  // cache desactivation
  $_SERVER['REQUEST_METHOD']='POST';

include_spip("inc/meta");
include_spip('inc/sktheme_balises');

// Set a default configuration - each values can be modify 
// in the private area
if (!isset($GLOBALS['meta']['sktheme_squelettes_public_dir'])){
  ecrire_meta('sktheme_squelettes_public_dir',"themes/squelettes");
  ecrire_meta('sktheme_habillages_public_dir',"themes/habillages");
  ecrire_meta('sktheme_squelette_public_name',"dist");
  ecrire_meta('sktheme_habillage_public_name',"");
  ecrire_meta('sktheme_theme_switcher_style',"font-size: 10px;background-color: #FFF;color: #0C479D;border-top: 1px solid #CECECE; border-bottom: 2px solid #4A4A4A; border-left: 1px solid #CECECE; border-right: 1px solid #CECECE;margin:2px .5em;");
  ecrire_meta('sktheme_habillage_switcher_style',"font-size: 10px;background-color: #FFF;color: #0C479D;border-top: 1px solid #CECECE; border-bottom: 2px solid #4A4A4A; border-left: 1px solid #CECECE; border-right: 1px solid #CECECE;margin:2px .5em;");
  ecrire_meta('sktheme_switcher_activated',"no");
  ecrire_meta('sktheme_switcher_admin_only',"yes");
  ecrire_metas();
} 

// Set default user choice
$s_dir = $GLOBALS['meta']['sktheme_squelettes_public_dir'].'/'.$GLOBALS['meta']['sktheme_squelette_public_name'];
$h_dir = $GLOBALS['meta']['sktheme_habillages_public_dir'].'/'.$GLOBALS['meta']['sktheme_habillage_public_name'];

//
// SWITCHER THEME
//
// Contrib de Fil : voir http://trac.rezo.net/trac/spip-zone/browser/_contribs_/switcher/switcher.php
// --------------------------------------------------------------------------------------------------
// Ask sktheme 
if (isset($_GET['sktheme'])) {
  
  // sktheme format : 
  // for theme           = squelette_name::habillage_name
  // for habillage only  = __current::habillage_name
  // 
  list($squelette,$habillage)= split ("::", $_GET['sktheme']);
    
  // For habillage only
  if ($squelette == "__current") {
    $squelette = $GLOBALS['meta']['sktheme_squelette_public_name'];
  }
    
  $s_dir = $GLOBALS['meta']['sktheme_squelettes_public_dir'].'/'.$squelette;
  $h_dir = $GLOBALS['meta']['sktheme_habillages_public_dir'].'/'.$habillage;
    
  if (is_dir(_DIR_RACINE.$s_dir)) {
    // theme exist put a cookie
    setcookie('spip_sktheme', $_COOKIE['spip_sktheme'] = $_GET['sktheme'], NULL, '/');
  } else {
    // not valid remove cookie
    setcookie('spip_sktheme', $_COOKIE['spip_sktheme'] = '', -24*3600, '/');
  }
}

if (isset($_COOKIE['spip_sktheme'])) {
  list($squelette,$habillage)= split ("::", $_COOKIE['spip_sktheme']);
  // For habillage only
  if ($squelette == "__current") {
    $squelette = $GLOBALS['meta']['sktheme_squelette_public_name'];
  }
  $s_dir_new = $GLOBALS['meta']['sktheme_squelettes_public_dir'].'/'.$squelette;
  $h_dir_new = $GLOBALS['meta']['sktheme_habillages_public_dir'].'/'.$habillage;
  
  if (is_dir(_DIR_RACINE.$s_dir_new)) {
    $s_dir = $s_dir_new;
  }
  if (is_dir(_DIR_RACINE.$h_dir_new)) {
    $h_dir = $h_dir_new;
  }
} 

// So set only the 'dossier_squelettes' to the corresponding user choice
$dossier_squelettes = $h_dir.':'.$s_dir;


?>
