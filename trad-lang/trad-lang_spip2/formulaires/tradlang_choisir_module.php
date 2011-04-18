<?php

function formulaires_tradlang_choisir_module_charger($module="",$lang_orig="",$lang_cible="",$lang_crea=""){
	$module_defaut = sql_getfetsel('module','spip_tradlang_modules','','','','0,1');
	$module = _request('module') ? _request('module') : $module;
	/**
	 * Si aucun module dans la base
	 */
	if(!$module_defaut){
		$valeurs['message_erreur'] = _T('tradlang:erreur_aucun_module');
		$valeurs['editable'] = false;
		return $valeurs;
	}
	
	if(!$module OR !sql_getfetsel('id_tradlang_module','spip_tradlang_modules','module='.sql_quote($module))){
		$valeurs['module'] = $module = $module_defaut;
	}
	
	include_spip('inc/autoriser');
	if(autoriser('modifier','tradlang')){
		$valeurs = array('module' => $module,'lang_orig' => $lang_orig,'lang_cible'=>$lang_cible,'lang_crea'=> $lang_crea);
		foreach($valeurs as $key => $val){
			if(_request($key)){
				$valeurs[$key] = _request($key);
			}
		}
		
		$valeurs['lang_mere'] = sql_getfetsel('lang_mere','spip_tradlang_modules',"module=".sql_quote($valeurs['module']));
		
		include_spip('inc/lang_liste');
		$langues_possibles = $GLOBALS['codes_langues'];
		
		$langues_modules = sql_select('DISTINCT lang','spip_tradlang','module='.sql_quote($module));
		while($langue = sql_fetch($langues_modules)){
			$langues_presentes[$langue['lang']] = traduire_nom_langue($langue['lang']);
		}
		if(is_array($langues_presentes))
			$langues_possibles = array_diff($langues_possibles,$langues_presentes);
		
		$config = @unserialize($GLOBALS['meta']['tradlang']);
		if (is_array($config) && is_array($config['langues_autorisees'])){
			foreach($config['langues_autorisees'] as $key=>$val){
				$langues_conf[$val] = traduire_nom_langue($val);
			}
			spip_log($langues_conf);
			$langues_possibles = array_intersect_key($langues_possibles,$langues_conf);	
		}
			
		$valeurs['_langues_possibles'] = $langues_possibles;
		if(!$lang_orig){
			$valeurs['lang_orig'] = $valeurs['lang_mere'];
		}
	}else{
		$valeurs['editable'] = false;
		$valeurs['message_erreur'] = _T('tradlang:erreur_autorisation_modifier_modules');
	}
	return $valeurs;
}

function formulaires_tradlang_choisir_module_verifier($module="",$lang_orig="",$lang_cible="",$lang_crea=""){
	$erreur = array();
	if(!_request('lang_cible') && !_request('creer_lang_cible')){
		$erreur['lang_cible'] = _T('tradlang:erreur_pas_langue_cible');
	}
	return $erreur;
}

function formulaires_tradlang_choisir_module_traiter($module="",$lang_orig="",$lang_cible="",$lang_crea=""){
	$module = _request('module');
	$lang_orig = _request('lang_orig');
	$lang_cible = _request('lang_cible');
	$lang_crea = _request('creer_lang_cible');
	if($traduire = _request('traduire')){
		include_spip('inc/autoriser');
		$res['message_ok'] = _T('tradlang:message_passage_trad');
		if($lang_crea && autoriser('tradlang','configurer')){
			// Import de la langue mere
			$infos_module = sql_fetsel('*','spip_tradlang_modules','nom_mod='.sql_quote($module));
			$ajouter_code_langue = charger_fonction('tradlang_ajouter_code_langue','inc');
			$ajouter_code_langue($infos_module,$lang_crea);
			$lang_cible = $lang_crea;
			$res['message_ok'] = _T('tradlang:message_passage_trad_creation_lang',array('lang'=>$lang_crea));
		}
		$res['redirect'] = parametre_url(parametre_url(parametre_url(parametre_url(generer_url_public("traduction","etape=traduction"),"module",$module),"lang_orig",$lang_orig),"lang_cible",$lang_cible),'lang_crea',$lang_crea);
	}else{
		$res['editable'] = true;
	}
	return $res;
}
?>