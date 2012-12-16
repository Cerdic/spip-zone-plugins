<?php

include_spip('inc/ckeditor_constantes') ;
include_spip('inc/ckeditor_tools') ;
include_spip("inc/toolbars") ;
include_spip('inc/ckeditor_lire_config') ;

function cke_case($val) {
	if (($val==='on') || ($val==='off')) {
		return $val ;
	} else {
		return ($val?'on':'off') ;
	}
}

function formulaires_configurer_ckeditor_p2_charger_dist() {
        ($cfg = lire_config("ckeditor")) || ($cfg = array()) ;
	$valeurs = array() ;
	$saisies = array() ;

	$html2spip = ckeditor_lire_config('html2spip_limite', _CKE_HTML2SPIP_LIMITE_DEF) ;
	foreach($GLOBALS['toolbars'] as $index => $toolbar) {
		$fieldset = array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'toolbar_'.$index
			),
			'saisies' => array()
		) ;

		$cp=0;
		foreach($toolbar as $tool => $item) {
			if (!ckeditor_tweaks_actifs('smileys') && ($tool == 'Smiley')) continue ;
			$valeurs["tool_$tool"] = cke_case(array_key_exists("tool_$tool", $cfg)?$cfg["tool_$tool"]:($item[1])) ;
			$saisie =  array('options'=>array('nom'=>'tool_'.$tool)) ;
			if ($html2spip && !$item[2]) {
				$saisie['options']['disabled']='oui' ;
			}
			$saisie['options']['label_case'] = _T('ckeditor:tool_'.$tool) ;

			if (isset($item[3]) && ($icon = find_in_path(sprintf("prive/themes/spip/images/icons/icon-%02d.png",$item[3])))) {
				$saisie['saisie'] = 'case_image' ;
				$saisie['options']['image_case'] = $icon ;
			} else {
				$saisie['saisie'] = 'case' ;
			}
			$fieldset['saisies'][] = $saisie ;
		}
		$saisies[] = $fieldset ;
	}

	$valeurs['cke_saisies_p2'] = $saisies ;

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
			if (preg_match('~^tool_~',$cle)) { 
				effacer_config("ckeditor/$cle") ;
				// éviter que les champs ne soit préremplis avec les valeurs précédentes
				unset($_POST[$cle]) ;
			}
		}
		return array('message_erreur' => _T('ckeditor:ck_delete')) ;
	} else {
		foreach($GLOBALS['toolbars'] as $toolbar) {
			foreach($toolbar as $tool => $size) {
				if (!ckeditor_tweaks_actifs('smileys') && ($tool == 'Smiley')) continue ;
				ecrire_config("ckeditor/tool_$tool", _request("tool_$tool")==='on'?1:0) ;
			}
		}
		return array('message_ok' => _T('ckeditor:ck_ok')) ;
	}
}

 ?>
