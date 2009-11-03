<?php

include_spip('tradlang_fonctions');

function formulaires_tradlang_ajout_codelangue_charger($module){
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

function formulaires_tradlang_ajout_codelangue_verifier($module){
	$erreur = array();
	
	$modules = tradlang_getmodules_base();
	$modok = $modules[$module];
	foreach($modok as $cle=>$item){
		if (strncmp($cle, "langue_", 7) == 0)
			$lgs[] = substr($cle,7);
	}
	
	$nouvelle_langue = _request('codelangue');
	
	include_spip('inc/lang_liste');
	if(!_request('codelangue')){
		$erreur['codelangue'] = _T('tradlang:erreur_code_langue_vide');
	}
	else if(in_array($nouvelle_langue,$lgs)){
		$erreur['codelangue'] = _T('tradlang:erreur_code_langue_existant');
	}else if(!array_key_exists($nouvelle_langue,$GLOBALS['codes_langues'])){
		$erreur['codelangue'] = _T('tradlang:erreur_code_langue_invalide');
	}
	
	return $erreur;
}

function formulaires_tradlang_ajout_codelangue_traiter($module){
	$res = sql_select('*','spip_tradlang_modules','nom_mod='.sql_quote($module));
	$modok = sql_fetch($res);
	$langue = _request('codelangue');
	
	$sauvegarde = charger_fonction('tradlang_ajouter_code_langue','inc');
	$sauvegarde($modok, $langue);
	
	$ret['editable'] = true;
	return $ret;
}
?>