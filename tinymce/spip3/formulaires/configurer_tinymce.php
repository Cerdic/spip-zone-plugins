<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("tinymce_fonctions") ;

function formulaires_configurer_tinymce_charger_dist(){
	$cfg_actuelle = tinymce_config();
	$tinymce_themes = $GLOBALS['tinymce_habillages'];
//var_export($cfg_actuelle);

	$valeurs = $cfg_actuelle;
	$valeurs['content_css_str'] = join(', ', $valeurs['content_css']);
	$valeurs['habillages'] = $GLOBALS['tinymce_habillages'];

	$valeurs['habillage_variants'] = array();
	if (true===isset($cfg_actuelle['skin']))
	{
		if (
			true===isset($tinymce_themes[$cfg_actuelle['skin']]) &&
			1<count($tinymce_themes[$cfg_actuelle['skin']])
		) {
			$valeurs['habillage_variants'] = $tinymce_themes[$cfg_actuelle['skin']];
		}
	}

	// liste des objets : trouver fonction ?
	$valeurs['objets_spip'] = tinymce_listerobjetsspip();
	
	// liste des modeles de config : faire fonction ?
	$valeurs['fonds_config'] = tinymce_listerfondsconfig();	

//var_export($valeurs);
	return $valeurs;
}

function formulaires_configurer_tinymce_traiter_dist(){
	include_spip('inc/meta');

	$usr_cfg = array(
		'objets'=>array(),
		'objets_barres'=>array(),
		'content_css'=>array(),
		'body_class' => _request('body_class'),
		'body_id' => _request('body_id'),
		'skin' => _request('skin'),
		'skin_variant' => _request('skin_variant') ? _request('skin_variant') : 'default',
	);

	$content_css_str = _request('content_css_str');
	$content_css_table = explode(',', $content_css_str);
	$usr_cfg['content_css'] = array();
	foreach($content_css_table as $css){
		$usr_cfg['content_css'][] = trim($css);
	}

	$objets = tinymce_listerobjetsspip();
	foreach($objets as $_obj=>$_str){
		$val = _request('objet_barre_'.$_obj);
		if ($val!='porteplume'){
			$usr_cfg['objets'][] = $_obj;
			$usr_cfg['objets_barres'][$_obj] = $val;
		}
	}

	ecrire_meta('tinymce', serialize($usr_cfg));
	return array('message_ok'=>_T('config_info_enregistree'));
}

?>