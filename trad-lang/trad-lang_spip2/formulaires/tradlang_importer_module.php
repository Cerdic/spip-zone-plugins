<?php

function formulaires_tradlang_importer_module_charger(){
	$valeurs = array();
	
	$valeurs['nom_mod'] = _request('nom_mod');
	
	include_spip('inc/tradlang_importer_module');
	list($select_modules, $modules) = tradlang_select_liste_rep_lang('nom_mod',$valeurs['nom_mod'],true);
	$valeurs['_select_nom_mod'] = $select_modules;
	
	if($valeurs['nom_mod']){
		$valeurs = array_merge($valeurs,$modules[$valeurs['nom_mod']]);
		$valeurs['_select_langues_dispo'] = tradlang_select_langues_module($valeurs['nom_mod'],"lang_mere",_request('lang_mere'),true);
		$fichiers_non_accessibles = tradlang_verifier_acces_fichiers($valeurs['nom_mod']);
		if (count($fichiers_non_accessibles)){
			$valeurs['_fichiers_non_accessibles'] = "<p class='attention'>"._T("tradlang:attentionimport");
			$valeurs['_fichiers_non_accessibles'] .= "<br /><br />".implode("<br />", $fichiers_non_accessibles)."</p>\n";
		}
	}
	return $valeurs;
}

function formulaires_tradlang_importer_module_verifier(){
	$erreurs = array();
	if(_request('importer')){
		$champs_obli = array('dir_lang','nom_mod','lang_mere');
		foreach($champs_obli as $champ){
			if(!_request($champ)){
				$erreurs[$champ] = _T('tradlang:champ_obligatoire');
			}
		}
	}
	if(count($erreurs) > 0){
		$erreurs['message_erreur'] = _T('tradlang:verifier_formulaire');
	}
	return $erreurs;
}

function formulaires_tradlang_importer_module_traiter(){
	if(_request('importer')){
		$import = charger_fonction('tradlang_importer_module','inc');
		$traiter['dir_lang'] = _request('dir_lang');
		$traiter['nom_mod'] = _request('nom_mod');
		$traiter['lang_mere'] = _request('lang_mere');
		$traiter['nom_module'] = _request('nom_module') ? _request('nom_module') : _request('nom_mod');
		$traiter['type_export'] = 'spip';
		$traiter['lang_prefix'] = $traiter['nom_mod'];
		list($retour,$etat) = $import($traiter);
		$res['message_ok'] = $retour;
		if($etat){
			$res['editable'] = false;
			set_request('nom_mod','');		
		}
	}
	$res['editable'] = true;
	return $res;
}
?>