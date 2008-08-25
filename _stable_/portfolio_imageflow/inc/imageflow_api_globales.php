<?php
// inc/imageflow_api_globales.php

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

function imageflow_spip_est_inferieur_193 () {
	static $is_inf;
	if($is_inf===NULL) {
		$is_inf = version_compare($GLOBALS['spip_version_code'],'1.9300','<');
	}
	return($is_inf);
}

/*
 * Journal de bord.
 * Activé uniquement en dev (voir imageflow_options.php)
 */
function imageflow_log ($message, $flag = null) {
	$flag = 
		($flag === null)
		? ""
		: " " . (!$flag ? "ERROR" : "OK")
		;
	if(!empty($message) && _IMAGEFLOW_DEBUG) {
		spip_log($message.$flag, _IMAGEFLOW_PREFIX);
	}
}

/*
 * renvoie les infos du plugin contenues dans les metas
 * qui contient 'dir' et 'version'
 */
function imageflow_get_plugin_meta_infos ($prefix) {
	if(isset($GLOBALS['meta']['plugin'])) {
		$result = unserialize($GLOBALS['meta']['plugin']);
		$prefix = strtoupper($prefix);
		if(isset($result[$prefix])) {
			return($result[$prefix]);
		}
	}
	return(false);
}

/*
 * retourne le num de version du plugin lors de la dernière installation
 * présent dans les metas
 */
function imageflow_get_meta_version ($prefix) {
	$result = false;
	$info = imageflow_get_plugin_meta_infos($prefix);
	if(isset($info['version'])) {
		$result = $info['version'];
	}
	return($result);
}

/*
 * retourne le dir du plugin
 * présent dans les metas
 */
function imageflow_get_plugin_meta_dir($prefix) {
	$result = false;
	$info = imageflow_get_plugin_meta_infos($prefix);
	if(isset($info['dir'])) {
		$result = $info['dir'];
	}
	return($result);
}

/*
 * ecriture des préférences dans les metas, format sérialisé
 */
function imageflow_set_preference ($key, $value) {
	if(isset($GLOBALS['meta'][_IMAGEFLOW_META_PREFERENCES])) {
		$s_meta = unserialize($GLOBALS['meta'][_IMAGEFLOW_META_PREFERENCES]);
		$s_meta[$key] = $value;
		return(imageflow_set_all_preferences($s_meta));
	}
	return(false);
}

/* 
 * ecriture dans les metas, format sérialisé
 * $preferences Array 
 */
function imageflow_set_all_preferences ($preferences = false) {
	$preferences =
		($preferences === false)
		? _IMAGEFLOW_PREFERENCES_DEFAULT
		: serialize($preferences)
		;
	ecrire_meta(_IMAGEFLOW_META_PREFERENCES, $preferences);
	return(imageflow_ecrire_metas());
}

/*
 * lecture dans les metas
 * retour: array ou false si inconnue
 */
function imageflow_get_all_preferences () {
	if(isset($GLOBALS['meta'][_IMAGEFLOW_META_PREFERENCES])) {
		return(unserialize($GLOBALS['meta'][_IMAGEFLOW_META_PREFERENCES]));
	}
	return(false);
}

// 
function imageflow_ecrire_metas () {
	if(imageflow_spip_est_inferieur_193()) { 
		include_spip("inc/meta");
		ecrire_metas();
	}
	return(true);
}

/*
 * Verifier PHP et GD 
 * @return FALSE ou index message (lang)
 * @see http://reflection.corephp.co.uk
 * */
 function imageflow_verifier_versions () {
 	
	//	PHP Version sanity check
	if (version_compare('4.3.2', phpversion()) == 1) 
	{
		return('error_php_old');
	}
	
	//	GD check
	if (extension_loaded('gd') == false && !dl('gd.so')) 
	{
		return('error_gd_missing');
	}
	
	//	GD Version check
	$gd_info = gd_info();
	
	if ($gd_info['PNG Support'] == false) {
		return('error_gd_not_png');
	}
	
	if (ereg_replace('[[:alpha:][:space:]()]+', '', $gd_info['GD Version']) < '2.0.1')
	{
		return('error_gd_old');
	}
	return(false);
}

?>