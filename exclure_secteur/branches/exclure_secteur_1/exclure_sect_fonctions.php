<?php
include_spip('inc/exclure_utils');
global $spip_version_affichee;


if (preg_match('/^1/',$spip_version_affichee)){
        
            function oui($i){
                if ($i == true){
                    return ' ';}
            return '';
            }

        
        /* surcharges de boucles*/
        function boucle_BREVES($id_boucle, &$boucles) {
            
            $boucle = &$boucles[$id_boucle];
            $id_table = $boucle->id_table;
            $crit = $boucle->criteres;
           
            if(!$boucle->modificateur['tout_voir'] and !($boucle->modificateur['tout'] and lire_config('secteur/tout') == 'oui')){
                $exclut = exclure_sect_choisir($crit);       
                $boucle->where[] = array("'NOT IN'", "'$id_table.id_rubrique'",sql_quote($exclut));    
            }
          
            return boucle_BREVES_dist($id_boucle, $boucles);
        }

        function boucle_FORUMS($id_boucle, &$boucles) {
            
            $boucle = &$boucles[$id_boucle];
            $id_table = $boucle->id_table;
            $crit = $boucle->criteres;
           
            if(!$boucle->modificateur['tout_voir'] and !($boucle->modificateur['tout'] and lire_config('secteur/tout') == 'oui')){
                $exclut = exclure_sect_choisir($crit);        
                global $table_prefix;
                $boucle->from['L1']=$table_prefix.'_articles';
                $boucle->where[] = array("'NOT IN'", "'L1.id_secteur'",sql_quote($exclut));  
                $boucle->join['L1'] = array('forum','id_article');

  
            }
          
            return boucle_FORUMS_dist($id_boucle, $boucles);
        }
        
        function boucle_ARTICLES($id_boucle, &$boucles) {
            
            $boucle = &$boucles[$id_boucle];
            $id_table = $boucle->id_table;
            $crit = $boucle->criteres;
           
            if(!$boucle->modificateur['tout_voir'] and !($boucle->modificateur['tout'] and lire_config('secteur/tout') == 'oui')){
                $exclut = exclure_sect_choisir($crit);       
                $boucle->where[] = array("'NOT IN'", "'$id_table.id_secteur'",sql_quote($exclut));    
            }
          
            return boucle_ARTICLES_dist($id_boucle, $boucles);
        }
        
        function boucle_RUBRIQUES($id_boucle, &$boucles) {
            
            $boucle = &$boucles[$id_boucle];
            $id_table = $boucle->id_table;
            $crit = $boucle->criteres;
            
            if(!$boucle->modificateur['tout_voir'] and !($boucle->modificateur['tout'] and lire_config('secteur/tout') == 'oui')){
                $exclut = exclure_sect_choisir($crit);       
                $boucle->where[] = array("'NOT IN'", "'$id_table.id_secteur'",sql_quote($exclut));    
            
            }
        
            return boucle_RUBRIQUES_dist($id_boucle, $boucles);
        }
        
        function boucle_SYNDICATION($id_boucle, &$boucles) {
            
            $boucle = &$boucles[$id_boucle];
            $id_table = $boucle->id_table;
            $crit = $boucle->criteres;
            
            if(!$boucle->modificateur['tout_voir'] and !($boucle->modificateur['tout'] and lire_config('secteur/tout') == 'oui')){
                $exclut = exclure_sect_choisir($crit);       
                $boucle->where[] = array("'NOT IN'", "'$id_table.id_secteur'",sql_quote($exclut));    
            
            }
        
            return boucle_SYNDICATION_dist($id_boucle, $boucles);
        }
}
?>