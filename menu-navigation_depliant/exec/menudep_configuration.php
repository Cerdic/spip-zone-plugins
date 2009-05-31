<?php 

	// exec/menudep_configuration.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2007 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of Menudep.
	
	Menudep is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	Menudep is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with Menudep; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de Menudep. 
	
	Menudep est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publie'e par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	Menudep est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but specifique. Reportez-vous a' la Licence Publique Generale GNU 
	pour plus de details. 
	
	Vous devez avoir recu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a' la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.
	
	*****************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;


function exec_menudep_configuration () {

	global $connect_statut
	, $connect_toutes_rubriques
	, $connect_id_auteur
	, $connect_id_rubrique
	, $spip_lang_left
	, $spip_lang_right
	;

	include_spip('inc/presentation');
	include_spip('inc/meta');
	include_spip('inc/urls');
	include_spip('inc/utils');
	include_spip('inc/acces');
	include_spip('inc/plugin_globales_lib');
	include_spip('inc/menudep_api_presentation');
	
	if (!(($connect_statut == '0minirezo') && $connect_toutes_rubriques)) {
		echo _T('avis_non_acces_page');
		echo fin_gauche(), fin_page();
		exit;
	}
	
	$menudep_values_array = unserialize(_MENUDEP_DEFAULT_VALUES_ARRAY);
	
	////////////////////////////////////
	// initialise les variables postees par le formulaire
	foreach(array_merge(
		array(
			'btn_valider_configure'
		)
		, array_keys($menudep_values_array)
		) as $key) {
		$$key = _request($key);
	}
	
	$vider_cache = _request('vider_cache');
	
	$rubrique = "configuration";
	
	$message_gauche = $message_erreur = "";
	
	$taille_cache = spip_fetch_array(spip_query("SELECT SUM(taille) AS n FROM spip_caches WHERE type='t'"));
	$message_gauche = 
		($taille_cache = $taille_cache['n']) 
		? _T('taille_cache_octets', array('octets' => taille_en_octets($taille_cache)))
		: _T('taille_cache_vide')
		;

	 
	
	////////////////////////////////////
	// valider la configuration
	if($btn_valider_configure) {
		$config = array();
		// completer les checkbox manquantes
		foreach($menudep_values_array as $key => $value) {
			if(($value == 'oui') && !isset($$key)) {
				$$key = 'non';
			}
		}
		// initialiser la config
		foreach($menudep_values_array as $key => $value) {
			$config[$key] = (isset($$key) && !empty($$key)) ? $$key	: $menudep_values_array[$key];
		}
	
		__plugin_ecrire_key_in_serialized_meta('config', $config, _MENUDEP_META_PREFERENCES);
		__ecrire_metas();
		
		if($vider_cache == 'oui') {
			include_spip('inc/invalideur');
			suivre_invalideur(1);
			$message_gauche = _T('taille_cache_vide');
			$taille_cache = 0;
		}
	}
	
	// mettre a' jour les variables locales
	$config = __plugin_lire_key_in_serialized_meta('config', _MENUDEP_META_PREFERENCES);
	foreach($config as $key=>$value) {
		$$key = $value;
	}

	// les valeurs obligatoires par defaut
	foreach($menudep_values_array as $key => $value) {
		if(!$$key || empty($$key)) $$key = $value;
	}
	
	if($message_gauche) { $message_gauche = "<p class='verdana2'>".$message_gauche."</p>\n"; }

	////////////////////////////////////
	// fin traitements

	$commencer_page = charger_fonction('commencer_page', 'inc');


////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////
	
	$page_result = ""
		. $commencer_page(_T(_MENUDEP_LANG."configuration_menu_depliant"), _MENUDEP_PREFIX)
		. "<br /><br /><br />\n"
		. gros_titre(_T(_MENUDEP_LANG."configuration_menu_depliant"), "", false)
		. barre_onglets($rubrique, _MENUDEP_PREFIX)
		. debut_gauche($rubrique, true)
		. __plugin_boite_meta_info(_MENUDEP_PREFIX, true)
		. $message_gauche
		. creer_colonne_droite($rubrique, true)
		. debut_droite($rubrique, true)
		;

	////////////////////////////////////
	// Boite principale des reglages
	$page_result .= ""
		. debut_cadre_trait_couleur(_DIR_PLUGIN_MENUDEP_IMG_PACK."administration-24.png", true, "", _T(_MENUDEP_LANG."configuration_menu_depliant"))
		. "<div  style='text-align: $spip_lang_left;font-style: italic;' class='verdana2'>\n"
		. _T(_MENUDEP_LANG."configuration_menu_depliant_desc")
		. "</div>\n"
		. $message_erreur
		. menudep_form_debut_form('form_configuration')
		;
	//
	// selecteurs
	$page_result .= ""
		. "<fieldset class='text-center'>\n"
		. "<legend>"._T(_MENUDEP_LANG."configurer_selecteurs")."</legend>\n"
		. "<p class='verdana2 description' style='margin-top:0;'>"._T(_MENUDEP_LANG."configurer_selecteurs_desc")."</p>\n"
		. menudep_form_input_text('menudep_id', "configurer_id", $menudep_id)
		. menudep_form_input_text('menudep_div', "configurer_div", $menudep_div)
		. menudep_form_input_text('menudep_a', "configurer_a", $menudep_a)
		. menudep_form_input_text('menudep_class', "configurer_class", $menudep_class)
		. "</fieldset>\n"
		;
	//
	// sous-menu flottant
	$page_result .= ""
		. "<fieldset class='text-center'>\n"
		. "<legend>"._T(_MENUDEP_LANG."configurer_absolute")."</legend>\n"
		. "<p class='verdana2 description' style='margin-top:0;'>"._T(_MENUDEP_LANG."configurer_absolute_desc")."</p>\n"
		. menudep_form_checkbox_button ('menudep_absolute', _T(_MENUDEP_LANG."configurer_absolute_activer")
			, ($menudep_absolute == 'oui'), 'oui', false)
		//
		. "<div id='menudep_absolute_pref_id' style='display:".(($menudep_absolute == 'oui') ? 'block' : 'none')."'>\n"
		. menudep_form_input_text('menudep_top', "configurer_top", $menudep_top)
		. menudep_form_input_text('menudep_left', "configurer_left", $menudep_left)
		. "<fieldset class='text-center'>\n"
		. "<legend>"._T(_MENUDEP_LANG."configurer_heriter")."</legend>\n"
		. menudep_form_checkbox_button ('menudep_heriter', _T(_MENUDEP_LANG."configurer_heriter_activer")
			, ($menudep_heriter == 'oui'), 'oui', false)
		. "<div id='menudep_heriter_pref_id' style='display:".(($menudep_heriter == 'oui') ? 'block' : 'none')."'>\n"
		. "<p class='verdana2 description' style='margin-top:1em;'>"._T(_MENUDEP_LANG."configurer_heriter_desc")."</p>\n"
		. menudep_form_input_text('menudep_bgcolor', "configurer_bgcolor", $menudep_bgcolor)
		. menudep_form_input_text('menudep_border', "configurer_border", $menudep_border)
		. menudep_form_input_text('menudep_zindex', "configurer_zindex", $menudep_zindex)
		. "</div>\n"
		. "</fieldset>\n"
		. "</div>\n"
		. "</fieldset>\n"
		;
	//
	// vitesse d'animation
	$page_result .= ""
		. "<fieldset class='text-center'>\n"
		. "<legend>"._T(_MENUDEP_LANG."configurer_vitesse")."</legend>\n"
		. "<p class='verdana2 description' style='margin-top:0;'>"._T(_MENUDEP_LANG."configurer_vitesse_desc")."</p>\n"
		. menudep_form_input_text('menudep_speedin', "configurer_vitesse_in", $menudep_speedin)
		. menudep_form_input_text('menudep_speedout', "configurer_vitesse_out", $menudep_speedout)
		. menudep_form_input_text('menudep_tempo', "configurer_tempo", $menudep_tempo)
		. "</fieldset>\n"
		;
	//
	// replier autres
	$page_result .= ""
		. "<fieldset class='text-center'>\n"
		. "<legend>"._T(_MENUDEP_LANG."configurer_replier")."</legend>\n"
		. "<p class='verdana2 description' style='margin-top:0;'>"._T(_MENUDEP_LANG."configurer_replier_desc")."</p>\n"
		. menudep_form_checkbox_button ('menudep_replier', _T(_MENUDEP_LANG."configurer_replier_activer")
			, ($menudep_replier == 'oui'), 'oui', true)
		. "<div id='menudep_reavant_id' style='display:".(($menudep_replier == 'oui') ? 'block' : 'none').";'>\n"
		. menudep_form_checkbox_button ('menudep_reavant', _T(_MENUDEP_LANG."configurer_reavant_activer")
			, ($menudep_reavant == 'oui'), 'oui', true)
		. "</div>\n"
		. "</fieldset>\n"
		;
	if($taille_cache > 0) {
		//
		// vider le cache
		$page_result .= ""
			. "<fieldset class='text-center'>\n"
			. "<legend>"._T("texte_vider_cache")."</legend>\n"
			. "<p class='verdana2 description' style='margin-top:0;'>"._T(_MENUDEP_LANG."configurer_vider_cache_desc")."</p>\n"
			. menudep_form_checkbox_button ('vider_cache', _T("texte_vider_cache")
				, (false), 'oui', true)
			. "</fieldset>\n"
			;
	}
	//
	// bouton valider
	$page_result .= ""
		. "<div style='text-align:$spip_lang_left;margin:0.75em 0 0;'>\n"
		. "<input type='submit' id='btn_valider_reinitialiser' name='btn_valider_reinitialiser' 
				style='display:block;float:left;' value='"._T(_MENUDEP_LANG."bouton_reinit")."' class='fondo' />\n"
		. "<div style='text-align:$spip_lang_right;margin:0;'>\n"
		. "<input type='reset' name='btn_valider_annule' value='"._T('bouton_annuler')."' class='fondo' />\n"
		. "<input type='submit' name='btn_valider_configure' value='"._T('bouton_valider')."' class='fondo' />\n"
		. "</div>\n"
		. "</div>\n"
		. menudep_form_fin_form()
		. fin_cadre_trait_couleur(true)
		;
	

	echo($page_result);
	echo __plugin_html_signature(_MENUDEP_PREFIX, true, true, true), fin_gauche(), fin_page();
	return(true);
}

