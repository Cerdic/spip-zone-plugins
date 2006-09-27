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

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/sktheme_util');

function exec_sktheme_config() {
  
  global $sktheme_action;
  global $squelettes_public_dir;
  global $habillages_public_dir;
  
  debut_page(_T('sktheme:title_config'),'configuration','sktheme_config');

  echo "<br />";
  gros_titre(_T('sktheme:main_config_title'));
  
  // Include 'onglets'
  sktheme_config_onglets();
  
  debut_gauche();
  
  // Include 'raccourcis'
  sktheme_raccourcis();
  
  debut_droite();
  echo "<br />";
  debut_cadre_trait_couleur('', false, "sktheme_config", _T('sktheme:public_themes_config'));
  
  // if submit : then save values
  if ($sktheme_action=="set_dir") {
    ecrire_meta('sktheme_squelettes_public_dir',$squelettes_public_dir);
    ecrire_meta('sktheme_habillages_public_dir',$habillages_public_dir);
    ecrire_metas();
  }
  
  // Print formular
  echo '<br />';
  echo '<FORM ACTION="'.generer_url_ecrire("sktheme_config", "sktheme_action=set_dir").'" METHOD="POST">';
  echo '<strong>'._T('sktheme:squelettes_public_dir').' : </strong><INPUT TYPE=TEXT  NAME="squelettes_public_dir" SIZE=25 VALUE="'.
    $GLOBALS['meta']['sktheme_squelettes_public_dir'].'">';
  echo '<br />';
  echo '<br />';
  echo '<strong>'._T('sktheme:habillages_public_dir').' : </strong><INPUT TYPE=TEXT  NAME="habillages_public_dir" SIZE=25 VALUE="'.
    $GLOBALS['meta']['sktheme_habillages_public_dir'].'">';
  echo '<br />';
  echo '<P><DIV ALIGN="RIGHT"><INPUT TYPE=SUBMIT VALUE="'._T('sktheme:save_public_directories').'"></DIV></P>';
  echo '</FORM>';
  
  fin_cadre_trait_couleur();  
  
  debut_cadre_trait_couleur('', false, "sktheme_config", _T('sktheme:private_themes_config'));
  echo '<br />';
  echo _T('sktheme:to_be_done');
  echo '<br />';
  fin_cadre_trait_couleur();  
  
  fin_page();

}


?>