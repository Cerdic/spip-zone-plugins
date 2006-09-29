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
  //onglet(_T('sktheme:private_choice'), "?exec=sktheme_private_choice", "sktheme_public_choice", "sktheme_private_choice", "");
  onglet(_T('sktheme:configuration'),  "?exec=sktheme_config",         "sktheme_public_choice", "sktheme_config", "");
  onglet(_T('sktheme:documentation'),  "?exec=sktheme_doc",            "sktheme_public_choice", "sktheme_doc", "");
   fin_onglet();

}
function sktheme_private_choice_onglets() {

  debut_onglet();
  // onglet($texte, $lien, $onglet_ref, $onglet, $icone="")
  onglet(_T('sktheme:public_choice'),  "?exec=sktheme_public_choice",  "sktheme_private_choice", "sktheme_public_choice", "");
  //onglet(_T('sktheme:private_choice'), "?exec=sktheme_private_choice", "sktheme_private_choice", "sktheme_private_choice", "");
  onglet(_T('sktheme:configuration'),  "?exec=sktheme_config",         "sktheme_private_choice", "sktheme_config", "");
  onglet(_T('sktheme:documentation'),  "?exec=sktheme_doc",            "sktheme_private_choice", "sktheme_doc", "");
  fin_onglet();

}
function sktheme_config_onglets() {

  debut_onglet();
  // onglet($texte, $lien, $onglet_ref, $onglet, $icone="")
  onglet(_T('sktheme:public_choice'),  "?exec=sktheme_public_choice",  "sktheme_config", "sktheme_public_choice", "");
  //onglet(_T('sktheme:private_choice'), "?exec=sktheme_private_choice", "sktheme_config", "sktheme_private_choice", "");
  onglet(_T('sktheme:configuration'),  "?exec=sktheme_config",         "sktheme_config", "sktheme_config", "");
  onglet(_T('sktheme:documentation'),  "?exec=sktheme_doc",            "sktheme_config", "sktheme_doc", "");
  fin_onglet();

}
function sktheme_doc_onglets() {

  debut_onglet();
  // onglet($texte, $lien, $onglet_ref, $onglet, $icone="")
  onglet(_T('sktheme:public_choice'),  "?exec=sktheme_public_choice",  "sktheme_doc", "sktheme_public_choice", "");
  //onglet(_T('sktheme:private_choice'), "?exec=sktheme_private_choice", "sktheme_config", "sktheme_private_choice", "");
  onglet(_T('sktheme:configuration'),  "?exec=sktheme_config",         "sktheme_doc", "sktheme_config", "");
  onglet(_T('sktheme:documentation'),  "?exec=sktheme_doc",            "sktheme_doc", "sktheme_doc", "");
  fin_onglet();

}

// Build 'raccourcis' for all pages
function sktheme_raccourcis() {

  debut_raccourcis();
  
  
  fin_raccourcis();

}

// Build documentation
function sktheme_config_doc_box() {
  
  debut_cadre_relief();
  echo _T('sktheme:config_doc_directories');
  fin_cadre_relief();
  debut_cadre_relief();
  echo _T('sktheme:config_doc_switcher');
  fin_cadre_relief();
  
}

// Build documentation
function  sktheme_public_theme_doc_box() {
  debut_cadre_relief();
  echo _T('sktheme:public_theme_doc_squelette');
  fin_cadre_relief();
  debut_cadre_relief();
  echo _T('sktheme:public_theme_doc_habillage');
  fin_cadre_relief();
 
}

?>