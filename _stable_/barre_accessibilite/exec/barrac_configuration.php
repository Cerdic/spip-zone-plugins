<?php 

	// exec/barrac_configuration.php
	
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


function exec_barrac_configuration () {

spip_log("## exec_barrac_configuration() --", _BARRAC_PREFIX);

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
	include_spip('inc/barrac_api_presentation');
	include_spip('inc/barrac_api_icones');
	
	if (!(($connect_statut == '0minirezo') && $connect_toutes_rubriques)) {
		echo _T('avis_non_acces_page');
		echo fin_gauche(), fin_page();
		exit;
	}
	
	$barrac_default_values_array = unserialize(_BARRAC_DEFAULT_VALUES_ARRAY);
	
	$barrac_boutons_parents = unserialize(_BARRAC_BOUTONS_PARENTS);
	
	////////////////////////////////////
	// initialise les variables postées par le formulaire
	foreach(array_merge(
		array('btn_valider_configure')
		, array_keys($barrac_default_values_array)
		) as $key) {
		$$key = _request($key);
	}
	
	$rubrique = "configuration";
	
	$message_gauche = $message_erreur = "";
	
	////////////////////////////////////
	// valider la configuration demandée
	if($btn_valider_configure) {
		$config = array();
		foreach($barrac_default_values_array as $key => $value) {
			if(isset($$key)) {
				$config[$key] = $$key;
			}
			else {
				if(($value == 'oui')) {
					// si radio non coché, dévalider l'option
					$config[$key] = 'non';
				}
			}
		}

		$config['barrac_pointeur_ancre'] = "#".trim($barrac_pointeur_ancre, "#");
		
		// verifier si les fichiers css indiqués sont présents
		foreach(
			array(
				'barrac_grossir_global' => 'barrac_grossir_cssfile'
				, 'barrac_espacer_global' => 'barrac_espacer_cssfile'
				, 'barrac_encadrer_global' => 'barrac_encadrer_cssfile'
				, 'barrac_inverser_global' => 'barrac_inverser_cssfile'
				) as $opt_globale => $cssfile) {
			if(!empty($$cssfile)) {
				$$cssfile = preg_replace("/[^a-z0-9_\-.]/Ui", "", basename($$cssfile));
				if(!empty($$cssfile)) {
					$config[$cssfile] = (find_in_path($$cssfile)) ? $$cssfile : "";
				} 
			}
			if(empty($config[$cssfile])) {
				$config[$opt_globale] = "oui";	
			}
		}
		
		__plugin_ecrire_key_in_serialized_meta('config', $config, _BARRAC_META_PREFERENCES);
		__ecrire_metas();
		// vide le cache (est-ce la meilleure solution ? la barre est recalculée également en js)
		include_spip('inc/invalideur');
		suivre_invalideur(1);
	}

	// recharge la config
	$config = __plugin_lire_key_in_serialized_meta('config', _BARRAC_META_PREFERENCES);
	foreach($config as $key=>$value) {
		$$key = $value;
	}

	////////////////////////////////////
	// fin traitements

	$commencer_page = charger_fonction('commencer_page', 'inc');


////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////
	
	$page_result = ""
		. $commencer_page(_T(_BARRAC_LANG."configuration_barre_accessibilite"), _BARRAC_PREFIX)
		. "<br /><br /><br />\n"
		. gros_titre(_T(_BARRAC_LANG."configuration_barre_accessibilite"), "", false)
		. barre_onglets($rubrique, _BARRAC_PREFIX)
		. debut_gauche($rubrique, true)
		. __plugin_boite_meta_info(_BARRAC_PREFIX, true)
		. $message_gauche
		. creer_colonne_droite($rubrique, true)
		. debut_droite($rubrique, true)
		;

	////////////////////////////////////
	// Boite des réglages
	$page_result .= ""
		. debut_cadre_trait_couleur(_DIR_PLUGIN_BARRAC_IMG_PACK."administration-24.png", true, "", _T(_BARRAC_LANG."configurer_barrac"))
		. barrac_form_description_champ('configurer_barrac_desc')
		. $message_erreur
		. "<div  style='text-align: $spip_lang_left;' class='verdana2'>\n"

		// début formulaire
		. "<form name='form_configuration' id='form_configuration' method='post' action=''>\n"
		;

	//=================================
	// Familles des styles disponibles
	// Si une seule famille, l'impose, sinon propose liste
	$barrac_familles = barrac_familles_array();
	if(!$barrac_familles) {
		$page_result .= "<div style='margin-top:0.5em;'>".__boite_alerte (_T(_BARRAC_LANG."erreur_installation")."</div>\n", true);
	} 
	else {
		$page_result .= ""
			. "<fieldset class='text-center'>\n"
			. barrac_form_fieldset_legend("configurer_familles")
			. "<div>"
			;
		if($ii = count($barrac_familles)) {
			if($ii == 1) {
				$page_result .= _T(_BARRAC_LANG."configurer_familles_defaut");
			}
			else {
				$page_result .= ""
					. "<label for='id_famille'>"._T(_BARRAC_LANG."configurer_familles_choisir")."</label>\n"
					. "<select name='barrac_famille_boutons' id='id_famille' class='fondo'>\n"
					;
				foreach($barrac_familles as $key) {
					$page_result .= "<option value='$key'".(($key == $barrac_famille_boutons) ? " selected='selected'" : "").">$key</option>\n";
				}
				$page_result .= ""
					. "</select>\n"
					;
			}
		}
		$page_result .= ""
			. "</div>"
			. "</fieldset>\n"
			;
	} // end else


	//=================================
	// Sélection des boutons actifs
	$page_result .= ""
		. "<fieldset class='text-center'>\n"
		. barrac_form_fieldset_legend("configurer_activation_boutons")
		. "<ul class='barrac-boutons-actifs'>\n"
		;
		
	$icone_fond = barrac_icone_fond(24);

	foreach(barrac_icones_array(false, 24) as $key=>$value) {
	
		// pas les boutons frères
		if($value['display']=='none') {
			continue;
		}
		
		$titre = $value['titre'];
		$titre_alt = _T(_BARRAC_LANG."configurer_activation_bouton").$titre;
		$icone = $value['icone'];
		$style = "background-image: url($icone);";
		$checked = (!$$key || ($$key == "oui")) ? "checked='checked'" : "";
		$page_result .= "<li class='item'>" 
			. "<div class='input'>\n"
			. "<input name='$key' type='checkbox' id='id_$key' value='oui' title=\"$titre_alt\" $checked />"
			. "</div>\n"
			. "<div class='label' style='background-image: url($icone_fond);'>\n"
			. "<label for='id_$key' style='$style' title=\"$titre\"><span>$titre_alt</span>&nbsp;"
			. "</label>"
			. "</div>\n"
			. "</li>\n"
			;
	}
	$page_result .= ""
		. "</ul>\n"
		. "</fieldset>\n"
		;

	//=================================
	// Position de la barre
	$checked = unserialize(_BARRAC_POSITIONS_ARRAY);
	$barrac_position_barre = (
		(array_key_exists($barrac_position_barre, $checked))
		? $barrac_position_barre
		: _BARRAC_POSITION_DEFAULT
		);
	foreach($checked as $key=>$value) {
		$checked[$key] = "";
	}
	$checked[$barrac_position_barre] = "checked='checked' ";
	$page_result .= ""
		. "<fieldset class='text-center' id='fieldset_configurer_position_boutons'>\n"
		. barrac_form_fieldset_legend("configurer_position_boutons")
		. "<div class='barrac-table-ecran'>\n"
			. "<div class='row'>\n" // rangée #1
				. "<div class='cell block-left'>\n"
					. "<label for='position_tl'>"._T(_BARRAC_LANG."configurer_position_tl")."</label>\n"
					. "<input type='radio' name='barrac_position_barre' value='top_left' id='position_tl' title=\""._T(_BARRAC_LANG."configurer_position_tl")."\" ".$checked['top_left']."/>\n"
				. "</div>\n"
				. "<div class='cell text-right'>\n"
					. "<label for='position_tr'>"._T(_BARRAC_LANG."configurer_position_tr")."</label>\n"
					. "<input type='radio' name='barrac_position_barre' value='top_right' id='position_tr' title=\""._T(_BARRAC_LANG."configurer_position_tr")."\" ".$checked['top_right']."/>\n"
				. "</div>\n"
			. "</div>\n" // #1
			. "<div class='row'>\n" // rangée #2
				. "<div class='cell block-left text-bottom'>\n"
					. "<label for='position_bl'>"._T(_BARRAC_LANG."configurer_position_bl")."</label>\n"
					. "<input type='radio' name='barrac_position_barre' value='bottom_left' id='position_bl' title=\""._T(_BARRAC_LANG."configurer_position_bl")."\" ".$checked['bottom_left']."/>\n"
				. "</div>\n"
				. "<div class='cell text-bottom text-right'>\n"
					. "<label for='position_br'>"._T(_BARRAC_LANG."configurer_position_br")."</label>\n"
					. "<input type='radio' name='barrac_position_barre' value='bottom_right' id='position_br' title=\""._T(_BARRAC_LANG."configurer_position_br")."\" ".$checked['bottom_right']."/>\n"
				. "</div>\n"
			. "</div>\n" // #2
		. "</div>\n"
		. "</fieldset>\n"
		;

	//=================================
	// position absolute ou fixed
	$page_result .= ""
		. "<fieldset class='text-center' id='fieldset_configurer_flip_boutons'>\n"
		. barrac_form_fieldset_legend("configurer_position_fixed")
		. barrac_form_description_champ('configurer_position_fixed_desc')
		. barrac_form_checkbox_button ('barrac_position_fixed', "configurer_position_fixer", ($barrac_position_fixed == 'oui'), 'oui', true)
		. "</fieldset>\n"
		;	

	//=================================
	// Adapter les boutons (flip h ou v suivant la position écran)
	// et direction de la flèche (pointer)
	$page_result .= ""
		. "<fieldset class='text-center' id='fieldset_configurer_flip_boutons'>\n"
		. barrac_form_fieldset_legend("configurer_flip_boutons")
		. barrac_form_checkbox_button ('barrac_flip_pointer', "configurer_flip_pointer", ($barrac_flip_pointer == 'oui'), 'oui', true)
		. barrac_form_checkbox_button ('barrac_flip_horizontal', "configurer_flip_horizontal", ($barrac_flip_horizontal == 'oui'), 'oui', true)
		. barrac_form_checkbox_button ('barrac_flip_vertical', "configurer_flip_vertical", ($barrac_flip_vertical == 'oui'), 'oui', true)
		. barrac_form_checkbox_button ('barrac_flip_contextuel', "configurer_flip_contextuel", ($barrac_flip_contextuel == 'oui'), 'oui', true)
		. "</fieldset>\n"
		;	

	//=================================
	// Présentation de la barre (horizontal ou vertical)
	$checked = array(_BARRAC_PRESENTATION_HORIZONTAL => "", _BARRAC_PRESENTATION_VERTICAL => "");
	$key = (
		($barrac_presentation_barre && in_array($barrac_presentation_barre, array_keys($checked)))
		? $barrac_presentation_barre
		: _BARRAC_PRESENTATION_DEFAULT
		);
	$checked[$key] = "checked='checked' ";
	$page_result .= ""
		. "<fieldset class='barrac-2options text-center' id='fieldset_configurer_presentation_boutons'>\n"
		. barrac_form_fieldset_legend("configurer_presentation_boutons")
		. "<div class='presentations'>\n"
			. "<div class='presentation-h'>\n"
				. "<label for='presentation_h'><span class='label'>"._T(_BARRAC_LANG."configurer_presenter_h")."</span>\n"
				. "<span class='input'>"
					. "<input type='radio' name='barrac_presentation_barre' value='"._BARRAC_PRESENTATION_HORIZONTAL."' id='presentation_h'"
						. " title=\""._T(_BARRAC_LANG."configurer_presenter_h")."\" ".$checked[_BARRAC_PRESENTATION_HORIZONTAL]." />\n"
				. "</span>\n"
				. "<span class='icon'>"."</span></label>\n"
			. "</div>\n"
			. "<div class='presentation-v'>\n"
				. "<label for='presentation_v'><span class='label'>"._T(_BARRAC_LANG."configurer_presenter_v")."</span>\n"
				. "<span class='input'>"
					. "<input type='radio' name='barrac_presentation_barre' value='"._BARRAC_PRESENTATION_VERTICAL."' id='presentation_v'"
						. " title=\""._T(_BARRAC_LANG."configurer_presenter_v")."\" ".$checked[_BARRAC_PRESENTATION_VERTICAL]." />\n"
				. "</span>\n"
				. "<span class='icon'>"."</span></label>\n"
			. "</div>\n"
		. "</div>\n"
		. "</fieldset>\n"
		;	

	//=================================
	// Taille des icones et Marge entre les icones
	$marge = 
		($marge && ($marge <= _BARRAC_ICONE_MARGE_MAX))
		? $marge
		: _BARRAC_ICONE_MARGE_DEFAULT
		;
	$page_result .= ""
		. "<fieldset class='text-center' id='fieldset_configurer_espace_taille_boutons'>\n"
		. barrac_form_fieldset_legend("configurer_espace_taille_boutons")
		. "<div>"
		. "<label for='marge'>"._T(_BARRAC_LANG."configurer_espace_boutons")."</label>"
		. barrac_form_select_fondo ('marge', 'barrac_marge_entre_boutons', 1, _BARRAC_ICONE_MARGE_MAX, "+1", $barrac_marge_entre_boutons)
		. "</div>"
		;
	$taille = 
		($taille && ($taille <= _BARRAC_ICONE_TAILLE_MAX))
		? $taille
		: _BARRAC_ICONE_TAILLE_DEFAULT
		;
	$page_result .= ""
		. "<div style='margin-top:0.5em;'>"
		. "<label for='taille'>"._T(_BARRAC_LANG."configurer_taille_boutons")."</label>"
		. barrac_form_select_fondo ('taille', 'barrac_taille_bouton', 12, _BARRAC_ICONE_TAILLE_MAX, "*2", $barrac_taille_bouton)
		. "</div>"
		. "</fieldset>\n"
		;
	
	//=================================
	// plusieurs boutons valider pour éviter le mal d'ascenseur
	$page_result .= ""
			. barrac_form_bouton_valider ('btn_valider_configure')
			;

	//=================================
	// texte d'intro sur les réglages
	$page_result .= ""
		. "<div style='margin-top:1em;'>\n"
		. debut_cadre_trait_couleur('', true, '', '')
		. barrac_form_description_champ('configurer_options_desc', false)
		. fin_cadre_trait_couleur(true)
		. "</div>"
		;
					
	//=================================
	$page_result .= ""
		// pointeur de contenu
		. "<div id='bloc_barrac_pointer'>\n"
		. debut_cadre_relief(_DIR_PLUGIN_BARRAC_IMG_PACK."barrac_pointer-24.png", true, "", _T(_BARRAC_LANG."configurer_pointeur"))
		. barrac_form_description_champ('configurer_pointeur_desc', false)
		. barrac_form_input_text('barrac_pointeur_ancre', 'configurer_pointeur_ancre', $barrac_pointeur_ancre)
		. fin_cadre_relief(true)
		. "</div>\n"
		;

	//=================================
	// taille texte
	$page_result .= ""
		. "<div id='bloc_barrac_grossir'>\n"
		. debut_cadre_relief(_DIR_PLUGIN_BARRAC_IMG_PACK."barrac_grossir-24.png", true, "", _T(_BARRAC_LANG."configurer_grossir"))
		. barrac_form_description_champ ('configurer_grossir_desc', 'configurer_selectionner_css')
		. barrac_form_radio_button ('barrac_grossir_global', 'configurer_grossir_global', ($barrac_grossir_global == 'oui'), 'oui'
			,
			 "<label for='grossir_taille' style='display:none;'>"._T(_BARRAC_LANG."configurer_grossir_global").":</label>"
			. barrac_form_select_fondo ('grossir_taille', 'barrac_grossir_taille', 200, _BARRAC_GROSSIR_TAILLE_MAX, "+100", $barrac_grossir_taille)
			)
		. barrac_form_radio_button ('barrac_grossir_global', 'configurer_indiquez_fichier', ($barrac_grossir_global == 'non'), 'non')
		. barrac_form_input_text('barrac_grossir_cssfile', 'configurer_indiquez_fichier', $barrac_grossir_cssfile)
		. fin_cadre_relief(true)
		. "</div>\n"
		;

	//=================================
	// espacement liens
	$page_result .= ""
		. "<div id='bloc_barrac_espacer'>\n"
		. debut_cadre_relief(_DIR_PLUGIN_BARRAC_IMG_PACK."barrac_espacer-24.png", true, "", _T(_BARRAC_LANG."configurer_espacer"))
		. barrac_form_description_champ ('configurer_espacer_desc', 'configurer_selectionner_css')
		. barrac_form_radio_button ('barrac_espacer_global', 'configurer_espacer_global', ($barrac_espacer_global == 'oui'), 'oui')
		. barrac_form_radio_button ('barrac_espacer_global', 'configurer_indiquez_fichier', ($barrac_espacer_global == 'non'), 'non')
		. barrac_form_input_text('barrac_espacer_cssfile', 'configurer_indiquez_fichier', $barrac_espacer_cssfile)
		. fin_cadre_relief(true)
		. "</div>\n"
		;

	//=================================
	// encadrement paragraphes
	$page_result .= ""
		. "<div id='bloc_barrac_encadrer'>\n"
		. debut_cadre_relief (_DIR_PLUGIN_BARRAC_IMG_PACK."barrac_encadrer-24.png", true, "", _T(_BARRAC_LANG."configurer_encadrer"))
		. barrac_form_description_champ ('configurer_encadrer_desc', 'configurer_selectionner_css')
		. barrac_form_radio_button ('barrac_encadrer_global', 'configurer_encadrer_global', ($barrac_encadrer_global == 'oui'), 'oui')
		. barrac_form_radio_button ('barrac_encadrer_global', 'configurer_indiquez_fichier', ($barrac_encadrer_global == 'non'), 'non')
		. barrac_form_input_text ('barrac_encadrer_cssfile', 'configurer_indiquez_fichier', $barrac_encadrer_cssfile)
		. fin_cadre_relief(true)
		. "</div>\n"
		;

	//=================================
	// inverser couleur texte
	$page_result .= ""
		. "<div id='bloc_barrac_inverser'>\n"
		. debut_cadre_relief (_DIR_PLUGIN_BARRAC_IMG_PACK."barrac_inverser-24.png", true, "", _T(_BARRAC_LANG."configurer_inverser"))
		. barrac_form_description_champ ('configurer_inverser_desc')
		. barrac_form_radio_button ('barrac_inverser_global', 'configurer_inverser_global', ($barrac_inverser_global == 'oui'), 'oui')
		. barrac_form_radio_button ('barrac_inverser_global', 'configurer_indiquez_fichier', ($barrac_inverser_global == 'non'), 'non')
		. barrac_form_input_text ('barrac_inverser_cssfile', 'configurer_indiquez_fichier', $barrac_inverser_cssfile)
		. fin_cadre_relief (true)
		. "</div>\n"
		;

	//=================================
	// validation, fin de formulaire
	// plusieurs boutons valider pour éviter le mal d'ascenseur
	$page_result .= ""
		. barrac_form_bouton_valider ('btn_valider_configure')
		;

	$page_result .= ""
		. barrac_form_fin_form ()
		. fin_cadre_trait_couleur(true)
		;
	////////////////////////////////////
	// Boite des réglages :: FIN

	echo($page_result);
	echo __plugin_html_signature(_BARRAC_PREFIX, true, true, true), fin_gauche(), fin_page();
	
	return(true);

} // end exec_barrac_configuration ()


