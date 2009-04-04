<?php
if (!function_exists('critere_tout_secteur_dist')){
function critere_tout_secteur_dist($idb, &$boucles, $crit) {   
    $boucle = &$boucles[$idb];
    $boucle->modificateur['tout_secteur'] = true;
}
}

function exclure_sect_choisir(){
  $cfg =lire_config('secteur/exclure_sect');
  if ( $cfg = array_map('sql_quote', $cfg) ) {
    $cfg = implode($cfg, ',');
  }
  else {
    $cfg = sql_quote('z');
  }

  return '('.$cfg.')';

}


function boucle_ARTICLES($id_boucle, &$boucles) {
    
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	
    if(!$boucle->modificateur['tout_secteur']){
        $boucle->where[] = array("'NOT IN'", "'$id_table.id_secteur'", 'exclure_sect_choisir()');    
    }

	return boucle_ARTICLES_dist($id_boucle, $boucles);
}

function boucle_RUBRIQUES($id_boucle, &$boucles) {
    
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	
    if(!$boucle->modificateur['tout_secteur']){
        $boucle->where[] = array("'NOT IN'", "'$id_table.id_secteur'", 'exclure_sect_choisir()');
    
    }

	return boucle_RUBRIQUES_dist($id_boucle, $boucles);
}

function boucle_SYNDICATION($id_boucle, &$boucles) {
    
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	
    if(!$boucle->modificateur['tout_secteur']){
        $boucle->where[] = array("'NOT IN'", "'$id_table.id_secteur'", 'exclure_sect_choisir()');
    
    }

	return boucle_SYNDICATION_dist($id_boucle, $boucles);
}

?>