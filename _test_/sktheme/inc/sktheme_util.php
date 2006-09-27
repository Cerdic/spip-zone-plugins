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

include_spip('inc/presentation');

// Build 'onglets' for all pages
function sktheme_public_choice_onglets() {

  debut_onglet();
  // onglet($texte, $lien, $onglet_ref, $onglet, $icone="")
  onglet(_T('sktheme:public_choice'),  "?exec=sktheme_public_choice",  "sktheme_public_choice", "sktheme_public_choice", "");
  onglet(_T('sktheme:private_choice'), "?exec=sktheme_private_choice", "sktheme_public_choice", "sktheme_private_choice", "");
  onglet(_T('sktheme:configuration'),  "?exec=sktheme_config",         "sktheme_public_choice", "sktheme_config", "");
   fin_onglet();

}
function sktheme_private_choice_onglets() {

  debut_onglet();
  // onglet($texte, $lien, $onglet_ref, $onglet, $icone="")
  onglet(_T('sktheme:public_choice'),  "?exec=sktheme_public_choice",  "sktheme_private_choice", "sktheme_public_choice", "");
  onglet(_T('sktheme:private_choice'), "?exec=sktheme_private_choice", "sktheme_private_choice", "sktheme_private_choice", "");
  onglet(_T('sktheme:configuration'),  "?exec=sktheme_config",         "sktheme_private_choice", "sktheme_config", "");
  fin_onglet();

}
function sktheme_config_onglets() {

  debut_onglet();
  // onglet($texte, $lien, $onglet_ref, $onglet, $icone="")
  onglet(_T('sktheme:public_choice'),  "?exec=sktheme_public_choice",  "sktheme_config", "sktheme_public_choice", "");
  onglet(_T('sktheme:private_choice'), "?exec=sktheme_private_choice", "sktheme_config", "sktheme_private_choice", "");
  onglet(_T('sktheme:configuration'),  "?exec=sktheme_config",         "sktheme_config", "sktheme_config", "");
  fin_onglet();

}

// Build 'raccourcis' for all pages
function sktheme_raccourcis() {

  debut_raccourcis();
  
  
  fin_raccourcis();

}


?>