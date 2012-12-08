<?php

include_spip('inc/ckeditor_constantes') ;
include_spip('inc/ckeditor_tools') ;
include_spip("inc/toolbars") ;


function formulaires_configurer_ckeditor_p2_charger_dist() {
        ($cfg = lire_config("ckeditor")) || ($cfg = array()) ;
	$valeurs = array() ;
	foreach($GLOBALS['toolbars'] as $toolbar) {
		foreach($toolbar as $tool => $item) {
			if (!ckeditor_tweaks_actifs('smileys') && ($tool == 'Smiley')) continue ;
			$valeurs["tool_$tool"] = array_key_exists("tool_$tool", $cfg)?$cfg["tool_$tool"]:($item[1]) ;
		}
	}
	return $valeurs ;
}

function formulaires_configurer_ckeditor_p2_verifier_dist() {
	$result = array() ;
	return $result ;
}

function formulaires_configurer_ckeditor_p2_traiter_dist() {
	if (_request("_cfg_delete")) {
		$valeurs = formulaires_configurer_ckeditor_p2_charger_dist() ;
		foreach($valeurs as $cle => $valeur) {
			$_GET[$cle] = $valeur ;
			ecrire_config("ckeditor/$cle", $valeur) ;
		}
		return array('message_ok' => _T('ckeditor:ck_delete')) ;
	} else {
		$action = (_request('_cfg_reset_toolbars') ? 'reset' : (_request('_cfg_ok') ? 'ok' : '' ) ) ;
		foreach($GLOBALS['toolbars'] as $toolbar) {
			foreach($toolbar as $tool => $size) {
				if (!ckeditor_tweaks_actifs('smileys') && ($tool == 'Smiley')) continue ;
				if ($action == 'ok' ) ecrire_config("ckeditor/tool_$tool", _request("tool_$tool")?1:0) ;
				if ($action == 'reset') effacer_config("ckeditor/tool_$tool") ;
			}
		}
		return array('message_ok' => _T('ckeditor:ck_ok')) ;
	}
}

 ?>
