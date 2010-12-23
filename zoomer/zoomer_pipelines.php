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
function zoomer_inserer_javascript($flux){
  //$flux .='<script type="text/javascript" src="'._DIR_PLUGIN_ZOOMER.'javascript/featuredimagezoomer.js"></script>';
  $flux .='<script type="text/javascript" src="'._DIR_PLUGIN_ZOOMER.'javascript/zoomer.js"></script>';
 // $flux .=config_zoomer_js($js);
  return $flux;
}

 
function zoomer_jquery_plugins($scripts){
 $scripts[] = _DIR_PLUGIN_ZOOMER."javascript/featuredimagezoomer.js";
 return $scripts;
}



function zoomer_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
		$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_ZOOMER.'zoomer.css'.'" type="text/css" media="all" />'."\n";
	 	}
	return $flux;
}

//espace prive sur les articles uniquement .... grr
function zoomer_header_prive($flux){
	$exec = _request('exec');
	if ($exec=='articles'){
	//$js  ='<script type="text/javascript" src="'._DIR_PLUGIN_ZOOMER.'javascript/featuredimagezoomer.js"></script>';
	//$js .=config_zoomer_js();
	$flux  = zoomer_inserer_javascript($flux);
	$flux  = zoomer_insert_head_css($flux);
	}
	return $flux;
}
 
//todo cfg voir mediabox_pipelines
function config_zoomer_js(){
$js ='<script type="text/javascript"><!--

(function($) {
	var zooming = function() {
		 
		 alert("sqdqd");
	};
	$(function(){
		zooming();
		onAjaxLoad(zooming);
	});
})(jQuery); 

// --></script>'."\n";
return $js;
}
 

/*
  //chercher les configs de la loupe
  $style = lire_config('zoomer/zoomstyle');
  if(!$style) $style = 'default';
  if($style == 'other') $style = lire_config('zoomer/zoomstyle_o'); //si le style est autre
  $conf = find_in_path("javascript/zoomer/zoomer_config_$style.js"); 
  if(!$conf) $conf = find_in_path("zoomer_config_$style.js"); // cas 1.9.2
  $toRet .= '<script type="text/javascript" src="'.$conf.'"></script>';
  
  if($style == 'default' || $style == 'smart' || $style == 'relative'){
	$shadow = find_in_path("javascript/zoomer/dropshadow");
	if(!$shadow) $shadow = find_in_path("zoomer/dropshadow");
  } else {
	$shadow = find_in_path("javascript/zoomer/".$style);
	if(!$shadow) $shadow = find_in_path("zoomer/$style"); //cas 1.9.2
	if(!$shadow) $shadow = find_in_path($style); // style perso direct dans squelettes
  }
  if($shadow)
	$toRet .= '<script type="text/javascript">var TJPshadow="'.$shadow.'/"</script>';
*/

?>
