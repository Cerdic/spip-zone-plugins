<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');
include_spip('tradlang_fonctions');

function formulaires_editer_tradlang_module_charger($id_tradlang_module,$retour=''){
	$valeurs = formulaires_editer_objet_charger('tradlang_module',$id_tradlang_module,0,'',$retour,$config_fonc,$row,$hidden);
	
	$modules = tradlang_getmodules_base();
	$modok = $modules[$valeurs['module']];
	foreach($modok as $cle=>$item){
		if (strncmp($cle, "langue_", 7) == 0)
			$lgs[] .= substr($cle,7);
	}
	
	$valeurs['_langues'] = $lgs;
	$valeurs['codelangue'] = _request('codelangue');
	spip_log($valeurs,'test.'._LOG_ERREUR);
	return $valeurs;
}

function formulaires_editer_tradlang_module_verifier($id_tradlang_module,$retour=''){
	$erreur = array();
	$module = sql_getfetsel('module','spip_tradlang_modules','id_tradlang_module='.intval($id_tradlang_module));
	$modules = tradlang_getmodules_base();
	if(!isset($modules[$module])){
		$erreur['module'] = _T('tradlang:erreur_module_inexistant');
	}
	$modok = $modules[$module];
	foreach($modok as $cle=>$item){
		if (strncmp($cle, "langue_", 7) == 0)
			$lgs[] = substr($cle,7);
	}
	
	$nouvelle_langue = _request('codelangue');
	
	include_spip('inc/lang_liste');
	if($nouvelle_langue){
		if(in_array($nouvelle_langue,$lgs))
			$erreur['codelangue'] = _T('tradlang:erreur_code_langue_existant');
		else if(!array_key_exists($nouvelle_langue,$GLOBALS['codes_langues']))
			$erreur['codelangue'] = _T('tradlang:erreur_code_langue_invalide');
	}
	
	$limite_trad = _request('limite_trad');
	if(!is_numeric($limite_trad) || (intval($limite_trad) < 0) || (intval($limite_trad) > 100))
		$erreur['limite_trad'] = _T('tradlang:erreur_limite_trad_invalide');

	return $erreur;
}

function formulaires_editer_tradlang_module_traiter($id_tradlang_module,$retour=''){
	$ret = array();
	$module = sql_getfetsel('nom_mod','spip_tradlang_modules','id_tradlang_module='.intval($id_tradlang_module));
	if(_request('delete_module')){
		$supprimer_module = charger_fonction('tradlang_supprimer_module','inc');
		$suppressions = $supprimer_module($id_tradlang_module);
		$ret['editable'] = false;
		if(intval($suppressions) && ($suppressions > 1))
			$ret['message_ok'] = _T('tradlang:message_suppression_module_trads_ok',array('nb'=>$suppressions,'module'=>$module));
		else
			$ret['message_ok'] = _T('tradlang:message_suppression_module_ok',array('module'=>$module));
	}
	else{
		$res = sql_select('*','spip_tradlang_modules','id_tradlang_module='.intval($id_tradlang_module));
		$modok = sql_fetch($res);
		$langue = _request('codelangue');
		
		$datas = array(
			'nom_mod' => _request('nom_mod') ? _request('nom_mod') : $module,
			'lang_mere' => _request('lang_mere'),
			'texte' => _request('texte'),
			'priorite' => _request('priorite')
		);
		$limite_trad = _request('limite_trad') ? _request('limite_trad') : 0;
		$datas['limite_trad'] = $limite_trad;
		sql_updateq('spip_tradlang_modules',$datas,'id_tradlang_module='.intval($id_tradlang_module));
		$ret['message_ok'] = _T('tradlang:message_module_updated',array('module'=>$module));
		
		if($langue){
			$sauvegarde = charger_fonction('tradlang_ajouter_code_langue','inc');
			$sauvegarde($modok, $langue);
			$ret['message_ok'] .= "<br />"._T('tradlang:message_module_langue_ajoutee',array('module'=>$module,'langue'=>$langue));
		}
		$ret['editable'] = true;
		$ret['redirect'] = $retour;
	}
	return $ret;
}
?>