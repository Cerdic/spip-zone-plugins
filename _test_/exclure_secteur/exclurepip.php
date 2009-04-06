<?php
include_spip('inc/exclure_utils');
function exclure_sect_pre_boucle(&$boucle){
 
    if ($boucle->modificateur['tout_voir'] or ($boucle->modificateur['tout'] and lire_config('secteur/tout') == 'oui')){
        return $boucle;
    }
    $type = $boucle->id_table;
    
    
    if ($type == 'articles' or $type == 'rubriques' or $type == 'syndic'){
    
        $crit = $boucle->criteres;
        $exclut = exclure_sect_choisir($crit);       
        $boucle->where[] = array("'NOT IN'", "'$type.id_secteur'",sql_quote($exclut));    
         
    }
    
    if ($type == 'breves'){
    
        $crit = $boucle->criteres;
        $exclut = exclure_sect_choisir($crit);       
        $boucle->where[] = array("'NOT IN'", "'$type.id_rubrique'",sql_quote($exclut));    
         
    }

    
    return $boucle;
}
?>