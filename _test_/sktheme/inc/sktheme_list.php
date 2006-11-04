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
  
  // Create theme list
function sktheme_list() {
    
  include_spip('inc/sktheme_xml');

  $original = _T('sktheme:original');
  $sktheme_list = array();
  
  $s_dir = _DIR_RACINE.$GLOBALS['meta']['sktheme_squelettes_public_dir'];
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
    // no extra directory for squelette
    array_push($sktheme_list,"dist::".$original);
    return $sktheme_list;
  }

  $h_dir = _DIR_RACINE.$GLOBALS['meta']['sktheme_habillages_public_dir'];
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
	      $habillages_list[$dir] = sktheme_xml_get_infos($h_dir."/".$dir,"theme");
	    }
	  }
	}
      }
      closedir($dh);
    } 
  } else {
    // no habillage directory
    foreach( $squelettes_list as $s_key => $s_value) {
      array_push($sktheme_list,$s_key."::".$original);
      return $sktheme_list;
    }
  }

  // Look for matching theme
  foreach( $squelettes_list as $s_key => $s_value) {
    
    foreach( $habillages_list as $h_key => $h_info) {
      if ($h_key == $original) {
	array_push($sktheme_list,$s_key."::".$h_key);
      } else {
	$squelette_ok = false;
	if (is_array($h_info['squelettes'])) {
	  foreach ($h_info['squelettes'] as $sq){
	    $sq = trim($sq);
	    if ($sq == $s_key) {
	      $squelette_ok = true;
	    }
	  }
	} else {
	  if ($h_info['squelettes'] == $s_key) {
	    $squelette_ok = true;
	  }
	}
	if ($squelette_ok) {
	  array_push($sktheme_list,$s_key."::".$h_key);
	}
      }
    }
  }
  return $sktheme_list;
}



function sktheme_habillage_list() {
    
  include_spip('inc/sktheme_xml');

  $original = _T('sktheme:original');
  $sktheme_habillage_list = array();
  
  $squelette_name = $GLOBALS['meta']['sktheme_squelette_public_name'];
  
  $h_dir = _DIR_RACINE.$GLOBALS['meta']['sktheme_habillages_public_dir'];
  $habillages_list = array($original => '');
  if (is_dir($h_dir)) {
    if ($dh = opendir($h_dir)) {
      while (($file = readdir($dh)) !== false) {
	if ( (is_dir($h_dir."/".$file)) AND ($file[0]!=".") ) {
	  $habillages_list[$file] = sktheme_xml_get_infos($h_dir."/".$file,"theme");
	}
      }
      closedir($dh);
    } 
  } else {
    // no habillage directory
    array_push($sktheme_habillage_list,$original);
    return $sktheme_habillage_list;
  }

  // Look for matching theme
    
  foreach( $habillages_list as $h_key => $h_info) {
    if ($h_key == $original) {
      array_push($sktheme_habillage_list,$h_key);
    } else {
      $squelette_ok = false;
      if (is_array($h_info['squelettes'])) {
	foreach ($h_info['squelettes'] as $sq){
	  $sq = trim($sq);
	  if ($sq == $squelette_name) {
	    $squelette_ok = true;
	  }
	}
      } else {
	if ($h_info['squelettes'] == $squelette_name) {
	  $squelette_ok = true;
	}
      }
      if ($squelette_ok) {
	array_push($sktheme_habillage_list,$h_key);
      }
    }
  }
  return $sktheme_habillage_list;
}
?>