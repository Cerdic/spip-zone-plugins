<?php

//    Fichier créé pour SPIP.
//    Distribué sans garantie sous licence GPL.
//    Copyright (C) 2008  Pierre ANDREWS
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


  //pipeline pour ajouter des scripts dans la page public
function tjpzoom_insert_head($flux) {
  return tjpzoom_gen($flux);
  }

function tjpzoom_header_prive($flux) {
  return tjpzoom_gen($flux);
  }

function tjpzoom_gen($flux) {
  //trouver la lib tjpzoom
  $tjp = find_in_path('lib/tjpzoom/tjpzoom.js');
  if(!$tjp) $tjp = find_in_path('tjpzoom/tjpzoom.js'); //cas 1.9.2
  $toRet = $flux.'<script type="text/javascript" src="'.$tjp.'"></script>';

  //chercher les configs de la loupe
  $style = lire_config('tjpzoom/zoomstyle');
  if(!$style) $style = 'default';
  if($style == 'other') $style = lire_config('tjpzoom/zoomstyle_o'); //si le style est autre
  $conf = find_in_path("lib/tjpzoom/tjpzoom_config_$style.js"); 
  if(!$conf) $conf = find_in_path("tjpzoom_config_$style.js"); // cas 1.9.2
  $toRet .= '<script type="text/javascript" src="'.$conf.'"></script>';
  
  if($style == 'default' || $style == 'smart' || $style == 'relative'){
	$shadow = find_in_path("lib/tjpzoom/dropshadow");
	if(!$shadow) $shadow = find_in_path("tjpzoom/dropshadow");
  } else {
	$shadow = find_in_path("lib/tjpzoom/".$style);
	if(!$shadow) $shadow = find_in_path("tjpzoom/$style"); //cas 1.9.2
	if(!$shadow) $shadow = find_in_path($style); // style perso direct dans squelettes
  }
  if($shadow)
	$toRet .= '<script type="text/javascript">var TJPshadow="'.$shadow.'/"</script>';
  
  return $toRet;
}

?>