////////////////////////////////////
// FONCTIONS
////////////////////////////////////


// barrac_familles_array ()
/**
/*	vérifie les familles et renvoie la liste des familles 
/*	qui sont correctement composées des 10 icones
*/
function barrac_familles_array () {
	$result = false;
	if($dh = opendir(_DIR_PLUGIN_BARRAC_IMG_PACK)) {
		$familles = array();
		while (($file = readdir($dh)) !== false) {
			if(!is_dir($file)) {
				//spip_log($file);
				if(preg_match('/^([a-z_]+)-([a-z]+)-([0-9]+)\.png$/', $file, $matches)) {
					$familles[$matches[1]][] = $matches[2];
				}
			}
		}
		closedir($dh);
		if(count($familles)) {
			$result = array();
			$target_array = array(
				_BARRAC_ACTION_POINTER,_BARRAC_ACTION_GROSSIR,_BARRAC_ACTION_REDUIRE
				,_BARRAC_ACTION_ESPACER,_BARRAC_ACTION_RAPPROCHER,_BARRAC_ACTION_ENCADRER
				,_BARRAC_ACTION_DECADRER,_BARRAC_ACTION_INVERSER,_BARRAC_ACTION_REPLACER 
				,_BARRAC_ACTION_FOND
			);
			sort($target_array);
			$targe_count = count($target_array);
			foreach($familles as $key => $array) {
				if(count($array) == $targe_count) {
					sort($array);
					if(!count(array_diff($target_array, $array))) {
						$result[] = $key;
					}
				}
			}
		}
		if(!count($result)) $result = false;
	}
	return($result);
	
} // end barrac_familles_array()


