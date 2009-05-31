<?php 

	// inc/barrac_api_metas.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of BarrAc.
	
	BarrAc is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	BarrAc is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with BarrAc; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de BarrAc. 
	
	BarrAc est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	BarrAc est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de détails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.
	
	*****************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/plugin');

/**/
if(!function_exists('__plugin_get_meta_infos')) {
	// renvoie les infos du plugin contenues dans les metas
	// qui contient 'dir' et 'version'
	function __plugin_get_meta_infos ($prefix) {
		if(isset($GLOBALS['meta']['plugin'])) {
			$result = unserialize($GLOBALS['meta']['plugin']);
			$prefix = strtoupper($prefix);
			if(isset($result[$prefix])) {
				return($result[$prefix]);
			}
		}
		return(false);
	}
} // end if __plugin_get_meta_infos

/**/
if(!function_exists('__plugin_get_meta_dir')) {
	// renvoie le dir du plugin
	// présent dans les metas
	function __plugin_get_meta_dir($prefix) {
		$result = false;
		$info = __plugin_get_meta_infos($prefix);
		if(isset($info['dir'])) {
			$result = $info['dir'];
		}
		return($result);
	}
} // end if __plugin_get_meta_dir

/**/
if(!function_exists('__plugin_current_version_base_get')) {
	// renvoie la version_base en cours
		// doc: voir inc/plugin.php sur version_base (plugin.xml)
		// qui s'appelle base_version en spip_meta %-}
	function __plugin_current_version_base_get ($prefix) {
		return(lire_meta($prefix."_base_version"));
	}
} // end if __plugin_current_version_base_get

/**/
if(!function_exists('__plugin_real_tag_get')) {
	function __plugin_real_tag_get ($prefix, $s) {
		$dir = __plugin_get_meta_dir($prefix);
		$f = _DIR_PLUGINS.$dir."/"._FILE_PLUGIN_CONFIG;
		if(is_readable($f) && ($c = file_get_contents($f))) {
			$p = array("/<!--(.*?)-->/is","/<\/".$s.">.*/s","/.*<".$s.">/s");
			$r = array("","","");
			$r = preg_replace($p, $r, $c);
		}
		return(!empty($r) ? $r : false);
	}
} // end if __plugin_real_tag_get

if(!function_exists('__plugin_ecrire_key_in_serialized_meta')) {
	// ecriture dans les metas, format sérialisé
	function __plugin_ecrire_key_in_serialized_meta ($key, $value, $meta_name) {
		$s_meta = unserialize($GLOBALS['meta'][$meta_name]);
		$s_meta[$key] = $value;
		ecrire_meta($meta_name, serialize($s_meta));
		return(true);
	}
}

if(!function_exists('__plugin_lire_key_in_serialized_meta')) {
// lecture d'une clé dans la meta sérialisée
	function __plugin_lire_key_in_serialized_meta ($key, $meta_name) {
		$result = false;
		$s_meta = __plugin_lire_serialized_meta($meta_name);
		if($s_meta && isset($s_meta[$key])) {
			$result = $s_meta[$key];
		}
		return($result);
	}
}

if(!function_exists('__plugin_lire_serialized_meta')) {
	// lecture dans les metas, format sérialisé
	function __plugin_lire_serialized_meta ($meta_name) {
		if(isset($GLOBALS['meta'][$meta_name])) {
			return(unserialize($GLOBALS['meta'][$meta_name]));
		}
		return(false);
	}
}

if(!function_exists('__ecrire_metas')) {
	function __ecrire_metas () {
		if(version_compare($GLOBALS['spip_version_code'],'1.9300','<')) { 
			include_spip("inc/meta");
			ecrire_metas();
		}
		return(true);
	}
}

?>