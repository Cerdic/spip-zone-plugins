<?php 

	// inc/amocles_api.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2007 Christian PAULUS
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
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	Amocles est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de détails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.
	
	*****************************************************/

include_spip('inc/plugin');

if(!function_exists('__plugin_html_signature')) {
	// petite signature de plugin
	// du style "Dossier plugin [version]"
	function __plugin_html_signature ($return = false, $html = true) {
	
		$info = plugin_get_infos($plug_file = __plugin_dirname());
		$nom = typo($info['nom']);
		$version = typo($info['version']);
		//$base_version = typo($info['version_base']); // cache ?
		$base_version = __plugin_current_version_base_get(__plugin_real_tag_get('prefix'));
		$revision = "";
		if($html) {
			$version = (($version) ? " <span style='color:gray;'>".$version."</span>" : "");
			$base_version = (($base_version) ? " <span style='color:#66c;'>&lt;".$base_version."&gt;</span>" : "");
		}
		$result = ""
			. $nom
			. $version
			. $base_version
			;
		if($html) {
			$result = "<p class='verdana1 spip_xx-small' style='font-weight:bold;'>$result</p>\n";
		}
		if($return) return($result);
		else echo($result);
	}
}

if(!function_exists('__plugin_dirname')) {
	// renvoie le nom du répertoire du plugin
	function __plugin_dirname() {
		$p = trim(dirname(preg_replace("/.*".basename(_DIR_PLUGINS)."(.*)/", "$1", realpath(dirname(__FILE__)))), "/");
		return($p);
	}
}

if(!function_exists('__plugin_meta_info')) {
	// renvoie le tableau meta du plugin
	function __plugin_meta_info($prefix = "") {
		if(empty($prefix)) {
			$prefix = __plugin_real_prefix_get();
		}
		if(!empty($prefix)) {
			$meta_plugin = isset($GLOBALS['meta']['plugin']) ? $GLOBALS['meta']['plugin'] : '';
			if(!empty($meta_plugin) && is_array($result = unserialize($meta_plugin)) && isset($result[$prefix]) && is_array($result = $result[$prefix])) {
				return($result);
			}
		}
		return(false);
	}
}

if(!function_exists('__plugin_real_prefix_get')) {
	// renvoie le prefix du fichier plugin.xml
	function __plugin_real_prefix_get () {
		$r = __plugin_real_tag_get('prefix');
		return ($r ? strtoupper($r) : false);
	}
}

if(!function_exists('__plugin_real_tag_get')) {
	function __plugin_real_tag_get ($s) {
			
		$f = _DIR_PLUGINS.__plugin_dirname()."/"._FILE_PLUGIN_CONFIG;
	
		if(is_readable($f) && ($c = file_get_contents($f))) {
			$p = array("/<!--(.*?)-->/is","/<\/".$s.">.*/s","/.*<".$s.">/s");
			$r = array("","","");
			$r = preg_replace($p, $r, $c);
		}
		return(!empty($r) ? $r : false);
	}
}

if(!function_exists('__plugin_boite_meta_info')) {
	// affiche un petit bloc info sur le plugin
	function __plugin_boite_meta_info ($return = false, $prefix = "") {
		global $spip_lang_right;
	
		$result = false;
		if(empty($prefix)) {
			$prefix = __plugin_real_prefix_get();
		}
		$info = plugin_get_infos($plug_file = __plugin_dirname());
		$icon = 
			(isset($info['icon']))
			? "<div "
				. " style='width:64px;height:64px;"
					. "margin:0 auto 1em;"
					. "background: url(". _DIR_PLUGINS.$plug_file.'/'.trim($info['icon']).") no-repeat center center;overflow: hidden;'"
				. " title='Logotype plugin'>"
				. "</div>\n"
			: ""
			;
		
		if($info = __plugin_meta_info($prefix)) {
			foreach($info as $k=>$v) {
				$result .= "<li><span style='font-weight:bold;font-variant: small-caps;'>$k</span> : $v</li>";
			}
			if(!empty($result)) {
				$result = ""
					. debut_cadre_relief('plugin-24.gif', true, '', $prefix)
					. $icon
					. "<ul style='margin:0;padding:0 1ex;list-style: none;' class='verdana2'>$result</ul>"
					. fin_cadre_relief(true)
					;
			}
		}
		if($return) return($result);
		else echo($result);
	}
}

if(!function_exists('__plugin_current_version_base_get')) {
	// renvoie la version_base en cours
		// doc: voir inc/plugin.php sur version_base (plugin.xml)
		// qui s'appelle base_version en spip_meta %-}
	function __plugin_current_version_base_get ($prefix) {
		$ii = $prefix."_base_version";
		return(isset($GLOBAL['meta'][$ii]) ? $GLOBAL['meta'][$ii] : false);
	}
}

if(!function_exists('__plugin_ecrire_s_meta')) {
	// ecriture dans les metas, format sérialisé
	function __plugin_ecrire_s_meta ($key, $value, $meta_name) {
		$s_meta = unserialize($GLOBALS['meta'][$meta_name]);
		$s_meta[$key] = $value;
		ecrire_meta($meta_name, serialize($s_meta));
		return(true);
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

function amocles_titre_groupe_get ($id_groupe) {
	$row = spip_fetch_array(spip_query("SELECT titre FROM spip_groupes_mots WHERE id_groupe=$id_groupe LIMIT 0,1"));
	return($row['titre']);
}

// renvoie la liste (array) des auteurs admin groupes de mots
function amocles_admins_groupes_mots_get_ids () {
	if(
		($result = __plugin_lire_serialized_meta(_AMOCLES_META_PREFERENCES))
		&& isset($result['admins_groupes_mots_ids'])
		) {
			$result = array_values($result['admins_groupes_mots_ids']);
			$result = array_merge(array(1), $result);
	}
	else {
		$result = array(1);
	}
	return($result);
}

?>