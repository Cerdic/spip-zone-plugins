<?php

// inc/fmp3_api_prive.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined('_ECRIRE_INC_VERSION')) return;

// fonctions utilises en espace privé
// presentation

include_spip('inc/plugin');
include_spip('inc/presentation');
include_spip('inc/texte');

// Enveloppe message en paragraphe
function fmp3_message_p ($message) {
	$message = "<p>".$message."</p>";
	return($message);
}

/***********************************************/
function fmp3_terminer_page_non_autorisee () {
	return(fmp3_message_p(_T('fmp3:pas_acces_a_la_page')));
}

/***********************************************/
function fmp3_gros_titre ($titre, $ze_logo='', $return = false) {
	if(!fmp3_spip_est_inferieur_193()) {
		$ze_logo = ""; // semble ne plus être utilisé dans exec/*
	}
	$r = "<div style='margin-top:3em'>"
		. gros_titre($titre, $ze_logo, false)
		. "</div>\n"
		;
	return($r);
}

/**
 * infos du plugin
 * @see: http://plugins.spip.net/Fond-MP3#forum1094
 * */
function fmp3_plugin_get_infos($dir)
{
	static $info;
	if($info === null)
	{
		if(version_compare($GLOBALS['spip_version_code'],'15375','>='))
		{
			$get_infos = charger_fonction('get_infos','plugins');
			$info = $get_infos(fmp3_get_plugin_meta_dir($prefix));
		}
		else
		{
			$info = plugin_get_infos(fmp3_get_plugin_meta_dir($prefix));
		} 
	}
	return($info);
}

/***********************************************/
function fmp3_html_signature ($prefix, $html = true) {

	$info = fmp3_plugin_get_infos(fmp3_get_plugin_meta_dir($prefix));
	$nom = typo($info['nom']);
	$version = typo($info['version']);
	$version = 
		($version && $html) 
		? " <span style='color:gray;'>" . $version . "</span>"
		: $version
		;
	$result = ""
		. $nom
		. " " . $version
		;
	if($html) {
		$result = "<p class='verdana1 spip_xx-small' style='font-weight:bold;'>$result</p>\n";
	}
	return($result);
}

/***********************************************/
function fmp3_boite_plugin_info ($prefix) {
	include_spip('inc/meta');
	$result = false;
	if(!empty($prefix)) {
		$plug_infos = fmp3_get_plugin_meta_infos($prefix); // dir et version
		$info = fmp3_plugin_get_infos($plug_infos['dir']);
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
			$propre = trim(propre($info[$key]));
			if(!fmp3_spip_est_inferieur_193()){
				// supprimer la balise enveloppe de SPIP 2
				$propre = preg_replace(';(^<p>(.*)<\/p>)$;s', '${2}', $propre);
			}
			if(isset($info[$key]) && !empty($info[$key])) {
				$result .= "<li>" . ucfirst($key) . ": " . $propre . "</li>\n";
			}
		}
		$result = ""
			. "<ul style='list-style-type:none;margin:0;padding:0 1ex' class='detailplugin verdana2'>\n"
			. $result
			. "</ul>\n"
			;
		// $result .= affiche_bloc_plugin(_DIR_PLUGIN_FMP3, $info);
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
function fmp3_boite_aide_info () {
	$result = ""
		// colonne gauche boite info
		. "<br />"
		. debut_boite_info(true)
		. _T('fmp3:fmp3_aide')
		. fin_boite_info(true)
		;
	return($result);
}

/***********************************************/
function fmp3_form_debut_form ($nom_form, $ancre = '') {
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
function fmp3_form_fin_form () {
	$result = ""
		. "</form>\n"
		. "</div>\n"
		;
	return($result);
}

/***********************************************/
function fmp3_input_value ($label, $input_name, $value = "") {
	global $spip_lang_left;
	$result = ""
		. "<label style='text-align:$spip_lang_left'>\n"
		. "<span class='verdana2 input_configure' style='display:block;margin-bottom:0.5em'>" . $label . "</span>\n"
		. "<input type='text' name='$input_name' value='" . $value . "' class='forml' />\n"
		. "</label>\n"
		;
	return($result);
}

/***********************************************/
function fmp3_input_checkbox ($label, $input_name, $value, $checked = false, $style = "") {
	global $spip_lang_left;
	$checked = ($checked ? "checked='checked'" : "");
	$result = ""
		. "<label id='label_$input_name' class='verdana2 input_configure' style='text-align:$spip_lang_left;$style'>\n"
		. "<input type='checkbox' name='$input_name' value='$value' $checked />\n"
		. $label
		. "</label>\n"
		;
	return($result);
	
}

/***********************************************/
function fmp3_form_bouton_valider ($nom_bouton, $style = "") {
	global $spip_lang_right;
	$result = 
		"<div style='text-align:$spip_lang_right;$style'>\n"
		. "<input type='submit' name='$nom_bouton' value='"._T('bouton_valider')."' class='fondo' />\n"
		. "</div>\n"
		;
	return($result);
}

/***********************************************/
function fmp3_boite_alerte ($titre, $message) {
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

?>