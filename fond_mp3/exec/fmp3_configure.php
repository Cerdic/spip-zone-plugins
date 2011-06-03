<?php

// exec/fmp3_configure.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of Fmp3.
	
	Fmp3 is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	Fmp3 is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with Fmp3; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de Fmp3. 
	
	Fmp3 est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	Fmp3 est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de details. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en même temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.
	
	*****************************************************/
	
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/fmp3_api_globales');
include_spip('inc/fmp3_api_prive');
include_spip('inc/fmp3_api_journal');

/**
 * Page de configuration du plugin
 */
function exec_fmp3_configure () {

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		;

	// la configuration est réservée aux admins tt rubriques
	$autoriser = ($connect_statut == "0minirezo") && $connect_toutes_rubriques;

	if($autoriser) {
			
		$preferences_default = unserialize(_FMP3_PREFERENCES_DEFAULT);
		$preferences_meta = fmp3_get_all_preferences();
		$preferences_current = array();
		$retour_formulaire = _request('btn_valider_fmp3');
		
		/*
		 * récupère le résultat du formulaire (si retour de ... formulaire)
		 * */
		foreach(array_keys($preferences_default) as $key) {
			// si non transmise par le formulaire, prendre celle enregistree
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
		}
		// vérifier les couleurs
		foreach(array('backColor', 'frontColor') as $key) {
			$val = $preferences_current[$key];
			$val = ltrim($val, "0x");
			$val = ltrim($val, "#");
			$val = strtolower($val);
			$preferences_current[$key] =
				((strlen($val) != 6) || !preg_match("/[0-9a-f]{6}/", $val))
				? $preferences_default[$key]
				: $val
				;
		}
		if ($retour_formulaire) {
			// enregistre les valeurs validées dans spip_meta
			fmp3_set_all_preferences($preferences_current);
		}
	}
	
////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = _T('fmp3:portfolio_fmp3');
	// Permet entre autres d'ajouter les classes à la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = "configuration";
	$sous_rubrique = _FMP3_PREFIX;

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page($titre_page, $rubrique, $sous_rubrique));

	if(!$autoriser) {
		die (fmp3_terminer_page_non_autorisee() . fin_page());
	}

	$page_result = ""
		. fmp3_gros_titre(_T('titre_page_config_contenu'), '', true)
		. barre_onglets($rubrique, _FMP3_PREFIX)
		. debut_gauche($rubrique, true)
		. fmp3_boite_plugin_info(_FMP3_PREFIX)
		. creer_colonne_droite($rubrique, true)
		. fmp3_boite_aide_info(true)
		. "<br />"
		. fmp3_raccourci_journal()
		. debut_droite($rubrique, true)
		;
	
	
	// affiche milieu
	// début formulaire
	$page_result .= ""
		. debut_cadre_trait_couleur("administration-24.gif", true, "", _T('fmp3:configuration_fmp3'))
		. fmp3_form_debut_form('fmp3_configure')
		;
	
	// 'autoStart' => "false" // toggle for autostarting the mp3 > true or false
	$page_result .= ""
		. fmp3_input_checkbox (_T('fmp3:autoStart_label')
			, 'autoStart', "true"
			, $preferences_current['autoStart'] == "true", $stylefml)
		;

	// 'repeatPlay' => "false" // toggle for repeating the mp3 > true or false
	$page_result .= ""
		. fmp3_input_checkbox (_T('fmp3:repeatPlay_label')
			, 'repeatPlay', "true"
			, $preferences_current['repeatPlay'] == "true", $stylefml)
		;

	// 'songVolume' => "90" // toggle for the volume of the song > 0 to 100
	$page_result .= ""
		. fmp3_input_value (_T('fmp3:songVolume_label')." "._T('fmp3:par_defaut', array('default' => $preferences_default['songVolume']))
			, 'songVolume', $preferences_current['songVolume'], $stylefml)
		;

	// 'backColor' => "0xeeeeee" // toggle for the backgroundcolor of the player > hex code
	$page_result .= ""
		. fmp3_input_value (_T('fmp3:backColor_label')." "._T('fmp3:par_defaut', array('default' => $preferences_default['backColor']))
			, 'backColor', $preferences_current['backColor'], $stylefml)
		;

	// 'frontColor' => "0x333333" // toggle for the backgroundcolor of the player > hex code
	$page_result .= ""
		. fmp3_input_value (_T('fmp3:frontColor_label')." "._T('fmp3:par_defaut', array('default' => $preferences_default['frontColor']))
			, 'frontColor', $preferences_current['frontColor'], $stylefml)
		;

	// 'heritage' => "true" 
	$page_result .= ""
		. fmp3_input_checkbox (_T('fmp3:inherit_label')
			, 'inherit', "true"
			, $preferences_current['inherit'] == "true", $stylefml)
		;

	// fin formulaire
	$page_result .= ""
		. fmp3_form_bouton_valider('btn_valider_fmp3', "margin:1em 0;")
		. fmp3_form_fin_form()
		. fin_cadre_trait_couleur(true)
		;
		
	// Fin de la page
	echo($page_result);
	echo fmp3_html_signature(_FMP3_PREFIX), fin_gauche(), fin_page();
}

?>