<?php

function formulaires_tradlang_filtrer_traductions_charger($module,$lang_orig,$lang_cible){
	$valeurs = array('module'=>$module,'lang_orig'=>$lang_orig,'lang_cible'=> $lang_cible);
	
	return $valeurs;
}

function formulaires_tradlang_filtrer_traductions_verifier($module,$lang_orig,$lang_cible){
	$erreurs = array();
	//$new_module = _request('module');
	$new_lang_orig = _request('lang_orig');
	$new_lang_cible = _request('lang_cible');
	if($new_lang_cible == $new_lang_orig){
		$erreurs['lang_orig'] = _T('tradlang:erreurs_langues_differentes');
		$erreurs['lang_cible'] = _T('tradlang:erreurs_langues_differentes');
	}else if(!$new_lang_orig && ($new_lang_cible == $lang_orig)){
		$erreurs['lang_cible'] = _T('tradlang:erreurs_langues_differentes_unique');
	}else if(!$new_lang_cible && ($new_lang_orig == $lang_cible)){
		$erreurs['lang_orig'] = _T('tradlang:erreurs_langues_differentes_unique');
	}
	if(count($erreurs)>0){
		set_request('lang_orig',$lang_orig);
		set_request('lang_cible',$lang_cible);
	}
	return $erreurs;
}

function formulaires_tradlang_filtrer_traductions_traiter($module,$lang_orig,$lang_cible){
	$new_module = _request('module');
	$new_lang_orig = _request('lang_orig') ? _request('lang_orig') : $lang_orig;
	$new_lang_cible = _request('lang_cible') ? _request('lang_cible') : $lang_cible;
	$res['redirect'] = parametre_url(parametre_url(parametre_url(self(),'lang_cible',$new_lang_cible),'lang_orig',$new_lang_orig),'module',$new_module);
	$res['editable'] = true;
	return $res;
}
?>