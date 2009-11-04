<?php

function formulaires_tradlang_choisir_module_charger($module="",$lang_orig="",$lang_cible="",$lang_crea=""){
	$valeurs = array('module' => $module,'lang_orig' => $lang_orig,'lang_cible'=>$lang_cible,'lang_crea'=> $lang_crea);
	foreach($valeurs as $key => $val){
		if(_request($key)){
			$valeurs[$key] = _request($key);
		}
	}
	if(!$module){
		$valeurs['module'] = sql_getfetsel('nom_mod','spip_tradlang_modules','','','','0,1');
	}
	if(!$lang_orig){
		$valeurs['lang_orig'] = sql_getfetsel('lang_mere','spip_tradlang_modules',"nom_mod=".sql_quote($valeurs['module']));
	}
	return $valeurs;
}

function formulaires_tradlang_choisir_module_verifier($module="",$lang_orig="",$lang_cible="",$lang_crea=""){
	$erreur = array();
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