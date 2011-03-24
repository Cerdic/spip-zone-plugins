<?php

function formulaires_tradlang_choisir_module_charger($module="",$lang_orig="",$lang_cible="",$lang_crea=""){
	$module_defaut = sql_getfetsel('nom_mod','spip_tradlang_modules','','','','0,1');
	
	/**
	 * Si aucun module dans la base
	 */
	if(!$module_defaut){
		$valeurs['message_erreur'] = _T('tradlang:erreur_aucun_module');
		$valeurs['editable'] = false;
		return $valeurs;
	}
	$valeurs = array('module' => $module,'lang_orig' => $lang_orig,'lang_cible'=>$lang_cible,'lang_crea'=> $lang_crea);
	foreach($valeurs as $key => $val){
		if(_request($key)){
			$valeurs[$key] = _request($key);
		}
	}
	if(!$module OR !sql_getfetsel('idmodule','spip_tradlang_modules','nom_mod='.sql_quote($module))){
		$valeurs['module'] = $module_defaut;
	}
	
	$valeurs['lang_mere'] = sql_getfetsel('lang_mere','spip_tradlang_modules',"nom_mod=".sql_quote($valeurs['module']));

	if(!$lang_orig){
		$valeurs['lang_orig'] = $valeurs['lang_mere'];
	}
	return $valeurs;
}

function formulaires_tradlang_choisir_module_verifier($module="",$lang_orig="",$lang_cible="",$lang_crea=""){
	$erreur = array();
	if(!_request('lang_cible')){
		$erreur['lang_cible'] = _T('tradlang:erreur_pas_langue_cible');
	}
	return $erreur;
}

function formulaires_tradlang_choisir_module_traiter($module="",$lang_orig="",$lang_cible="",$lang_crea=""){
	$module = _request('module');
	$lang_orig = _request('lang_orig');
	$lang_cible = _request('lang_cible');
	$lang_crea = _request('lang_crea');
	if($traduire = _request('traduire')){
		$res['message_ok'] = 'On passe à la traduction';
		$res['redirect'] = generer_url_public("trad_lang","etape=traduction&module=$module&lang_orig=$lang_orig&lang_cible=$lang_cible");
	}else{
		$res['editable'] = true;
	}
	return $res;
}
?>