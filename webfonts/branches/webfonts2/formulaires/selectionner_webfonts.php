<?php
/*
 * Squelette
 * (c) 2016 
 * Distribue sous licence GPL
 *
 * @url - http://programmer.spip.net/-Formulaires-35-
 * http://marcimat.magraine.net/Les-formulaires-CVT-de-SPIP
 * 
 *
 */

function formulaires_selectionner_webfonts_charger_dist(){
	$valeurs = array(
		'font_search'=>_request('font_search'),
		'sort'=>_request('sort'),
		'preview_text'=>_request('preview_text'),
		'category'=>_request('category'),
		'preview'=>_request('preview'),
		'preview_font_size'=>_request('preview_font_size'),
		'infos'=>_request('infos')
    );
	return $valeurs;
}

function formulaires_selectionner_webfonts_verifier_dist(){
	
	$erreurs = array();
	if(!defined('_GOOGLE_API_KEY')) {
		
		$erreurs['message_erreur'] = "Pas de API KEY definie";
	}
	
	if (count($erreurs)) {
		$erreurs['message_erreur'] = "Une erreur est présente dans votre saisie";
	}
	return $erreurs;
}

function formulaires_selectionner_webfonts_traiter_dist(){
	
    ($sort = _request('sort')) ? $sort = _request('sort') : $sort = false;
	($category = _request('category')) ? $category = _request('category') : $category ;
	
	if(defined('_GOOGLE_API_KEY') && _GOOGLE_API_KEY != false) {
		
		$googlefonts = googlefont_api_get(_GOOGLE_API_KEY,$sort,$category);
		
		if($font_search = _request('font_search')){
			$result = google_font_search($googlefonts, _request('font_search'));
		}else{
			$result = $googlefonts['items'];
		}
		
		include_spip('flock','inc');
		$jsonfile = ecrire_fichier(_DIR_TMP.'/googlefont_list.json',json_encode($result));
		
		$res = array('message_ok'=>_T('config_info_enregistree'),'editable'=>true);
	}else{
		$res = array('message_erreur'=>'Pas de API KEY definie');
	}

	
	
	return $res;
}





?>