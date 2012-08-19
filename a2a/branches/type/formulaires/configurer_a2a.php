<?php

// Transforme en tableau une liste de type de la forme :
// type, texte
// type2, texte2
function a2a_types2array($type){
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
	return lire_config('a2a');	
}


function formulaires_configurer_a2a_traiter(){
	$cfg = array();
	$cfg['types']  = a2a_types2array(_request('types'));
	$cfg['type_obligatoire'] = _request('type_obligatoire');
	ecrire_config('a2a',$cfg);
	$cfg['message_ok']='oui';
	return $cfg;
}

?>