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
      while (($file = readdir($dh)) !== false) {
	if ( (is_dir($s_dir."/".$file)) AND ($file[0]!=".") ) {
	  $squelettes_list[$file]=$s_dir."/".$file;
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
      while (($file = readdir($dh)) !== false) {
	if ( (is_dir($h_dir."/".$file)) AND ($file[0]!=".") ) {
	  $habillages_list[$file] = sktheme_xml_get_infos($h_dir."/".$file,"habillage");
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
	if (is_array($h_info['squelette'])) {
	  foreach ($h_info['squelette'] as $sq){
	    if ($sq == $s_key) {
	      $squelette_ok = true;
	    }
	  }
	} else {
	  if ($h_info['squelette'] == $s_key) {
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
	  $habillages_list[$file] = sktheme_xml_get_infos($h_dir."/".$file,"habillage");
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
      if (is_array($h_info['squelette'])) {
	foreach ($h_info['squelette'] as $sq){
	  if ($sq == $squelette_name) {
	    $squelette_ok = true;
	  }
	}
      } else {
	if ($h_info['squelette'] == $squelette_name) {
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