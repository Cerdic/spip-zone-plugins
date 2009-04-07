<?php
if (!function_exists('critere_tout_voir_dist')){
        function critere_tout_voir_dist($idb, &$boucles, $crit) {   
            $boucle = &$boucles[$idb];
            $boucle->modificateur['tout_voir'] = true;
    }
}
function secteur_explicite($crit){
    
    foreach($crit as $critere){   
        if ($critere->param[0][0]->texte == 'id_secteur' and $critere->not!='!') {
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
