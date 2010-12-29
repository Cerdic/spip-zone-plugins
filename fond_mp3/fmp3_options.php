<?php

// fmp3_options.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of Fmp3.
	
	Fmp3 is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	Fmp3 is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with Fmp3; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de Fmp3. 
	
	Fmp3 est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	Fmp3 est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de details. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en même temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.
	
	*****************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

// Pour activer (forcer) le mode debug, valider la ligne suivante
//define('_FMP3_DEBUG', true); 

include_spip('inc/fmp3_api_globales');

define("_FMP3_PREFIX", "fmp3");

if(!defined('_DIR_PLUGIN_FMP3')) {
	$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_FMP3',(_DIR_PLUGINS.end($p)).'/');
}

define("_DIR_FMP3_IMAGES", _DIR_PLUGIN_FMP3."images/");
define("_FMP3_TYPEDOC", "audio/mpeg");
define("_FMP3_JQUERY_JS", "javascript/jquery.fmp3.min.js");

define("_FMP3_PREFERENCES_DEFAULT", 
	serialize(
		array(
			'file' => "" // chemin URL du fichier mp3
			, 'autoStart' => "false" // toggle for autostarting the mp3 > true or false
			, 'repeatPlay' => "false" // toggle for repeating the mp3 > true or false
			, 'songVolume' => "90" // toggle for the volume of the song > 0 to 100
			, 'backColor' => "eeeeee" // toggle for the backgroundcolor of the player > hex code
			, 'frontColor' => "333333" // toggle for the backgroundcolor of the player > hex code
			, 'inherit' => "false" // hérite le son du parent si absent
		)
	)
);

define("_FMP3_META_PREFERENCES", 'fmp3_preferences');

