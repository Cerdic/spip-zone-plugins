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

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/sktheme_util');

function exec_sktheme_config() {
  
  global $sktheme_action;
  global $squelettes_public_dir;
  global $habillages_public_dir;
  global $habillage_switcher_style;
  global $theme_switcher_style;
  global $switcher_activated;
  global $switcher_admin_only;
  
  debut_page(_T('sktheme:title_config'),'configuration','sktheme_config');

  echo "<br />";
  gros_titre(_T('sktheme:main_config_title'));
  
  // Include 'onglets'
  sktheme_config_onglets();
  
  debut_gauche();
  
  // Include Doc box
  sktheme_config_doc_box();
  
  debut_droite();
  
  //
  // PUBLIC THEMES DIRECTORIES CONFIG
  //
  debut_cadre_trait_couleur('', false, "sktheme_config", _T('sktheme:public_themes_config'));
  
  // if submit : then save values
  if ($sktheme_action=="set_dir") {
    ecrire_meta('sktheme_squelettes_public_dir',$squelettes_public_dir);
    ecrire_meta('sktheme_habillages_public_dir',$habillages_public_dir);
    ecrire_metas();
  }
  
  // Set directories
  echo '<form action="'.generer_url_ecrire("sktheme_config", "sktheme_action=set_dir").'" method="post">';
  
  // Set squelettes_public_dir
  debut_cadre_gris_clair();
  echo '<strong>'._T('sktheme:squelettes_public_dir').' : </strong><input type="text" name="squelettes_public_dir" size="25" value="'.
    $GLOBALS['meta']['sktheme_squelettes_public_dir'].'">';
  fin_cadre_gris_clair();
  
  // Set habillages_public_dir
  debut_cadre_couleur();
  echo '<strong>'._T('sktheme:habillages_public_dir').' : </strong><input type="text" name="habillages_public_dir" size="25" value="'.
    $GLOBALS['meta']['sktheme_habillages_public_dir'].'">';
  fin_cadre_couleur(); 
  
  echo '<div><div style="text-align:right"><input type="submit" value="'._T('sktheme:save_public_directories').'" /></div></div>';
  echo '</form>';
 
  fin_cadre_trait_couleur();  
  
  //
  // PRIVATE THEMES DIRECTORIES CONFIG
  //
  //   debut_cadre_trait_couleur('', false, "sktheme_config", _T('sktheme:private_themes_config'));
  //   echo '<br />';
  //   echo _T('sktheme:to_be_done');
  //   echo '<br />';
  //   fin_cadre_trait_couleur();  
  //
  // SWITCHER OPTIONS
  //
  // if submit : save switcher options
  if ($sktheme_action=="set_switcher") {
    ecrire_meta('sktheme_theme_switcher_style',$theme_switcher_style);
    ecrire_meta('sktheme_habillage_switcher_style',$habillage_switcher_style);
    ecrire_meta('sktheme_switcher_activated',$switcher_activated);
    ecrire_meta('sktheme_switcher_admin_only',$switcher_admin_only);
    ecrire_metas();
  }
  debut_cadre_trait_couleur('', false, "sktheme_config", _T('sktheme:themes_switcher'));
  
  echo '<form action="'.generer_url_ecrire("sktheme_config", "sktheme_action=set_switcher").'" method="post">';
  
  // Set sktheme_switcher_activated
  debut_cadre_gris_clair();
  echo '<strong>'._T('sktheme:switcher_activated').' : </strong>';
  echo '<input type="radio" name="switcher_activated"  value="yes" ';
  if (isset($GLOBALS['meta']['sktheme_switcher_activated']) AND ($GLOBALS['meta']['sktheme_switcher_activated']=="yes")) {
    echo ' checked="checked" ';
  }
  echo ' />'._T('sktheme:yes');
  echo '<input type="radio" name="switcher_activated" value="no" ';
  if (isset($GLOBALS['meta']['sktheme_switcher_activated']) AND ($GLOBALS['meta']['sktheme_switcher_activated']=="no")) {
    echo ' checked="checked" ';
  }
  echo ' />'._T('sktheme:no');
  fin_cadre_gris_clair();
  
  // Set sktheme_switcher_admin_only
  debut_cadre_couleur();
  echo '<strong>'._T('sktheme:switcher_admin_only').' : </strong>';
  echo '<input type="radio" name="switcher_admin_only"  value="yes" ';
  if (isset($GLOBALS['meta']['sktheme_switcher_admin_only']) AND ($GLOBALS['meta']['sktheme_switcher_admin_only']=="yes")) {
    echo ' checked="checked" ';
  }
  echo ' />'._T('sktheme:yes');
  echo '<input type="radio" name="switcher_admin_only"  value="no" ';
  if (isset($GLOBALS['meta']['sktheme_switcher_admin_only']) AND ($GLOBALS['meta']['sktheme_switcher_admin_only']=="no")) {
    echo ' checked="checked" ';
  }
  echo ' />'._T('sktheme:no');
  fin_cadre_couleur(); 
  
  // Set theme_switcher_style
  debut_cadre_gris_clair();
  echo '<strong>'._T('sktheme:theme_switcher_style').' : </strong>';
  echo "<br />";
  echo '<textarea  cols="50" rows="8" name="theme_switcher_style">';
  echo $GLOBALS['meta']['sktheme_theme_switcher_style'].'</textarea>';  
  fin_cadre_gris_clair();
  
  // Set habillage_switcher_style
  debut_cadre_couleur();
  echo '<strong>'._T('sktheme:habillage_switcher_style').' : </strong>';
  echo "<br />";
  echo '<textarea  cols="50" rows="8" name="habillage_switcher_style">';
  echo $GLOBALS['meta']['sktheme_habillage_switcher_style'].'</textarea>';  
  fin_cadre_couleur(); 
  
    
  echo '<div><div style="text-align:right"><input type="submit" value="'._T('sktheme:save_switcher_options').'" /></div></div>';
  echo '</form>';
  
  fin_cadre_trait_couleur();  
  
  fin_page();

}


?>