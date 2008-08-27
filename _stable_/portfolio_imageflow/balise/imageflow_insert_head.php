<?php

// balise/imageflow_insert_head.php

	/*****************************************************
	Copyright (C) 2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of "Portfolio ImageFlow".
	
	"Portfolio ImageFlow" is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	"Portfolio ImageFlow" is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with "Portfolio ImageFlow"; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de "Portfolio ImageFlow". 
	
	"Portfolio ImageFlow" est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	"Portfolio ImageFlow" est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de détails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.
	
	*****************************************************/
	
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if(!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('inc/imageflow_api_globales');

// Balise independante du contexte


// insert la ligne qui charge le css
// A placer dans votre squelette, après la balise INSERT_HEAD
function balise_IMAGEFLOW_INSERT_HEAD ($p) {
	
	$error = array();
	$css = $js = $insert = "";
	
	$preferences_meta = imageflow_get_all_preferences();
	$preferences_default = unserialize(_IMAGEFLOW_PREFERENCES_DEFAULT);
	
	foreach($preferences_meta as $key => $value) {
		if($key == 'img') continue;
		if(empty($value)) {
			$preferences_meta[$key] = $preferences_default[$key];
		}
	}

	// récupère le contenu du css et mise en cache
	if($path = find_in_path($f = "imageflow/screen.css")) {
		$path = direction_css($path);
		$css = compacte($path);
		
	}
	else {
		$error[] = $f;
	}
	
	// idem pour javascript
	if($path = find_in_path($f = "imageflow/imageflow.js")) {
		$js = compacte($path);
	}	
	else {
		$error[] = $f;
	}

	if(!empty($css) && !empty($js)) {
		$insert .= ""
			. "<link rel=\"stylesheet\" title=\"Standard\" href=\"".$css."\" type=\"text/css\" media=\"screen\" />\n"
			. "<script type=\"text/javascript\" src=\"".$js."\"></script>\n"
			;
	}
	foreach($error as $f) {
		$e = "ERROR: image flow ".$f." file not found!";
		$insert .= "<!-- ".$e." -->\n";
		imageflow_log($e);
	}

	$insert = "\n"
		. "<!-- imageflow_insert_head -->\n"
		. $insert
		. "\n"
		;

	if ($preferences_meta['preloader'] == 'oui') {
		$insert .= "
<script type=\"text/javascript\">
//<![CDATA[ 
$(document).ready(function(){
	var tmp_img = new Image();
	$(\"#imageflow #images img\").each(function(){
		tmp_img.src = $(this).attr(\"name\");
	});
});
//]]>
</script>
		";
	}
	
	
	
	//$slider = "imageflow/slider.png";
	$slider = find_in_path(_DIR_IMAGEFLOW_IMAGES . $preferences_meta['slider']);

	// correction du path pour le slider
	// + position du slider pour IE
	// + centrer le scrollbar pour IE
	$insert .= "
<style type=\"text/css\" media=\"screen\">
#imageflow {background-color:transparent;}
#scrollbar-box {text-align:center}
#scrollbar{margin:0 auto}
#slider {background-image:url(" . $slider . ");top:0;left:0}
#images {overflow: hidden;}
#lightbox {text-align:center;width:512px;height:384px;}
#affichage {max-width:512px;max-height:384px;margin:0 auto}
</style>
"
		; 

	if ($preferences_meta['slideshow'] == 'oui') {
		$js = find_in_path($f = "javascript/imageflow_slideshow.js");
		$insert .= "<script type=\"text/javascript\" src=\"".$js."\"></script>\n
<style type=\"text/css\" media=\"screen\">
#lightbox {position:relative}
#affichage {position:absolute;top:0;left:0;z-index:1024}
#affichage_cache {}
</style>
		"
		;
	}

	$p->code = "'".$insert."'";
	$p->interdire_scripts = false;
	
	return($p);
}

?>