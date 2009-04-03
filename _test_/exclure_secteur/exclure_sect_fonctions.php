<?php
function exclure_sect_choisir(){
    $cfg =lire_config('secteur/exclure_sect');
    $cfg = array_map('sql_quote',$cfg);
    $cfg = implode($cfg,',');
    
    gettype($cfg)=='NULL' ? $cfg = 0 : $cfg = $cfg;
    
    return '('.$cfg.')';

}

function boucle_ARTICLES($id_boucle, &$boucles) {
    
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	
    if(!$boucle->modificateur['tout']){
        $boucle->where[] = array("NOT IN", "'$id_table.id_secteur'", 'sql_quote(exclure_sect_choisir())');
    
    }

	return boucle_ARTICLES_dist($id_boucle, $boucles);
}

function boucle_RUBRIQUES($id_boucle, &$boucles) {
    
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	
    if(!$boucle->modificateur['tout']){
        $boucle->where[] = array("'NOT IN'", "'$id_table.id_secteur'", 'exclure_sect_choisir()');
    
    }

	return boucle_RUBRIQUES_dist($id_boucle, $boucles);
}

function boucle_SYNDICATION($id_boucle, &$boucles) {
    
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	
    if(!$boucle->modificateur['tout']){
        $boucle->where[] = array("'NOT IN'", "'$id_table.id_secteur'", 'exclure_sect_choisir()');
    
    }

	return boucle_SYNDICATION_dist($id_boucle, $boucles);
}

?>