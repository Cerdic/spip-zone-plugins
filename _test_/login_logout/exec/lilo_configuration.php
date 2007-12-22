<?php 

	// exec/lilo_configuration.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2007 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of LiLo.
	
	LiLo is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	LiLo is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with LiLo; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de LiLo. 
	
	LiLo est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	LiLo est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de détails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.
	
	*****************************************************/
	
	// page de configuration espace privé

if (!defined("_ECRIRE_INC_VERSION")) return;


function exec_lilo_configuration () {

	global $connect_statut
		, $connect_toutes_rubriques
		, $spip_lang_left
		, $spip_lang_right
		;

	include_spip('inc/presentation');
	include_spip('inc/meta');
	include_spip('inc/urls');
	include_spip('inc/utils');
	include_spip('inc/acces');
	include_spip('inc/plugin_globales_lib');
	
	if (!(($connect_statut == '0minirezo') && $connect_toutes_rubriques)) {
		echo _T('avis_non_acces_page');
		echo fin_gauche(), fin_page();
		exit;
	}
	
	$lilo_values_array = unserialize(_LILO_DEFAULT_VALUES_ARRAY);
	
	////////////////////////////////////
	// initialise les variables postées par le formulaire
	foreach(array_merge(
		array(
			'btn_valider_configure'
		)
		, array_keys($lilo_values_array)
		) as $key) {
		$$key = _request($key);
	}
	
	$rubrique = "configuration";
	
	$message_gauche = $message_erreur = "";
	 
	
	////////////////////////////////////
	// valider la configuration
	if($btn_valider_configure) {
		$config = array();
		// compléter les checkbox manquantes
		foreach($lilo_values_array as $key => $value) {
			if(($value == 'oui') && !isset($$key)) {
				// si radio non coché, valeur = 'non'
				$$key = 'non';
			}
		}
		// initialiser la config par défaut si besoin (installation non configurée)
		foreach($lilo_values_array as $key => $value) {
			$config[$key] = (isset($$key) && !empty($$key)) ? $$key	: $lilo_values_array[$key];
		}
	
		__plugin_ecrire_key_in_serialized_meta('config', $config, _LILO_META_PREFERENCES);
		__ecrire_metas();
		
	}
	
	// mettre à jour les variables locales
	$config = __plugin_lire_key_in_serialized_meta('config', _LILO_META_PREFERENCES);
	foreach($config as $key=>$value) {
		$$key = $value;
	}

	// les valeurs obligatoires par defaut
	foreach($lilo_values_array as $key => $value) {
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
		. $commencer_page(_T(_LILO_LANG."configuration_login_logout"), _LILO_PREFIX)
		. "<br /><br /><br />\n"
		. gros_titre(_T(_LILO_LANG."configuration_login_logout"), "", false)
		. barre_onglets($rubrique, _LILO_PREFIX)
		. debut_gauche($rubrique, true)
		. __plugin_boite_meta_info(_LILO_PREFIX, true)
		. $message_gauche
		. creer_colonne_droite($rubrique, true)
		. debut_droite($rubrique, true)
		;

	////////////////////////////////////
	// Boite principale des réglages
	$page_result .= ""
		. debut_cadre_trait_couleur(_DIR_PLUGIN_LILO_IMG_PACK."administration-24.png", true, "", _T(_LILO_LANG."configuration_login_logout"))
		. lilo_form_description('configuration_login_logout_desc')
		. $message_erreur
		. lilo_form_debut_form('form_configuration')
		;
	//
	// Ecran login (appelé pour accéder à l'espace privé, login.html) 
	$page_result .= ""
		. debut_cadre_relief(_DIR_PLUGIN_LILO_IMG_PACK."lilo-login-win-24.png", true, '', _T(_LILO_LANG."configurer_login_prive"))
		. lilo_form_description('configurer_login_prive_desc')
		. "<fieldset class='text-center'>\n"
		. lilo_form_legend('configurer_login_prive_voir_logo')
		. lilo_form_checkbox_button ('lilo_login_voir_logo', _T(_LILO_LANG."configurer_login_prive_voir_logo_desc")
			, ($lilo_login_voir_logo == 'oui'), 'oui', true)
		. "</fieldset>\n"
		//
		. "<fieldset class='text-center'>\n"
		. lilo_form_legend('configurer_login_voir_erreur')
		. lilo_form_checkbox_button ('lilo_login_voir_erreur', _T(_LILO_LANG."configurer_login_voir_erreur_desc")
			, ($lilo_login_voir_erreur == 'oui'), 'oui', true)
		. "</fieldset>\n"
		//
		. "<fieldset class='text-center'>\n"
		. lilo_form_legend('configurer_login_session_remember')
		. lilo_form_checkbox_button ('lilo_login_session_remember', _T(_LILO_LANG."configurer_login_session_remember_desc")
			, ($lilo_login_session_remember == 'oui'), 'oui', true)
		. "</fieldset>\n"
		//
		. fin_cadre_relief(true)
		;
	//
	// Boite de statut 
	$page_result .= ""
		. debut_cadre_relief(_DIR_PLUGIN_LILO_IMG_PACK."lilo-statut-24.png", true, '', _T(_LILO_LANG."configurer_statut"))
		. lilo_form_description('configurer_statut_desc')
		// position 
		. "<fieldset class='text-center'>\n"
		. lilo_form_legend('configurer_statut_position')
		. "<div class='lilo-screen'>"
		. "<ul>\n"
		. "<li class='tl'>".lilo_form_radio_button_position ('lilo_statut_position', 'lilo_statut_position_tl', $lilo_statut_position, 'tl')."</li>\n"
		. "<li class='tr'>".lilo_form_radio_button_position ('lilo_statut_position', 'lilo_statut_position_tr', $lilo_statut_position, 'tr')."</li>\n"
		. "<li class='bl'>".lilo_form_radio_button_position ('lilo_statut_position', 'lilo_statut_position_bl', $lilo_statut_position, 'bl')."</li>\n"
		. "<li class='br'>".lilo_form_radio_button_position ('lilo_statut_position', 'lilo_statut_position_br', $lilo_statut_position, 'br')."</li>\n"
		. "</ul>\n"
		. "</div>"
		. "</fieldset>\n"
		// boite flotante ou fixée
		. "<fieldset class='text-center'>\n"
		. lilo_form_legend('configurer_statut_fixed')
		. lilo_form_checkbox_button ('lilo_statut_fixed', _T(_LILO_LANG."configurer_statut_fixed_desc")
			, ($lilo_statut_fixed == 'oui'), 'oui', true)
		. "</fieldset>\n"
		// logo auteur
		. "<fieldset class='text-center'>\n"
		. lilo_form_legend('configurer_statut_voir_logo')
		. lilo_form_checkbox_button ('lilo_statut_voir_logo', _T(_LILO_LANG."configurer_statut_voir_logo_desc")
			, ($lilo_statut_voir_logo == 'oui'), 'oui', true)
		. "</fieldset>\n"
		// insérer boutons spip admin
		. "<fieldset class='text-center'>\n"
		. lilo_form_legend('configurer_statut_voir_btn_admins')
		. lilo_form_checkbox_button ('lilo_statut_voir_boutons_admins', _T(_LILO_LANG."configurer_statut_voir_btn_admins_desc")
			, ($lilo_statut_voir_boutons_admins == 'oui'), 'oui', true)
		. "</fieldset>\n"
		// transparence
		. "<fieldset class='text-center'>\n"
		. lilo_form_legend('configurer_statut_transparent')
		. lilo_form_checkbox_button ('lilo_statut_transparent', _T(_LILO_LANG."configurer_statut_transparent_desc")
			, ($lilo_statut_transparent == 'oui'), 'oui', true)
		. "</fieldset>\n"
		// couleur de fond
		. "<fieldset class='text-center'>\n"
		. lilo_form_legend('configurer_statut_bgcolor')
		. "<div><div style='float:left;margin-right:1ex;'>\n"
		. lilo_form_radio_button_color ('lilo_statut_bgcolor', "configurer_statut_couleur_000", ($lilo_statut_bgcolor == '000'), '000')
		. "</div><div style='float:left;margin-right:1ex;'>\n"
		. lilo_form_radio_button_color ('lilo_statut_bgcolor', "configurer_statut_couleur_f00", ($lilo_statut_bgcolor == 'f00'), 'f00')
		. "</div><div style='float:left;margin-right:1ex;'>\n"
		. lilo_form_radio_button_color ('lilo_statut_bgcolor', "configurer_statut_couleur_f0f", ($lilo_statut_bgcolor == 'f0f'), 'f0f')
		. "</div><div style='float:left;margin-right:1ex;'>\n"
		. lilo_form_radio_button_color ('lilo_statut_bgcolor', "configurer_statut_couleur_0c0", ($lilo_statut_bgcolor == '0c0'), '0c0')
		. "</div><div style='float:left;margin-right:1ex;'>\n"
		. lilo_form_radio_button_color ('lilo_statut_bgcolor', "configurer_statut_couleur_00f", ($lilo_statut_bgcolor == '00f'), '00f')
		. "</div><div style='float:left;margin-right:1ex;'>\n"
		. lilo_form_radio_button_color ('lilo_statut_bgcolor', "configurer_statut_couleur_666", ($lilo_statut_bgcolor == '666'), '666')
		. "</div></div>\n"
		. "</fieldset>\n"
		//
		. fin_cadre_relief(true)
		;
	//
	// bouton valider
	$page_result .= ""
		. "<div style='text-align:$spip_lang_left;margin:0.75em 0 0;'>\n"
		. "<div style='text-align:$spip_lang_right;margin:0;'>\n"
		. "<input type='reset' name='btn_valider_annule' value='"._T('bouton_annuler')."' class='fondo' />\n"
		. "<input type='submit' name='btn_valider_configure' value='"._T('bouton_valider')."' class='fondo' />\n"
		. "</div>\n"
		. "</div>\n"
		;
	//
	// fin du formulaire
	$page_result .= ""
		. lilo_form_fin_form()
		. fin_cadre_trait_couleur(true)
		;
	
	echo($page_result);
	echo __plugin_html_signature(_LILO_PREFIX, true), fin_gauche(), fin_page();
	return(true);
}

/***********************************************/
function lilo_form_input_text ($nom_champ, $label_champ, $value) {
	$page_result = ""
		. "<label for='$nom_champ' style='display:block;margin-top:0.5em;'>"._T(_LILO_LANG.$label_champ).":</label>\n"
		. "<input type='text' name='$nom_champ' id='$nom_champ' size='40' class='forml' style='margin-top:0.5em;' value=\"".$value."\" />\n"
		;
	return($page_result);
}

/***********************************************/
function lilo_form_debut_form ($nom_form, $ancre = '') {
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

/***********************************************/
function lilo_form_fin_form () {
	$page_result = ""
		. "</form>\n"
		. "</div>\n"
		;
	return($page_result);
}

/***********************************************/
function lilo_form_checkbox_button ($nom_checkbox, $label_checkbox, $checked, $value, $div = false) {
	global $spip_lang_left;
	$page_result = ""
		. ($div ? "<div style='text-align: $spip_lang_left;' class='verdana2'>" : "")
		. "<input name='$nom_checkbox' type='checkbox' id='$nom_checkbox' value='$value'". ($checked ? " checked='checked'" : "")." />\n"
		. "<label for='$nom_checkbox'>".$label_checkbox."</label>\n"
		. ($div ? "</div>" : "")
		;
	return($page_result);
}

/***********************************************/
function lilo_form_description ($texte) {
	if(!$texte) return ('');
	return( ""
		. "<div  style='text-align: $spip_lang_left;font-style: italic;' class='verdana2'>\n"
		. _T(_LILO_LANG.$texte)
		. "</div>\n"
	);
}

/***********************************************/
function lilo_form_legend ($texte) {
	if(!$texte) return ('');
	return( ""
		. "<legend class='verdana2'>"._T(_LILO_LANG.$texte)."</legend>\n"
	);
}

/***********************************************/
function lilo_form_radio_button_position ($nom_radio, $label_radio, $current_value, $value) {
	static $id = 1;
	$title = _T(_LILO_LANG.$label_radio);
	$page_result = ""
		. "<div style='margin:0.5em 0 0;' class='verdana2'>\n"
		. "<input type='radio' name='$nom_radio' value='$value' id='".$nom_radio."_$id'"
			. (($current_value == $value) ? " checked='checked'" : "")
			. " title = \"$title\" />\n"
		. "<label for='".$nom_radio."_$id'>$title</label>\n"
		. "</div>\n"
		;
	$id++;
	return($page_result);
}

/***********************************************/
function lilo_form_radio_button_color ($nom_radio, $label_radio, $current_value, $value) {
	static $idcolor = 1;
	$title = _T(_LILO_LANG.$label_radio)." (#$value)";
	$page_result = ""
		. "<div style='margin:0.5em 0 0;background-color:#$value;line-height:1.4em;' class='verdana2'>\n"
		. "<input type='radio' name='$nom_radio' value='$value' id='".$nom_radio."_$idcolor' style='line-height:1.3em;'"
			. (($current_value == $value) ? " checked='checked'" : "")
			. " title = \"$title\" />\n"
		. "<label for='".$nom_radio."_$idcolor'><span style='line-height:1.4em;height:1.4em;padding:0 1ex;color:#fff;'>$title</span></label>\n"
		. "</div>\n"
		;
	$idcolor++;
	return($page_result);
}

//
?>