// barrac_form_select_fondo ()
function barrac_form_select_fondo ($id, $name, $start, $end, $incr, $selected) {
	$page_result = ""
		. "<select class='fondo' name='$name' id='$id'>\n"
		;
		$ii = 1;
		for($ii = $start; $ii <= $end; eval("\$ii = $ii $incr;")) {
	      $page_result .= "<option class='verdana2' value='$ii'".(($selected==$ii) ? " selected='selected'" : "").">$ii</option>\n";
		}
	$page_result .= ""
		. "</select>\n"
		;
	
	return($page_result);

} // end barrac_form_select()

// barrac_form_description_champ ()
function barrac_form_description_champ ($message, $message_suite = false) {
	
	global $spip_lang_left;
	
	$page_result = ""
		. "<div  style='text-align: $spip_lang_left;font-style: italic;' class='verdana2'>\n"
		. _T(_BARRAC_LANG.$message)
		. "</div>\n"
		;
	if($message_suite && is_string($message_suite)) {
		$page_result .= barrac_form_description_champ ($message_suite);
	}
	return($page_result);
}

// barrac_form_bouton_valider ()
function barrac_form_bouton_valider ($nom_bouton) {
	global $spip_lang_right;
	static $id_valider = 1;
	$page_result = ""
		. "<div style='text-align:$spip_lang_right;margin:0.75em 0 0;'>\n"
		. "<label for='$nom_bouton"."_"."$id_valider' style='display:none;'>"
		.  _T(_BARRAC_LANG."configurer_valider_desc")
		. "</label>\n"
		. "<input type='submit' name='$nom_bouton' id='$nom_bouton"."_"."$id_valider' value='"._T('bouton_valider')."' class='fondo' />\n"
		. "</div>\n"
		;
	$id_valider++;
	return($page_result);
}

