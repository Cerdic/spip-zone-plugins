<?php

// imageflow_options.php

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

include_spip("inc/plugin_globales_lib");

/*
 * Option debug, à n'activer qu'en dev.
 * Permet d'avoir le journal "tmp/imageflow_log" 
 * */
//define("_IMAGEFLOW_DEBUG", true);

define("_IMAGEFLOW_PREFIX", "imageflow");

if(!defined('_DIR_PLUGIN_IMAGEFLOW')) {
	$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_IMAGEFLOW',(_DIR_PLUGINS.end($p)).'/');
}

define("_DIR_IMAGEFLOW_IMAGES", _DIR_PLUGIN_IMAGEFLOW."images/");

define("_IMAGEFLOW_PREFERENCES_DEFAULT", 
	serialize(
		array(
			'img' => "" // required	The source image (to reflect)
			, 'height' => "50%" // optional	Height of the reflection (% or pixel value)
			, 'bgc' => "none" // optional	Background colour to fade into (hex), 'none' for transparent, default = 'none'
			, 'fade_start' => "80%" // optional    Start the alpha fade from whch value? (% value)
			, 'fade_end' => "0%" // optional    End the alpha fade from whch value? (% value)
			// jpeg index obsolète de reflect_2
			//, 'jpeg' => "90"" // v2 :: optional	Output will be JPEG at 'param' quality (default 90)
			, 'tint' => "#7F7F7F" // v3 :: optional    Tint the reflection with this colour (hex)
			//, 'cache' => 1 // optional    Save reflection image to the cache? (boolean)
			, 'slider' => "slider_default-14.gif"
		)
	)
);

define("_IMAGEFLOW_META_PREFERENCES", 'imageflow_preferences');

?>