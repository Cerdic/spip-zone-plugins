<?php

include_spip('tradlang_fonctions');

function formulaires_editer_tradlang_module_charger($module){
	$valeurs = array();
	$res = sql_select('*','spip_tradlang_modules','nom_mod='.sql_quote($module));
	$valeurs = sql_fetch($res);
	
	$modules = tradlang_getmodules_base();
	$modok = $modules[$module];
	foreach($modok as $cle=>$item){
		if (strncmp($cle, "langue_", 7) == 0)
			$lgs .= substr($cle,7)." ";
	}
	
	$valeurs['langues'] = $lgs;
	$valeurs['codelangue'] = _request('codelangue');
	return $valeurs;
}

function formulaires_editer_tradlang_module_verifier($module){
	$erreur = array();
	
	$modules = tradlang_getmodules_base();
	if(!isset($modules[$module])){
		return $erreur;
	}
	$modok = $modules[$module];
	foreach($modok as $cle=>$item){
		if (strncmp($cle, "langue_", 7) == 0)
			$lgs[] = substr($cle,7);
	}
	
	$nouvelle_langue = _request('codelangue');
	
	include_spip('inc/lang_liste');
	if($nouvelle_langue){
		if(in_array($nouvelle_langue,$lgs)){
			$erreur['codelangue'] = _T('tradlang:erreur_code_langue_existant');
		}else if(!array_key_exists($nouvelle_langue,$GLOBALS['codes_langues'])){
			$erreur['codelangue'] = _T('tradlang:erreur_code_langue_invalide');
		}
	}
	
	return $erreur;
}

function formulaires_editer_tradlang_module_traiter($module){
	$ret = array();
	if(_request('delete_module')){
		$supprimer_module = charger_fonction('tradlang_supprimer_module','inc');
		$suppressions = $supprimer_module($module);
		$editable = false;
		if(intval($suppressions) && ($suppressions > 1)){
			$ret['message_ok'] = _T('tradlang:message_suppression_module_trads_ok',array('nb'=>$suppressions,'module'=>$module));
		}else{
			$ret['message_ok'] = _T('tradlang:message_suppression_module_ok',array('module'=>$module));
		}
	}
	else{
		$res = sql_select('*','spip_tradlang_modules','nom_mod='.sql_quote($module));
		$modok = sql_fetch($res);
		$langue = _request('codelangue');
		
		$datas = array(
			'nom_module' => _request('nom_module') ? _request('nom_module') : $module,
			'lang_mere' => _request('lang_mere'),
			'texte' => _request('texte')
		);
		
		sql_updateq('spip_tradlang_modules',$datas,'nom_mod='.sql_quote($module));
		$ret['message_ok'] = _T('tradlang:message_module_updated',array('module'=>$module));
		
		if($langue){
			$sauvegarde = charger_fonction('tradlang_ajouter_code_langue','inc');
			$sauvegarde($modok, $langue);
			$ret['message_ok'] .= "<br />"._T('tradlang:message_module_langue_ajoutee',array('module'=>$module,'langue'=>$langue));
		}
		$editable = true;
	}
	$ret['editable'] = $editable;
	return $ret;
}
?>