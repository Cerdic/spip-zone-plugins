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

//
// Private Area Page definition 
//
function exec_sktheme_private_choice() {

  debut_page(_T('sktheme:private_theme_choice'),'configuration','sktheme_public_choice');

  echo "<br />";
  gros_titre(_T('sktheme:private_theme_choice'));
  
  // Include 'onglets'
  sktheme_private_choice_onglets();
  
  debut_gauche();
  // Include 'raccourcis'
  sktheme_raccourcis();

  
  debut_droite();
  echo "<br />";
  // debut_cadre_trait_couleur($icone='', $return = false, $fonction='', $titre=''){
  debut_cadre_trait_couleur('', false, "sktheme_private_squelette_list", _T('sktheme:available_squelette_list'));
  
  echo '<br />';
  echo _T('sktheme:to_be_done');
  echo '<br />';
   
  fin_cadre_trait_couleur();
  
  debut_cadre_trait_couleur('', false, "sktheme_squelette_list", _T('sktheme:available_habillage_list'));

  echo '<br />';
  echo _T('sktheme:to_be_done');
  echo '<br />';   
  
  fin_cadre_trait_couleur();
 
  
  
  fin_page();

}


?>