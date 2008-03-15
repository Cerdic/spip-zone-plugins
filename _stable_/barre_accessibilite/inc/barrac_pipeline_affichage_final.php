<?php 
	
	// inc/barrac_pipeline_affichage_final.php
	
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

// pipeline (plugin.xml)
// Insère corrections dynamiques avant head + la barre d'accessibilité après la balise body
function barrac_affichage_final ($flux) {

	include_spip('inc/plugin_globales_lib');
	include_spip('inc/barrac_api_icones');
	
	$barrac_insert_avant_head = $barrac_css_fixed_pour_ie = 
		$barrac_forcer_result = $barrac_css_result_par_uri = "";

	$config_default = unserialize(_BARRAC_DEFAULT_VALUES_ARRAY);
	
	// charger la config barrac
	$config = __plugin_lire_key_in_serialized_meta('config', _BARRAC_META_PREFERENCES);
	
	// corrige les manques éventuels
	foreach($config_default as $key=>$value) {
		if(!isset($config[$key])) $config[$key] = $config_default[$key];
	}

	$boutons_actifs_count = barrac_boutons_actifs_count($config);
		
	// les actions peuvent être demandées en arguments URI
	$barrac_forcer_par_uri = array();
	foreach(array(_BARRAC_ACTION_GROSSIR, _BARRAC_ACTION_ESPACER, _BARRAC_ACTION_ENCADRER, _BARRAC_ACTION_INVERSER) as $key) {
		$barrac_forcer_par_uri[$key] = (($config[$key]=='oui') && _request($key)=='oui');
	}

	// placer les actions forcés par URI (en général à cause de javascript désactivé)
	if($barrac_forcer_par_uri[_BARRAC_ACTION_GROSSIR]) {
		if(($config['barrac_grossir_global'] == "non") && (!empty($config['barrac_grossir_cssfile']))) {
			$barrac_forcer_result .= barrac_css_rel_link ('barrac_grossir_css', $config['barrac_grossir_cssfile'], 'nav_caracteres_grossir');
		}
		else {
			$barrac_css_result_par_uri .= "body { font-size:".$config['barrac_grossir_taille']."%; } ";
		}
	}
	if($barrac_forcer_par_uri[_BARRAC_ACTION_ESPACER]) {
		if(($config['barrac_espacer_global'] == "non") && (!empty($config['barrac_espacer_cssfile']))) {
			$barrac_forcer_result .= barrac_css_rel_link ('barrac_espacer_css', $config['barrac_espacer_cssfile'], 'nav_espacer_liens');
		}
		else {
			$barrac_css_result_par_uri .= "p a { margin:".$config['barrac_espacer_taille']."; } ";
		}
	}
	if($barrac_forcer_par_uri[_BARRAC_ACTION_ENCADRER]) {
		if(($config['barrac_encadrer_global'] == "non") && (!empty($config['barrac_encadrer_cssfile']))) {
			$barrac_forcer_result .= barrac_css_rel_link ('barrac_encadrer_css', $config['barrac_encadrer_cssfile'], 'nav_encadrer_liens');
		}
		else {
			$barrac_css_result_par_uri .= 
				".titre, .texte { padding:".$config['barrac_encadrer_padding'].";"
				. " border:".$config['barrac_encadrer_taille']." solid ".$config['barrac_encadrer_couleur']."; } ";
		}
	}
	if($barrac_forcer_par_uri[_BARRAC_ACTION_INVERSER]) {
		if(($config['barrac_inverser_global'] == "non") && (!empty($config['barrac_inverser_cssfile']))) {
			$barrac_forcer_result .= barrac_css_rel_link ('barrac_inverser_css', $config['barrac_inverser_cssfile'], 'nav_inverser_liens');
		}
		else {
			// filter:Invert() n'est compris que par MSIE
			$barrac_css_result_par_uri .= " body { filter:Invert(); } ";
		}
	}
	
	// si IE 6|5.5 détecté, corrige position:fixed
	if($ii = barrac_ie_6_5_fixed_position (
		preg_match('=^top=', $config['barrac_position_barre'])
		, preg_match('=_left=', $config['barrac_position_barre'])
		, (
			($config['barrac_presentation_barre']==_BARRAC_PRESENTATION_HORIZONTAL) 
			? barrac_barre_largest_side_size($config, $boutons_actifs_count) 
			: $config['barrac_taille_bouton']
			)
		, (
			($config['barrac_presentation_barre']==_BARRAC_PRESENTATION_VERTICAL) 
			? barrac_barre_largest_side_size($config, $boutons_actifs_count) 
			: $config['barrac_taille_bouton']
			)
		)
	) {
		$barrac_insert_avant_head .= ""
			. "<style type='text/css'>\n"
			. "#barrac_boutons {\n"
			. $ii 
			. "}\n"
			. "\n</style>\n"
			;
	}
	else if(
		!barrac_browser_is_explorer ()
		&& ($config[_BARRAC_ACTION_INVERSER] == 'oui') 
		&& ($config['barrac_inverser_global'] == 'oui')
	) {
		// pas en IE et pas de fichier CSS ? Dévalide le bouton inverse
		$barrac_insert_avant_head .= ""
			. "<script type='text/javascript'>"
			. "$(document).ready(function() {"
			. "$('#barrac_item_inverser').hide();"
			. "$('#barrac_boutons').css({ " 
				. (($config['barrac_presentation_barre'] == _BARRAC_PRESENTATION_HORIZONTAL) ? "width" : "height") 
				. ": "	. "'" . barrac_barre_largest_side_size($config, $boutons_actifs_count - 1) . "px' });"
			. "});"
			. "</script>"
			;
	}
	
	// envelopper les corrections par uri
	
	if(!empty($barrac_css_result_par_uri)) {
		$barrac_css_result_par_uri = "<!-- barrac par uri -->"
			. "<style type='text/css'>\n"
			. $barrac_css_result_par_uri . "\n</style>\n"
			. "<!-- barrac par uri end -->";
	}

	// barre des boutons en liste (ul/li) en javascript
	
	$barrac_icones_liste_script = preg_replace('/(title="[^"]*")/e', 'preg_replace("/\'/", "\'", "${1}")', barrac_icones_liste());
	
	$pat = array('=\"=', '=(/)=', '=[[:space:]]+=');
	$rep = array("'", '\/', ' ');
	$barrac_icones_liste_script = preg_replace($pat, $rep, $barrac_icones_liste_script);
	
	$barrac_icones_liste_script = "\n"
		. "<script type='text/javascript'>\n"
		. "//<![CDATA[\n"	// mode strict demande CDATA, sinon erreurs d'analyse
		. "document.write(\""
		. trim($barrac_icones_liste_script)
		. "\");\n"
		. "\n"
		. "//]]>\n"
		. "</script>\n"
		;
	
	// barre des boutons en liste (ul/li) pour noscript
	
	$barrac_icones_liste_noscript = "<noscript>\n" . barrac_icones_liste(false, true) . "</noscript>\n";

	// placer les correctifs d'appels en URI et autres modifications de dernières minutes

	$barrac_insert_avant_head = $barrac_css_result_par_uri . $barrac_css_fixed_pour_ie . $barrac_insert_avant_head
		//. "<!-- debug " . $_SERVER['HTTP_USER_AGENT'] . " -->"
		;

	if(!empty($barrac_insert_avant_head)) {
	
		$barrac_insert_avant_head = "<!-- barrac corr -->
			" . $barrac_insert_avant_head
			. "<!-- barrac corr end -->\n"
			;
		
		//$barrac_insert_avant_head = preg_replace('=[[:space:]]+=', ' ', $barrac_insert_avant_head);
		
		$flux = preg_replace('/(<\/head>)/'
			, $barrac_insert_avant_head . '${1}'
			, $flux);
	}

	// placer la barre en tete en début de page pour être immédiatement 
	// accessible par les revues d'écran (jaws, nvda, etc.)
	
	$flux = preg_replace('/(<body[^>]*>)/', '${1}' . $barrac_icones_liste_script . $barrac_icones_liste_noscript, $flux);

	return ($flux);
}


?>