<?php

// balise/imageflow_args.php

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

// $LastChangedRevision: 20912 $
// $LastChangedBy: paladin@quesaco.org $
// $LastChangedDate: 2008-06-09 20:05:56 +0200 (lun., 09 juin 2008) $

if(!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('inc/imageflow_api_globales');

// Balise independante du contexte


// insert les arguments pour reflect.php
// A placer dans votre squelette, sur l'URL de l'image appelée
function balise_IMAGEFLOW_ARGS ($p) {
	
	$preferences_meta = imageflow_get_all_preferences();
	$preferences_default = unserialize(_IMAGEFLOW_PREFERENCES_DEFAULT);
	
	foreach($preferences_meta as $key => $value) {
		if($key == 'img') continue;
		if(empty($value)) {
			$value = $preferences_default[$key];
		}
		//$insert .= "&amp;" . $key . "=" . rawurlencode($value);
		$insert .= "&" . $key . "=" . rawurlencode($value);
	}

	$p->code = "'".$insert."'";
	$p->interdire_scripts = false;
	
	return($p);
}

?>