// barrac_form_radio_button ()
function barrac_form_radio_button ($nom_radio, $label_radio, $checked, $value, $complement_bouton = "") {
	global $spip_lang_left;
	static $id = 1;
	$page_result = ""
		. "<div style='text-align:$spip_lang_left;margin:0.5em 0 0;'>\n"
		. "<input type='radio' name='$nom_radio' value='$value' id='".$nom_radio."_$id'".($checked ? " checked='checked'" : "")." />\n"
		. "<label class='verdana2' for='".$nom_radio."_$id'>"._T(_BARRAC_LANG.$label_radio)."</label>\n"
		. $complement_bouton
		. "</div>\n"
		;
	$id++;
	return($page_result);
}

// barrac_form_checkbox_button ()
function barrac_form_checkbox_button ($nom_checkbox, $label_checkbox, $checked, $value, $div) {
	global $spip_lang_left;
	$page_result = ""
		. ($div ? "<div style='text-align: $spip_lang_left;' class='verdana2'>" : "")
		. "<input name='$nom_checkbox' type='checkbox' id='$nom_checkbox' value='$value'". ($checked ? " checked='checked'" : "")." />\n"
		. "<label for='$nom_checkbox'>"._T(_BARRAC_LANG.$label_checkbox)."</label>\n"
		. ($div ? "</div>" : "")
		;
	return($page_result);
}

// barrac_form_input_text ()
function barrac_form_input_text ($nom_champ, $label_champ, $value) {
	$page_result = ""
		. "<label for='$nom_champ' style='display:none;'>"._T(_BARRAC_LANG.$label_champ)."</label>\n"
		. "<input type='text' name='$nom_champ' id='$nom_champ' size='40' class='forml' style='margin-top:0.5em;' value=\"".$value."\" />\n"
		;
	return($page_result);
}

// barrac_form_debut_form ()
function barrac_form_debut_form ($nom_form, $ancre) {
	global $spip_lang_left;
	$page_result = ""
		. "<div style='text-align: $spip_lang_left;' class='verdana2'>\n"
		. "<form name='$nom_form' id='$nom_form' method='post' action='".$_SERVER['REQUEST_URI']."#$ancre'>\n"
		;
	return($page_result);
}

function barrac_form_fieldset_legend ($legend) {
	if(empty($legend)) return("");
	return("<legend>"._T(_BARRAC_LANG.$legend)."</legend>\n");
}

// barrac_form_fin_form ()
function barrac_form_fin_form () {
	$page_result = ""
		. "</form>\n"
		. "</div>\n"
		;
	return($page_result);
}

?>