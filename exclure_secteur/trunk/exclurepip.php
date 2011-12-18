<?php
include_spip('inc/exclure_utils');

function exclure_sect_pre_boucle(&$boucle){
    
    if ($boucle->modificateur['tout_voir'] or ($boucle->modificateur['tout'] and lire_config('secteur/tout') == 'oui') or test_espace_prive()==1){
        return $boucle;
    }
    $type = $boucle->id_table;

    
    if ($type == 'articles' or $type == 'rubriques' or $type == 'syndic'){
    
        $crit = $boucle->criteres;
        $exclut = exclure_sect_choisir($crit,$type);    
        $boucle->where[] = "sql_in(id_secteur,$exclut,'NOT')";    
    }
    
    if ($type == 'breves'){
    
        $crit = $boucle->criteres;
        $exclut = exclure_sect_choisir($crit,$type);       
        $boucle->where[] = "sql_in(id_rubrique,$exclut,'NOT')";       
         
    }
    
    if ($type == 'forum'){
        $crit = $boucle->criteres;
        $exclut = exclure_sect_choisir($crit,$type);        
        global $table_prefix;
        $boucle->from['L1']=$table_prefix.'_articles';
        $boucle->where[] = array("'NOT'",array("'IN'", "'L1.id_secteur'",sql_quote($exclut)));  
        $boucle->join['L1'] = array('forum','id_article');
    }

    return $boucle;
}
?>