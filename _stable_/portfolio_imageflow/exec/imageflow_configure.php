<?php

// exec/imageflow_configure.php

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

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/imageflow_api_globales');
include_spip('inc/imageflow_api_prive');

function exec_imageflow_configure () {

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		;

	// la configuration est réservée aux admins tt rubriques
	$autoriser = ($connect_statut == "0minirezo") && $connect_toutes_rubriques;

	if($autoriser) {
			
		$preferences_default = unserialize(_IMAGEFLOW_PREFERENCES_DEFAULT);
		$preferences_meta = imageflow_get_all_preferences();
		$preferences_current = array();
		$retour_formulaire = _request('btn_valider_imageflow');
		
		/*
		 * récupère le résultat du formulaire (si retour de ... formulaire)
		 * */
		foreach(array_keys($preferences_default) as $key) {
			if ($key == "img") {
				// inutile de s'occuper de img. Complété par le squelette.
				continue;
			}
			// si non transmise par le formulaire, prendre celle par enregistree
			$value = 
				($retour_formulaire)
				? trim(_request($key))
				: $preferences_meta[$key]
				;
			// si pas encore enregistree, prendre celle par defaut
			$preferences_current[$key] = 
				($value)
				? $value
				: $preferences_default[$key]
				;
			if(!empty($value)) 
			{
				$preferences_current[$key] = 
					(in_array($key, array('slider', 'preloader')))
					? trim($value)
					: substr(trim($value), 0, 7)
					;
			}
		}
		if ($retour_formulaire) {
			// enregistre les valeurs validées dans spip_meta
			imageflow_set_all_preferences($preferences_current);
		}
	}
	
	// lister les sliders
	$sliders_result = "";
	$sliders = imageflow_sliders_lister();
	if(is_array($sliders) && count($sliders)) 
	{
		$ii = 0;
		foreach($sliders as $img)
		{
			$slider = basename($img);
			$checked = ($slider == $preferences_current['slider']) ? " checked='checked'" : "";
			$id = "slider-".$ii++;
			$sliders_result .= ""
				. "<li class='slider" . ($checked ? " checked" : "") . "'>"
				. "<img src='$img' width='14' height='14' border='0' alt='$slider' />"
				. "<label for='$id'>$slider</label>"
				. "<input type='radio' name='slider' id='$id' value='$slider' $checked />"
				. "</li>\n"
				;
		}
		$sliders_result = ""
			. debut_cadre_relief(_DIR_IMAGEFLOW_IMAGES."deg_down-24.png", true, "", _T('imageflow:slider_select'))
			. "<ul id='sliders'>\n"
			. $sliders_result
			. "</ul>\n"
			. fin_cadre_relief(true)
			;
	}

////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = _T('imageflow:portfolio_imageflow');
	// Permet entre autres d'ajouter les classes à la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = "configuration";
	$sous_rubrique = _IMAGEFLOW_PREFIX;

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page($titre_page, $rubrique, $sous_rubrique));

	if(!$autoriser) {
		die (imageflow_terminer_page_non_autorisee() . fin_page());
	}

	$page_result = ""
		. imageflow_gros_titre(_T('titre_page_config_contenu'), '', true)
		. barre_onglets($rubrique, _IMAGEFLOW_PREFIX)
		. debut_gauche($rubrique, true)
		. imageflow_boite_plugin_info(_IMAGEFLOW_PREFIX)
		. creer_colonne_droite($rubrique, true)
		. imageflow_boite_aide_info(true)
		. debut_droite($rubrique, true)
		;
	
	// affiche milieu
	// début formulaire
	$page_result .= ""
		. debut_cadre_trait_couleur("administration-24.gif", true, "", _T('imageflow:configuration_imageflow'))
		. imageflow_form_debut_form('imageflow_configure')
		;
	
	// hauteur du relief
	$value = $preferences_current['height'];
	$page_result .= ""
		. debut_cadre_relief(_DIR_IMAGEFLOW_IMAGES."deg_height-24.png", true, "", _T('imageflow:height'))
		. imageflow_input_value (
			_T('imageflow:height_label', array('height' => $preferences_default['height']))
			, 'height', $value)
		. fin_cadre_relief(true)		
		;
		
	// couleur de fond de l'image reflet
	$value = $preferences_current['bgc'];
	$page_result .= ""
		. debut_cadre_relief(_DIR_IMAGEFLOW_IMAGES."color-24.png", true, "", _T('imageflow:bgc'))
		. imageflow_input_value (
			_T('imageflow:bgc_label', array('bgc' => $preferences_default['bgc']))
			, 'bgc', $value)
		. fin_cadre_relief(true)		
		;
		
	// opacité du dégradé du reflet. Début du dégradé. En pourcentage
	$value = $preferences_current['fade_start'];
	$page_result .= ""
		. debut_cadre_relief(_DIR_IMAGEFLOW_IMAGES."deg_down-24.png", true, "", _T('imageflow:fade_start'))
		. imageflow_input_value (
			_T('imageflow:fade_start_label', array('fade_start' => $preferences_default['fade_start']))
			, 'fade_start', $value)
		. fin_cadre_relief(true)		
		;
		
	// opacité du dégradé du reflet. Fin du dégradé. En pourcentage
	$value = $preferences_current['fade_end'];
	$page_result .= ""
		. debut_cadre_relief(_DIR_IMAGEFLOW_IMAGES."deg_up-24.png", true, "", _T('imageflow:fade_end'))
		. imageflow_input_value (
			_T('imageflow:fade_end_label', array('fade_end' => $preferences_default['fade_end']))
			, 'fade_end', $value)
		. fin_cadre_relief(true)		
		;
	
	/*
	 * Code obsolète de reflect_2 pour reflect_v3
	 * A conserver, au cas ou l'option revienne
	// qualité de compression JPEG
	$value = $preferences_current['jpeg'];
	$page_result .= ""
		. debut_cadre_relief(_DIR_IMAGEFLOW_IMAGES."jpeg_quality-24.png", true, "", _T('imageflow:jpeg'))
		. imageflow_input_value (
			_T('imageflow:jpeg_label', array('jpeg' => $preferences_default['jpeg']))
			, 'jpeg', $value)
		. fin_cadre_relief(true)		
		;
	*/
	
	// Teinte du reflet
	$value = $preferences_current['tint'];
	$page_result .= ""
		. debut_cadre_relief(_DIR_IMAGEFLOW_IMAGES."deg_up-24.png", true, "", _T('imageflow:tint'))
		. imageflow_input_value (
			_T('imageflow:tint_label', array('tint' => $preferences_default['tint']))
			, 'tint', $value)
		. fin_cadre_relief(true)		
		;
		
	// boite de selection de slider
	$page_result .= $sliders_result;
	
	// precharger les images ?
	$page_result .= ""
		. debut_cadre_relief(_DIR_IMAGEFLOW_IMAGES."preloader-24.png", true, "", _T('imageflow:preloader'))
		. imageflow_input_checkbox (
			_T('imageflow:preloader_label')
			, 'preloader', 'oui', ($preferences_current['preloader'] == 'oui'))
		. fin_cadre_relief(true)		
		;
	

	// fin formulaire
	$page_result .= ""

		. imageflow_form_bouton_valider('btn_valider_imageflow')
		. fin_cadre_trait_couleur(true)
		. imageflow_form_fin_form()

		;
		
	// Fin de la page
	echo($page_result);
	echo imageflow_html_signature(_IMAGEFLOW_PREFIX), fin_gauche(), fin_page();
}

?>