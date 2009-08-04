<?php

// inc/imageflow_header.php

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

/*
 * inc_imageflow_header_dist ()
 * Le contenu nécessaire (JS + CSS) est renvoyé pour
 * - les pages en public qui le demandent
 * - la page articles du plugin
 * @author Christian Paulus (paladin@quesaco.org)
 * @return Le contenu à placer dans le header
 */
function inc_imageflow_header_dist () {

	$error = array();
	$css = $js = $result = "";

	$preferences_meta = imageflow_get_all_preferences();
	$preferences_default = unserialize(_IMAGEFLOW_PREFERENCES_DEFAULT);

	foreach($preferences_meta as $key => $value) {
		if($key == 'img') continue;
		if(empty($value)) {
			$preferences_meta[$key] = $preferences_default[$key];
		}
	}

	// recupere le contenu du css et mise en cache
	if($path = find_in_path($f = "imageflow/screen.css")) {
		$path = direction_css($path);
		$css = compacte($path);
		
	}
	else {
		$error[] = $f;
	}
	
	$js = (defined("_IMAGEFLOW_DEBUG") && _IMAGEFLOW_DEBUG) 
		? "imageflow.js" // script d'origine avec ses corrections
		: "imageflow.0.8.min.js" // la version compressee 
		;
	$js = 'imageflow/' . $js;
	if(!($f = find_in_path($js))) {
		$error[] = $js;
		$js = false;
	}
	else {
		$js = $f;
	}

	if(!empty($css) && !empty($js)) {
		$result .= ""
			. "<link rel=\"stylesheet\" title=\"Standard\" href=\"".$css."\" type=\"text/css\" media=\"screen\" />\n"
			. "<script type=\"text/javascript\" src=\"".$js."\"></script>\n"
			;
	}
	foreach($error as $f) {
		$e = "ERROR: image flow: ".$f." file not found!";
		$result .= "<!-- ".$e." -->\n";
		imageflow_log($e);
	}

	$result = "\n"
		. "<!-- imageflow_insert_head -->\n"
		. $result
		. "\n"
		;

	// CSS
	//$slider = "imageflow/slider.png";
	$slider = find_in_path(_DIR_IMAGEFLOW_IMAGES . $preferences_meta['slider']);

	// correction du path pour le slider
	// + position du slider pour IE
	// + centrer le scrollbar pour IE
	$result .= "
<style type=\"text/css\" media=\"screen\">
#imageflow {background-color:transparent;}
#scrollbar-box {text-align:center}
#scrollbar{margin:0 auto}
#slider {background-image:url(" . $slider . ");top:0;left:0}
#images {overflow: hidden;}
#lightbox {text-align:center;width:512px;height:384px;margin:0 auto}
#affichage {display:block;max-width:512px;max-height:384px;margin:0 auto;}
.mouse-hover {cursor:pointer}
</style>
"
		; 

	if (
		($preferences_meta['slideshow'] == 'oui')
		|| (
			($preferences_meta['active_description'] == 'oui')
			&& ($preferences_meta['active_alert'] != 'oui')
			)
		)
	{
		$js = find_in_path($f = "javascript/imageflow_slideshow.js");
		$result .= "<script type=\"text/javascript\" src=\"".$js."\"></script>\n
<style type=\"text/css\" media=\"screen\">
#lightbox {position:relative}
/* #affichage {position:absolute;top:0;left:0;z-index:1024} */
#affichage_cache {}
.affichage_legend {border:1px solid #ccc;background-color:#333;color:#fff;margin-top:-1em;z-index:2000;padding:0 1ex}
</style>
"
		;
	}

	foreach(array(
		'preloader' // precharger les images ?
		, 'active_link' // activer le lien URL sur l'image finale ?
		, 'active_description' // afficher la légende contenue dans longdesc ?
		, 'active_desc_effets' // utiliser les effets de fondus pour la legende ?
		, 'active_alert' // afficher le longdesc dans une boite alerte javascript ?
		) as $pref) {
		if ($preferences_meta[$pref] == 'oui') 
		{
			$result .= "<meta name=\'x-imageflow\' id=\'x-imageflow-$pref\' content=\'oui\' />\n";
		}
	}

	return ($result);
}
?>