/**********************************************
*/
function menudep_form_input_text ($nom_champ, $label_champ, $value) {
	$page_result = ""
		. "<label for='$nom_champ' style='display:block;margin-top:0.5em;'>"._T(_MENUDEP_LANG.$label_champ).":</label>\n"
		. "<input type='text' name='$nom_champ' id='$nom_champ' size='40' class='forml' style='margin-top:0.5em;' value=\"".$value."\" />\n"
		;
	return($page_result);
}

/**********************************************
*/
function menudep_form_debut_form ($nom_form, $ancre = '') {
	global $spip_lang_left;
	if(empty($ancre)) {
		$ancre = $nom_form;
	}
	$page_result = ""
		. "<div style='text-align: $spip_lang_left;' class='verdana2'>\n"
		. "<form name='$nom_form' id='$nom_form' method='post' action='".$_SERVER['REQUEST_URI']."#$ancre'>\n"
		;
	return($page_result);
}

/**********************************************
*/
function menudep_form_fin_form () {
	$page_result = ""
		. "</form>\n"
		. "</div>\n"
		;
	return($page_result);
}

 /**********************************************
*/
function menudep_form_checkbox_button ($nom_checkbox, $label_checkbox, $checked, $value, $div = false) {
	global $spip_lang_left;
	$page_result = ""
		. ($div ? "<div style='text-align: $spip_lang_left;' class='verdana2'>" : "")
		. "<input name='$nom_checkbox' type='checkbox' id='$nom_checkbox' value='$value'". ($checked ? " checked='checked'" : "")." />\n"
		. "<label for='$nom_checkbox'>".$label_checkbox."</label>\n"
		. ($div ? "</div>" : "")
		;
	return($page_result);
}


?>