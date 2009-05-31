<?php
  // ---------------------------------------------------------------------
  //
  // Sktheme : manage themes under SPIP (squelettes + habillages)
  //
  // Copyright (c) 2006 - Skedus
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

include_spip("inc/meta");

// Set a default configuration - each values can be modify 
// in the private area
if (!isset($GLOBALS['meta']['sktheme_squelettes_public_dir'])){
  ecrire_meta('sktheme_squelettes_public_dir',"themes");
  ecrire_meta('sktheme_habillages_public_dir',"themes");
  ecrire_meta('sktheme_squelette_public_name',"dist");
  ecrire_meta('sktheme_habillage_public_name',"");
  ecrire_metas();
} 

// Set default user choice
$s_dir = $GLOBALS['meta']['sktheme_squelettes_public_dir'].'/'.$GLOBALS['meta']['sktheme_squelette_public_name'];
$h_dir = $GLOBALS['meta']['sktheme_habillages_public_dir'].'/'.$GLOBALS['meta']['sktheme_habillage_public_name'];
/*
if (isset($_GET['var_sktheme'])) {
  
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
    setcookie('$cookie_prefix.’_sktheme’', $_COOKIE['$cookie_prefix.’_sktheme’'] = $_GET['var_sktheme'], NULL, '/');
  } else {
    // not valid remove cookie
    setcookie('$cookie_prefix.’_sktheme’', $_COOKIE['$cookie_prefix.’_sktheme’'] = '', -24*3600, '/');
  }
}

if (isset($_COOKIE['$cookie_prefix.’_sktheme’'])) {
  list($squelette,$habillage)= split ("::", $_COOKIE['$cookie_prefix.’_sktheme’']);
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
*/

// So set only the 'dossier_squelettes' to the corresponding user choice
$dossier_squelettes = $h_dir.':'.$s_dir;
?>