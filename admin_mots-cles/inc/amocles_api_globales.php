<?php 

	// inc/amocles_api_globales.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2007-2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of Amocles.
	
	Amocles is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	Amocles is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with Amocles; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de Amocles. 
	
	Amocles est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiee par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	Amocles est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but specifique. Reportez-vous a la Licence Publique Generale GNU 
	pour plus de details. 
	
	Vous devez avoir recu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, etats-Unis.
	
	*****************************************************/
	
include_spip('inc/plugin');

function amocles_spip_est_inferieur_193 () {
	static $is_inf;
	if($is_inf===NULL) {
		$is_inf = version_compare($GLOBALS['spip_version_code'],'1.9300','<');
	}
	return($is_inf);
}

// charge les vieilles def necessaires pour SPIP 2
if(!amocles_spip_est_inferieur_193()) { 
	include_spip("inc/vieilles_defs");
}

/*
 * Journal de bord.
 * Activé uniquement en dev (voir amocles_options.php)
 */
function amocles_log ($message, $flag = null) {
	if(!empty($message) && defined('_AMOCLES_DEBUG') && _AMOCLES_DEBUG) {
		$flag = 
			($flag === null)
			? ""
			: " " . (!$flag ? "ERROR" : "OK")
			;
		spip_log($message.$flag, _AMOCLES_PREFIX);
	}
}

/*
 * @return les infos du plugin contenues dans les metas
 * qui contient 'dir' et 'version'
 */
function amocles_get_plugin_meta_infos ($prefix) {
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
function amocles_get_meta_version ($prefix) {
	$result = false;
	$info = amocles_get_plugin_meta_infos($prefix);
	if(isset($info['version'])) {
		$result = $info['version'];
	}
	return($result);
}

/*
 * @return le dir du plugin present dans les metas
 */
function amocles_get_plugin_meta_dir($prefix) {
	$result = false;
	$info = amocles_get_plugin_meta_infos($prefix);
	if(isset($info['dir'])) {
		$result = $info['dir'];
	}
	return($result);
}

/*
 * Ecriture des preferences dans les metas, format serialise
 * @return true, ou false si erreur
 */
function amocles_set_preference ($key, $value) {
	if(isset($GLOBALS['meta'][_AMOCLES_META_PREFERENCES])) {
		$s_meta = unserialize($GLOBALS['meta'][_AMOCLES_META_PREFERENCES]);
		$s_meta[$key] = $value;
		return(amocles_set_all_preferences($s_meta));
	}
	return(false);
}

/* 
 * ecriture dans les metas, format sérialisé
 * @param $preferences Array 
 * @return true
 */
function amocles_set_all_preferences ($preferences = false) {
	$preferences =
		($preferences === false)
		? _AMOCLES_PREFERENCES_DEFAULT
		: serialize($preferences)
		;
	ecrire_meta(_AMOCLES_META_PREFERENCES, $preferences);
	return(amocles_ecrire_metas());
}

function amocles_get_preference ($key) {
	if ($r = amocles_get_all_preferences())
	{
		$r = $r[$key];
	}
	return($r);
}

/*
 * lecture dans les metas des preferences du plugin
 * @return array ou false si inconnue
 */
function amocles_get_all_preferences () {
	if (isset($GLOBALS['meta'][_AMOCLES_META_PREFERENCES]))
	{
		return(unserialize($GLOBALS['meta'][_AMOCLES_META_PREFERENCES]));
	}
	return(false);
}

// 
function amocles_ecrire_metas () {
	if (amocles_spip_est_inferieur_193()) 
	{ 
		include_spip("inc/meta");
		ecrire_metas();
	}
	return(true);
}

/*
 * @return la liste (array) des auteurs admin groupes de mots
 */
function amocles_admins_groupes_mots_get_ids () {
	if (
		($result = amocles_get_all_preferences())
		&& isset($result['admins_groupes_mots_ids'])
	)
	{
			$result = array_values($result['admins_groupes_mots_ids']);
			$result = array_merge(array(1), $result);
	}
	else 
	{
		$result = array(1);
	}
	return($result);
}

?>