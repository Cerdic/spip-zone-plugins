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

include_spip('inc/presentation');   // for spip presentation functions
include_spip('inc/layer');          // for spip layer functions
include_spip('inc/utils');          // for _request function
include_spip('inc/plugin');         // xml function
include_spip('inc/sktheme_util');   // for sktheme_* functions
include_spip('inc/sktheme_xml');    // for sktheme_* functions


//
// Public page choice definition
//
function exec_sktheme_public_choice() {

  global $squelette_public_name; // parameter
  global $habillage_public_name; // parameter
  
  // Get the specific action to do if some
  $sktheme_action = _request('sktheme_action');
  $original = _T('sktheme:original');

  debut_page(_T('sktheme:public_theme_choice'),'configuration','sktheme_public_choice');

  echo "<br />";
  gros_titre(_T('sktheme:public_theme_choice'));

  // Include 'onglets'
  sktheme_public_choice_onglets();
  
  debut_gauche();

  // download a squelette distribution
//  debut_cadre_gris_clair();
//  echo '<form action="' . generer_url_action("sktheme_install") .'" method="post">';
//  echo       '<strong>'._T('sktheme:zip_squelette_url').' : </strong>';
//  echo       '<input type="text" name="zip_url" size="25" value="http://">';
//  echo       '<input type="hidden" name="zip_type" value="squelette" />';
//  echo	     '<input type="submit" value="Download" />';
//  echo	'</form>';
//  fin_cadre_gris_clair();

  // download a habillage distribution
// debut_cadre_gris_clair();
// echo '<form action="' . generer_url_action("sktheme_install") .'" method="post">';
// echo       '<strong>'._T('sktheme:zip_habillage_url').' : </strong>';
// echo       '<input type="text" name="zip_url" size="25" value="http://">';
// echo       '<input type="hidden" name="zip_type" value="habillage" />';
// echo	     '<input type="submit" value="Download" />';
// echo	'</form>';
// fin_cadre_gris_clair();

  // Include 'raccourcis'
  sktheme_public_theme_doc_box();
  
  debut_droite();
  
  //
  // Choose Squelette
  $s_dir = _DIR_RACINE.$GLOBALS['meta']['sktheme_squelettes_public_dir'];
  debut_cadre_trait_couleur('', false, "sktheme_public_squelette_list",
			    _T('sktheme:available_squelette_list')." : ".$GLOBALS['meta']['sktheme_squelettes_public_dir']);
  
  $squelettes_list = array('dist' => '' );
  if (is_dir($s_dir)) {
    if ($dh = opendir($s_dir)) {
      while (($dir = readdir($dh)) !== false) {
	if ( (is_dir($s_dir."/".$dir)) AND ($dir[0]!=".") ) {
	  // Check if a theme.xml exists and if the type is squelettes
	  if (is_file($s_dir."/".$dir."/theme.xml")) {
	    $s_info = sktheme_xml_get_infos($s_dir."/".$dir,"theme");
	    $type = (isset($s_info['type'])) ? propre($s_info['type']) : "";
	    if ($type == 'squelettes') {
	      $squelettes_list[$dir]=$s_dir."/".$dir;
	    }
	  }
	}
      }
      closedir($dh);
    }
  } else {
      echo _T('sktheme:no_available_squelette');
  }
  
  // Save if needed
  if ($sktheme_action=="set_squelette") {
    echo _T('sktheme:update_squelette_to') . $squelette_public_name. "<br />";
    ecrire_meta('sktheme_squelette_public_name',$squelette_public_name);
    // Set habillage to the original one when squelette is changed
    ecrire_meta('sktheme_habillage_public_name',$original);
    ecrire_metas();
  } else {
    $squelette_public_name = $GLOBALS['meta']['sktheme_squelette_public_name'];
  }
  	
  echo '<br />';
  echo '<form action="'.generer_url_ecrire("sktheme_public_choice", "sktheme_action=set_squelette").'" method="post">';
  foreach( $squelettes_list as $key => $value) {
    $s_info = sktheme_xml_get_infos($value,"theme");
    echo debut_cadre_gris_clair();
    echo $s_info['extra_img_puce'];
    if ($squelette_public_name==$key) {
      echo '<input type="radio" name="squelette_public_name" value="'.$key.'" checked="checked" />';
      $o_info = $s_info;
    } else {
      echo '<input type="radio" name="squelette_public_name" value="'.$key.'" />';
    }
    echo bouton_block_invisible("$key");
    echo "<strong>$key</strong>";
    echo debut_block_invisible("$key");
    echo _T('version') .' '.  $s_info['version'] . " | <strong>".$s_info['extra_titre_etat']."</strong><br />";
    if (isset($s_info['description']))
      echo "<hr />" . propre($s_info['description']) . "<br />";
    if (isset($s_info['auteur']))
      echo "<hr />" . _T('auteur') .' '. propre($s_info['auteur']) . "<br />";
    if (isset($s_info['lien']))
      echo "<hr />" . _T('info_url') .' '. propre($s_info['lien']) . "<br />";
    echo fin_block();
    echo fin_cadre_gris_clair();
  }
  echo '<br />';
  echo '<div><div style="text-align:right"><input type="submit" value="'._T('sktheme:save_squelette').'"></div></div>';
  echo '</form>';
  
  
  fin_cadre_trait_couleur();
  
  //
  // Choose Habillage
  $h_dir = _DIR_RACINE.$GLOBALS['meta']['sktheme_habillages_public_dir'];
  debut_cadre_trait_couleur('', false, "sktheme_public_habillage_list", 
			    _T('sktheme:available_habillage_list')." : ".$GLOBALS['meta']['sktheme_habillages_public_dir']);
  
  
  $habillages_list = array($original => '');
  if (is_dir($h_dir)) {
    if ($dh = opendir($h_dir)) {
      while (($dir = readdir($dh)) !== false) {
	if ( (is_dir($h_dir."/".$dir)) AND ($dir[0]!=".") ) {
	  // Check if a theme.xml exists and if the type is themes
	  if (is_file($h_dir."/".$dir."/theme.xml")) {
	    $h_info = sktheme_xml_get_infos($h_dir."/".$dir,"theme");
	    $type = (isset($h_info['type'])) ? propre($h_info['type']) : "";
	    if ($type == 'themes') {
	      $habillages_list[$dir]=$h_dir."/".$dir;
	    }
	  }
	}
      }
      closedir($dh);
    } 
  } else {
    echo _T('sktheme:no_available_habillage');
  }
  
  // Save habillage if needed
  if ($sktheme_action=="set_habillage") {
    echo _T('sktheme:update_habillage_to') . $habillage_public_name . "<br />";
    ecrire_meta('sktheme_habillage_public_name',$habillage_public_name);
    ecrire_metas();
  } else {
    $habillage_public_name = $GLOBALS['meta']['sktheme_habillage_public_name'];
  }
    
  echo '<br />';
  echo '<form action="'.generer_url_ecrire("sktheme_public_choice", "sktheme_action=set_habillage").'" method="post">';
  foreach( $habillages_list as $key => $value) {
    $h_info = sktheme_xml_get_infos($value,"theme");

    // Check if habillage is available for this squelette selection
    // probably better way to do it in php (such grep perl function ? but its
    // works anyway)
    $squelette_ok = false;
    if (is_array($h_info['squelettes'])) {
      foreach ($h_info['squelettes'] as $sq){
	if ($sq == $squelette_public_name) {
	  $squelette_ok = true;
	}
      }
    } else {
      if ($h_info['squelettes'] == $squelette_public_name) {
	$squelette_ok = true;
      }
    }
    
    if (($key == $original)||($squelette_ok)) {
      echo debut_cadre_gris_clair();
      if ($key == $original) {
	echo $o_info['extra_img_puce'];
      } else {
	echo $h_info['extra_img_puce'];
      }
      if ($habillage_public_name==$key) {
	echo '<input type=radio  name="habillage_public_name" value="'.$key.'" checked="checked" />';
      } else {
	echo '<input type=radio  name="habillage_public_name" value="'.$key.'" />';
      }
      echo bouton_block_invisible("$key");
      if ($key == $original) {
	echo "<strong><i>$key (".$o_info['nom'].")</i></strong>";
	echo debut_block_invisible("$key");
	echo _T('version') .' '.  $o_info['version'] . " | <strong>".$o_info['extra_titre_etat']."</strong><br />";
	if (isset($o_info['description']))
	  echo "<hr />" . propre($o_info['description']) . "<br />";
	if (isset($o_info['auteur']))
	  echo "<hr />" . _T('auteur') .' '. propre($o_info['auteur']) . "<br />";
	if (isset($o_info['lien']))
	  echo "<hr />" . _T('info_url') .' '. propre($o_info['lien']) . "<br />";

      } else {
	echo "<strong>$key</strong>";
	echo debut_block_invisible("$key");
	echo _T('version') .' '.  $h_info['version'] . " | <strong>".$h_info['extra_titre_etat']."</strong><br />";
	if (isset($h_info['description']))
	  echo "<hr />" . propre($h_info['description']) . "<br />";
	if (isset($h_info['auteur']))
	  echo "<hr />" . _T('auteur') .' '. propre($h_info['auteur']) . "<br />";
	if (isset($h_info['lien']))
	  echo "<hr />" . _T('info_url') .' '. propre($h_info['lien']) . "<br />";
      }
      echo fin_block();
      echo fin_cadre_gris_clair();
    }
  }
    
  echo '<br />';
  echo '<div><div align="right"><input type="submit" value="'._t('sktheme:save_habillage').'"></div></div>';
  echo '</form>';
  
  fin_cadre_trait_couleur();
  
  
  
  
  fin_page();

}


?>