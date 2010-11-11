<?php 

	// inc/barrac_api_presentation.php
	
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
if(!function_exists('__plugin_html_signature')) {
	// petite signature de plugin
	// du style "Dossier plugin [version]"
	function __plugin_html_signature ($prefix, $return = false, $html = true) {
	
		$info = barrac_plugin_get_infos(__plugin_get_meta_dir($prefix));
		$nom = typo($info['nom']);
		$version = typo($info['version']);
		//$base_version = typo($info['version_base']); // cache ?
		$base_version = __plugin_current_version_base_get($prefix);
		$revision = "";
		if($html) {
			$version = (($version) ? " <span style='color:gray;'>".$version."</span>" : "");
			$base_version = (($base_version) ? " <span style='color:#66c;'>&lt;".$base_version."&gt;</span>" : "");
		}
		$result = ''
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
} // end if __plugin_html_signature

/**/
if(!function_exists('__plugin_boite_meta_info')) {
	// affiche un petit bloc info sur le plugin
	function __plugin_boite_meta_info ($prefix, $return = false) {
		include_spip('inc/meta');
		$result = false;
		if(!empty($prefix)) {
			$meta_info = __plugin_get_meta_infos($prefix); // dir et version
			$info = barrac_plugin_get_infos($meta_info['dir']);
			$icon = 
				(isset($info['icon']))
				? "<div "
					. " style='width:64px;height:64px;"
						. "margin:0 auto 1em;"
						. "background: url(". _DIR_PLUGINS.$meta_info['dir']."/".trim($info['icon']).") no-repeat center center;overflow: hidden;'"
					. " title='Logotype plugin $prefix'>"
					. "</div>\n"
				: ""
				;
			//$result .= __plugin_boite_meta_info_liste($info, true); // pour DEBUG
			$result .= __plugin_boite_meta_info_liste($info, false);
			$result .= __plugin_boite_meta_info_liste($meta_info, true);
			if(!empty($result)) {
				$result = ""
					. debut_cadre_relief('plugin-24.gif', true, '', $prefix)
					. $icon
					. $result
					. fin_cadre_relief(true)
					;
			}
		}
		if($return) return($result);
		else echo($result);
	}
	/**/
	function __plugin_boite_meta_info_liste($array, $recursive = false) {
		global $spip_lang_left;
		$result = "";
		if(is_array($array)) {
			foreach($array as $key=>$value) {
				$sub_result = "";
				if(is_array($value)) {
					if($recursive) {
						$sub_result = __plugin_boite_meta_info_liste($value);
					}
				}
				else {
					$sub_result = propre($value);
				}
				if(!empty($sub_result)) {
					$result .= "<li><span style='font-weight:bold;'>$key</span> : $sub_result</li>\n";
				}
			}
			if(!empty($result)) {
				$result = "<ul style='margin:0;padding:0 1ex;list-style: none;text-align: $spip_lang_left;' class='verdana2'>$result</ul>";
			}
		}
		return($result);
	}
} // end if __plugin_boite_meta_info

if(!function_exists("__boite_alerte")) {
	// Renvoie ou affiche une boite d'alerte
	function __boite_alerte ($message, $return = false) {
		$result = ""
			. debut_cadre_enfonce('', true)
			.  http_img_pack("warning.gif", _T('info_avertissement'), 
					 "style='width: 48px; height: 48px; float: right;margin: 5px;'")
			. "<strong>$message</strong>\n"
			. fin_cadre_enfonce(true)
			. "\n<br />"
			;
		if($return) return($result);
		else echo($result);
	}
}


/**
 * plugin_get_infos() n'est plus dans SPIP 2.n
 * En attendant de completer...
 **/
function barrac_plugin_get_infos($s)
{
	$info = array('nom' => null, 'version' => null, 'icon' => null);
	
	// SPIP < 2.n
	if(function_exists('plugin_get_infos'))
	{
		$info = plugin_get_infos($s);
	}
	else{
		// @todo: a ecrire
	}
	return($info);
}