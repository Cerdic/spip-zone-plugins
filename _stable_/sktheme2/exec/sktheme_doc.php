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
include_spip('inc/flock');
include_spip('inc/sktheme_util');

//
// Private Area Page definition 
//
function exec_sktheme_doc() {

  debut_page(_T('sktheme:extra_documentation'),'configuration','sktheme_public_choice');

  echo "<br />";
  gros_titre(_T('sktheme:extra_documentation'));
  
  // Include 'onglets'
  sktheme_doc_onglets();
  
  debut_gauche();

  
  debut_droite();
  echo "<br />";
  // debut_cadre_trait_couleur($icone='', $return = false, $fonction='', $titre=''){
  debut_cadre_trait_couleur('', false, "sktheme_documentation", _T('sktheme:extra_documentation'));
  
  $doc = _DIR_PLUGIN_SKTHEME."/DOCUMENTATION.txt";
  if (is_file($doc)) {
    lire_fichier ($doc,$contenu);
    $contenu = str_replace("<", "&lt;", $contenu);
    $contenu = str_replace(">", "&gt;", $contenu);
    $contenu = str_replace(" ", "&nbsp;", $contenu);
    $contenu = str_replace("//", "<br />", $contenu);
    echo $contenu;
  } else {
    echo _T('sktheme:to_be_done');
  }
  fin_cadre_trait_couleur();
  debut_cadre_trait_couleur('', false, "sktheme_documentation", _T('sktheme:to_be_done'));
  $todo = _DIR_PLUGIN_SKTHEME."/TODO.txt";
  if (is_file($todo)) {
    lire_fichier ($todo,$contenu);
    $list = split('\/\/',$contenu);
    foreach ($list as $lign) {
      echo "$lign<br />";
    }
    echo "<br />";
  } else {
    echo _T('sktheme:to_be_done');
  }
   
  fin_cadre_trait_couleur();
  
  
  fin_page();

}


?>