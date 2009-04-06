<?php
global $spip_version_branche;
if (eregi("^1",$spip_version_branche)){
        if (!function_exists('critere_tout_secteur_dist')){
        function critere_tout_secteur_dist($idb, &$boucles, $crit) {   
            $boucle = &$boucles[$idb];
            $boucle->modificateur['tout_secteur'] = true;
        }
        }
        function secteur_explicite($crit){
            
            foreach($crit as $critere){   
                if ($critere->param[0][0]->texte == 'id_secteur') {
                        switch ($critere->op){
                            case '=' :
                                return true;
                            case '==':
                                return true;
                            case 'IN':
                                return true;                
                        }                   
                }
            }
            return false;
        }
        
        function exclure_sect_choisir($crit){
            $cfg =lire_config('secteur/exclure_sect');
            $sect_afficher = secteur_explicite($crit);
            if ($cfg = array_map('sql_quote', $cfg) and !$sect_afficher ) {
                $cfg = implode($cfg, ',');
            }
            else {
                $cfg = sql_quote('z');
            }
            
           
            return '('.$cfg.')';
        
        }
        /* surcharges de boucles*/
        function boucle_BREVES($id_boucle, &$boucles) {
            
            $boucle = &$boucles[$id_boucle];
            $id_table = $boucle->id_table;
            $crit = $boucle->criteres;
            
            if(!$boucle->modificateur['tout_secteur'] and !($boucle->modificateur['tout'] and lire_config('secteur/tout') == 'oui')){
                $exclut = exclure_sect_choisir($crit);       
                $boucle->where[] = array("'NOT IN'", "'$id_table.id_secteur'",sql_quote($exclut));        
            }
        
            return boucle_BREVES_dist($id_boucle, $boucles);
        }
        
        
        
        function boucle_ARTICLES($id_boucle, &$boucles) {
            
            $boucle = &$boucles[$id_boucle];
            $id_table = $boucle->id_table;
            $crit = $boucle->criteres;
           
            if(!$boucle->modificateur['tout_secteur'] and !($boucle->modificateur['tout'] and lire_config('secteur/tout') == 'oui')){
                $exclut = exclure_sect_choisir($crit);       
                $boucle->where[] = array("'NOT IN'", "'$id_table.id_secteur'",sql_quote($exclut));    
            }
          
            return boucle_ARTICLES_dist($id_boucle, $boucles);
        }
        
        function boucle_RUBRIQUES($id_boucle, &$boucles) {
            
            $boucle = &$boucles[$id_boucle];
            $id_table = $boucle->id_table;
            $crit = $boucle->criteres;
            
            if(!$boucle->modificateur['tout_secteur'] and !($boucle->modificateur['tout'] and lire_config('secteur/tout') == 'oui')){
                $exclut = exclure_sect_choisir($crit);       
                $boucle->where[] = array("'NOT IN'", "'$id_table.id_secteur'",sql_quote($exclut));    
            
            }
        
            return boucle_RUBRIQUES_dist($id_boucle, $boucles);
        }
        
        function boucle_SYNDICATION($id_boucle, &$boucles) {
            
            $boucle = &$boucles[$id_boucle];
            $id_table = $boucle->id_table;
            $crit = $boucle->criteres;
            
            if(!$boucle->modificateur['tout_secteur'] and !($boucle->modificateur['tout'] and lire_config('secteur/tout') == 'oui')){
                $exclut = exclure_sect_choisir($crit);       
                $boucle->where[] = array("'NOT IN'", "'$id_table.id_secteur'",sql_quote($exclut));    
            
            }
        
            return boucle_SYNDICATION_dist($id_boucle, $boucles);
        }
}
?>