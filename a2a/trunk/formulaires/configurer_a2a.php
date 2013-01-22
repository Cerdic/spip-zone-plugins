<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// Transforme en tableau une liste de type de la forme :
// type, texte
// type2, texte2
include_spip('inc/config');
function a2a_types_liaisons2array($type){
	$tableau 	= array();
	$lignes 	= explode("\n",$type);
	foreach ($lignes as $l){
		$donnees					= explode(',',$l);
		if ($donnees[1])
			$tableau[trim($donnees[0])]	= trim ($donnees[1]);
		else
			$tableau[trim($donnees[0])] = '';
	}
	
	return $tableau;
}
function formulaires_configurer_a2a_charger(){
	if ($cfg=lire_config('a2a'))
		return $cfg;
	else
		return array();
}

function formulaires_configurer_a2a_verifier(){
	$erreurs = array();
	$types_liaisons 	= a2a_types_liaisons2array(_request('types_liaisons'));
	$types_liaisons_actuels = lire_config('a2a/types_liaisons');
	if ($GLOBALS['a2a_types_liaisons']){
		$types_liaisons = array_merge($types_liaisons,$GLOBALS['a2a_types_liaisons']);
	}
	$diff 	= array_diff_key($types_liaisons_actuels,$types_liaisons); // les clefs supprimés 
	$sup_pb = array(); // tableau associatifs listant les types_liaisons supprimés problématiques, car il y a des relations portant ce type
	foreach ($diff as $type=>$nom){
		$relations = sql_allfetsel('id_article','spip_articles_lies','type_liaison='.sql_quote($type),'id_article');
		
		if (count($relations) > 0){
			$sup_pb[$type] = $relations;	
		} 	
	}
	if (count($sup_pb) > 0){ // si un pb
		$erreurs['sup_pb'] = $sup_pb;
	}
	// teste si on rend obligatoire les types de liaisons qu'il n'existe pas de liaison non typées
	
	if (_request('type_obligatoire')=='on'){
		$relations = sql_allfetsel('id_article','spip_articles_lies','type_liaison='.sql_quote(''),'id_article');
		if (count($relations) > 0){
			$erreurs['ob_pb'] = $relations;	
		} 	
	}
	
	// teste si on enlève la possiblité de liaisons multiples
	
	if (!_request('types_differents')){
		$liaison_total = sql_allfetsel('id_article, id_article_lie, COUNT(type_liaison) as liaison_total', 'spip_articles_lies',''," id_article, id_article_lie",'','','liaison_total>1');
		if (count($liaison_total) > 0){
			$erreurs['td_pb'] = $liaison_total;
		}
		
	}
	return $erreurs;

}

function formulaires_configurer_a2a_traiter(){
	$cfg = array();
	$cfg['types_liaisons']  = a2a_types_liaisons2array(_request('types_liaisons'));
	$cfg['type_obligatoire'] = _request('type_obligatoire');
	$cfg['types_differents'] = _request('types_differents');
	ecrire_config('a2a',$cfg);
	$cfg['message_ok']='oui';
	return $cfg;
}

?>