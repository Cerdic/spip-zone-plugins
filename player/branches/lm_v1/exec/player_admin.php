<?php

	// exec/player_admin.php
	
	// $LastChangedRevision:$
	// $LastChangedBy:$
	// $LastChangedDate:$

	/*****************************************************
	Copyright (C) 2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************/

// CP-20080321 : premier jet

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_player_admin()
{
	global $connect_statut
		, $connect_toutes_rubriques
		, $spip_lang_right
		, $spip_lang_left
		;
	
	include_spip('inc/presentation');
	include_spip('inc/meta');
	include_spip('inc/config');

	include_spip('inc/player_affiche_config_form');
	include_spip('inc/player_flv_config');

	if (!(($connect_statut == '0minirezo') && $connect_toutes_rubriques)) {
		echo _T('avis_non_acces_page');
		echo fin_gauche(), fin_page();
		exit;
	}

	$message_gauche = "";

	$rubrique = "configuration";

	$player_flv_lecteurs = unserialize(_PLAYER_FLV_LECTEURS);

	// la grosse table commune a tous les profils
	$player_flv_config = player_flv_config();
	
	// lecture du meta
	$player_config = $GLOBALS['meta'][_PLAYER_META_PREFERENCES];
	$player_config = $player_config ? unserialize($player_config) : array();
	
	// est-ce bien un retour du formulaire ?
	$player_retour_formulaire = _request('btn_valider_video');
	
	// aplatir le tableau en ne recuperant que les valeurs
	$player_flv_lecteurs_values = array();
	foreach($player_flv_lecteurs as $key => $value) {
		$player_flv_lecteurs_values[$key] = $value['value'];
	}
	// verifier si le lecteur video indique est correct
	$player_video = $player_config['player_video'] = 
		(($ii = _request('player_video')) && (in_array($ii, $player_flv_lecteurs_values)))
		? $ii 
		: _PLAYER_FLV_LECTEUR_DEFAULT
		;
	$player_key = array_search($player_video, $player_flv_lecteurs_values);
	$player_config['player_key'] = $player_key;
	
	// premiere install pour le profil ou global
	if($player_premiere_installation = (!isset($player_config['player_video_prefs']))) {
		spip_log("PLAYER: premiere installation profil $player_key");
		$player_config['player_video_prefs'] = array();
	}
	
	// initialiser les variables, 
	foreach($player_flv_config as $key => $value) {
		if($player_retour_formulaire) {
			$$key = (($ii = _request($key)) ? $ii : '');
		} else if($player_premiere_installation) {
			$$key = $value['default'];
		} else {
			$$key = (isset($player_config['player_video_prefs'][$key]) ? $player_config['player_video_prefs'][$key] : '');
		}
		// retire les # (il n'en faut pas en flash)
		if($value['type'] == 'color') {
			$$key = ltrim($$key, '#');
		}
		// ne pas enregistrer les variables vides
		if(!empty($$key)) {
			$player_config['player_video_prefs'][$key] = $$key;
		}
		else if(isset($player_config['player_video_prefs'][$key])) {
			unset($player_config['player_video_prefs'][$key]);
		}
	}
	
	if($player_retour_formulaire) {
		//spip_log("PLAYER: enregistrement config profil $player_key". serialize($player_config));
		// enregistrer la config
		ecrire_meta(_PLAYER_META_PREFERENCES, serialize($player_config));
		if(version_compare($GLOBALS['spip_version_code'],'1.9300','<')) { 
			include_spip("inc/meta");
			ecrire_metas();
		}
	}
	
	$commencer_page = 
		(function_exists('debut_page'))
		? "debut_page"
		: charger_fonction('commencer_page', 'inc')
		;

////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////
	
	$page_result = ""
		. $commencer_page(_T(_PLAYER_LANG."configuration_player"), _PLAYER_PREFIX)
		. "<div style='height:3em;'></div>\n"
		. gros_titre(_T(_PLAYER_LANG."configuration_player"), "", false)
		. barre_onglets($rubrique, _PLAYER_PREFIX)
		. debut_gauche($rubrique, true)
		. player_petite_boite_info()
		. $message_gauche
		. creer_colonne_droite($rubrique, true)
		. debut_droite($rubrique, true)
		;
			
	////////////////////////////////////
	// configuration audio
	$page_result .= ""
		. player_affiche_config_form('player_admin')
		;

	////////////////////////////////////
	// configuration video
	$page_result .= ""
		. debut_cadre_trait_couleur(_DIR_PLUGIN_PLAYER_IMAGES."player-video-24.png", true, '', _T(_PLAYER_LANG."player_video"))
		. player_form_debut_form('player_video_config')
		. "<ul class='player_flv_player verdana2'>\n"
		;
	foreach($player_flv_lecteurs as $key => $value) {
		$checked = ($player_video == $value['value']) ? "checked='checked' " : "";
		$class = ($checked ? " onglet_off" : "");
		$page_result .= ""
			. "<li class='flv_onglet".$class."'>"
			. "<label><input type='radio' name='player_video' value='".$value['value']."' id='$key' $checked/> ".$value['label']."</label></li>\n"
			;
	}
	$page_result .= ""
		. "</ul>\n"
		. "<ul id='player_flv_options'>\n"
		;
	foreach($player_flv_config as $key => $value) {
		$player_flv_options = "";
		switch($value['type']) {
			case 'boolean':
				$player_flv_options = "<label title='$key'><input name='$key' type='checkbox' value='1' "
					. ($$key == "1" ? "checked='checked'" : "")." />".$value['label']."</label>\n";
				break;
			case 'url':
			case 'int':
			case 'text':
				$player_flv_options = "<label title='$key'>".$value['label']."<input type='text' name='$key' value='".$$key."' class='forml' /></label>\n";
				break;
			case 'list':
				$player_flv_options = "<label title='$key'>".$value['label']."<select name='$key' class='fondl'>\n";
				foreach($value['values'] as $k1 => $v1) {
					// si la cle n'est pas une chaine, prendre $v1 pour valeur de option
					$ii = (is_string($v1) ? _T(_PLAYER_LANG.$v1) : $v1);
					$player_flv_options .= "<option value='$k1'".(($$key == $k1) ? " selected='selected'" : "").">$ii</option>\n";
				}
				$player_flv_options .= "</select></label>\n";
				break;
			case 'color':
				$player_flv_options = "<label class='incolor' title='$key'>".$value['label']
					. "<input type='text' name='$key' value='#".$$key."' style='background-color:#".$$key."' id='$key' size='7' />\n"
					. "<span class='colorpicker' style='display:none;'></span>"
					. "</label>\n";
				break;
		}
		
		$style = in_array($player_key, explode(' ', $value['class'])) ? "" : " style='display:none;'";
		$page_result .= "<li class='verdana2 ".$value['class']."'".$style.">".$player_flv_options."</li>\n";
	}
	$page_result .= ""
		. "</ul>\n"
		;
	
	////////////////////////////////////
	// fin du formulaire
	$page_result .= ""
		. "<div style='text-align:$spip_lang_right'><input type='submit' name='btn_valider_video' value='"._T('bouton_valider')."' class='fondo' /></div>"
		. player_form_fin_form()
		. fin_cadre_trait_couleur(true)
		;

	echo($page_result);
	echo fin_gauche(), fin_page();
	return(true);
}

/***********************************************/
function player_form_debut_form ($nom_form, $ancre = '') {
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
function player_form_fin_form () {
	$page_result = ""
		. "</form>\n"
		. "</div>\n"
		;
	return($page_result);
}

/***********************************************/
function player_petite_boite_info () {
	include_spip('inc/plugin');
	if ($GLOBALS['spip_version_code']>=15133)
		include_spip('plugins/afficher_plugin');
	$get_infos = ($GLOBALS['spip_version_code']>=15133)?charger_fonction('get_infos','plugins'):'plugin_get_infos'; // Compatibilite SPIP 2.1
	$info = $get_infos(_DIR_PLUGIN_PLAYER);
	$titre = _T(_PLAYER_LANG.'player_nom');
	$result = ""
		. debut_cadre_relief('plugin-24.gif', true, '', $titre)
		. affiche_bloc_plugin(_DIR_PLUGIN_PLAYER, $info)
		. fin_cadre_relief(true)
		;
	return($result);
}
?>
