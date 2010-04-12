<?php

// inc/imageflow_api_prive.php

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

// fonctions utilises en espace privé
// presentation

include_spip('inc/plugin');
include_spip('inc/presentation');
include_spip('inc/texte');

// Enveloppe message en paragraphe
function imageflow_message_p ($message) {
	$message = "<p>".$message."</p>";
	return($message);
}

/***********************************************/
function imageflow_terminer_page_non_autorisee () {
	return(imageflow_message_p(_T('imageflow:pas_acces_a_la_page')));
}

/***********************************************/
function imageflow_gros_titre ($titre, $ze_logo='', $return = false) {
	if(!imageflow_spip_est_inferieur_193()) {
		$ze_logo = ""; // semble ne plus être utilisé dans exec/*
	}
	$r = "<div style='margin-top:3em'>"
		. gros_titre($titre, $ze_logo, false)
		. "</div>\n"
		;
	return($r);
}

/* Renvoie les infos du plugin demande'
 */
function imageflow_plugin_get_infos($plug)
{
	if(version_compare($GLOBALS['spip_version_code'],'15375','>=')) {
		$get_infos = charger_fonction('get_infos','plugins');
		$infos = $get_infos($plug);
	}
	else {
		$infos = plugin_get_infos($plug);
	}
	return($infos);
}

/***********************************************/
function imageflow_html_signature ($prefix, $html = true) {

	if(version_compare($GLOBALS['spip_version_code'],'15375','>=')) {
		$get_infos = charger_fonction('get_infos','plugins');
		$info = $get_infos($prefix);
	}
	else {
		$info = imageflow_plugin_get_infos($prefix);
	}
	
	$nom = typo($info['nom']);
	$version = typo($info['version']);
	$version = 
		($version && $html) 
		? " <span>" . $version . "</span>"
		: $version
		;
	$result = ""
		. $nom
		. " " . $version
		;
	$gd_info = gd_info();
	
	if($html) {
		$result = "<p class='verdana1 spip_xx-small' id='if-html-signature'>$result - gd <span>" . $gd_info['GD Version'] . "</span></p>\n";
	}
	return($result);
}

/***********************************************/
function imageflow_boite_plugin_info ($prefix) {
	include_spip('inc/meta');
	$result = false;
	if(!empty($prefix)) {
		$plug_infos = imageflow_get_plugin_meta_infos($prefix); // dir et version
		$info = imageflow_plugin_get_infos($plug_infos['dir']);
		$icon = 
			(isset($info['icon']))
			? "<div "
				. " style='width:64px;height:64px;"
					. "margin:0 auto 1em;"
					. "background: url(". _DIR_PLUGINS.$plug_infos['dir']."/".trim($info['icon']).") no-repeat center center;overflow: hidden;'"
				. " title='Logotype plugin $prefix'>"
				. "</div>\n"
			: ""
			;
		$result = "";
		foreach(array('version', 'etat', 'auteur', 'lien') as $key) {
			if(isset($info[$key]) && !empty($info[$key])) {
				$result .= "<li>" . ucfirst($key) . ": " . propre($info[$key]) . "</li>\n";
			}
		}
		$result = ""
			. "<ul style='list-style-type:none;margin:0;padding:0 1ex' class='detailplugin verdana2'>\n"
			. $result
			. "</ul>\n"
			;
		// $result .= affiche_bloc_plugin(_DIR_PLUGIN_IMAGEFLOW, $info);
		if(!empty($result)) {
			$result = ""
				. debut_cadre_relief('plugin-24.gif', true, '', $info['nom'])
				. $icon
				. $result
				. fin_cadre_relief(true)
				;
		}
	}
	return($result);
}

/***********************************************/
function imageflow_boite_aide_info () {
	$result = ""
		// colonne gauche boite info
		. "<br />"
		. debut_boite_info(true)
		. _T('imageflow:imageflow_aide')
		. fin_boite_info(true)
		;
	return($result);
}

/***********************************************/
function imageflow_form_debut_form ($nom_form, $ancre = '') {
	global $spip_lang_left;
	if(empty($ancre)) {
		$ancre = $nom_form;
	}
	$result = ""
		. "<div style='text-align: $spip_lang_left;' class='verdana2'>\n"
		. "<form name='$nom_form' id='$nom_form' method='post' action='".$_SERVER['REQUEST_URI']."#$ancre'>\n"
		;
	return($result);
}

/***********************************************/
function imageflow_form_fin_form () {
	$result = ""
		. "</form>\n"
		. "</div>\n"
		;
	return($result);
}

/***********************************************/
function imageflow_input_value ($label, $input_name, $value = "") {
	global $spip_lang_left;
	$result = ""
		. "<label style='text-align:$spip_lang_left'>\n"
		. "<span class='verdana2' style='display:block;margin-bottom:0.5em'>" . $label . "</span>\n"
		. "<input type='text' name='$input_name' value='" . $value . "' class='forml' />\n"
		. "</label>\n"
		;
	return($result);
}

/***********************************************/
function imageflow_input_checkbox ($label, $input_name, $value, $checked = false, $style = "") {
	global $spip_lang_left;
	$checked = ($checked ? "checked='checked'" : "");
	$result = ""
		. "<label id='label_$input_name' class='verdana2' style='".$style."text-align:$spip_lang_left'>\n"
		. "<input type='checkbox' name='$input_name' value='$value' $checked />\n"
		. $label
		. "</label>\n"
		;
	return($result);
	
}

/***********************************************/
function imageflow_form_bouton_valider ($nom_bouton) {
	global $spip_lang_right;
	$result = 
		"<div style='text-align:$spip_lang_right'>\n"
		. "<input type='submit' name='$nom_bouton' value='"._T('bouton_valider')."' class='fondo' />\n"
		. "</div>\n"
		;
	return($result);
}

/***********************************************/
function imageflow_boite_alerte ($titre, $message) {
	$result = ""
		. debut_boite_alerte()
		. "<h3 style='margin:0;padding:0;color:black'>" . $titre . "</h3>\n"
		.  http_img_pack("warning.gif", _T('info_avertissement'), 
				 "style='width:48px;height:48px;float:right;margin:5px;'")
		. "<span class='verdana2 message-alerte'>$message</span>\n"
		. fin_boite_alerte()
		. "\n<br />"
		;
	return($result);
}

/***********************************************/
function imageflow_sliders_lister ($dir = _DIR_IMAGEFLOW_IMAGES) {
	$result = array();
	$dir = rtrim(_DIR_IMAGEFLOW_IMAGES, "/")."/";
	if (is_dir($dir) && ($dh = opendir($dir))) {
		while (($file = readdir($dh)) !== false) {
			if (preg_match(';^(slider_).*(-14\.gif)$;', $file)) {
				$result[] = $dir . $file;					
			}
		}
		closedir($dh);
		return ($result);
	}
	return (false);
}


