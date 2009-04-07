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
    
    if ($type == 'forum'){
        $crit = $boucle->criteres;
        $exclut = exclure_sect_choisir($crit);        
        global $table_prefix;
        $boucle->from['L1']=$table_prefix.'_articles';
        $boucle->where[] = array("'NOT IN'", "'L1.id_secteur'",sql_quote($exclut));  
        $boucle->join['L1'] = array('forum','id_article');
    }

    return $boucle;
}
?>