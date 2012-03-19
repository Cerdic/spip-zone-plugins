<?php
function bisous_affiche_gauche($flux){
    include_spip('inc/presentation');
    include_spip('public/assembler');
    
   
    if ($flux['args']['exec'] == 'auteur_infos'){
        $flux['data'] .= debut_cadre_relief('',true,'',_T('bisous:bisous_donnes')); 
        $flux['data'] .= recuperer_fond('prive/bisous_donnes',array('id_auteur'=>$flux['args']['id_auteur']));
        $flux['data'] .= fin_cadre_relief(true);
        
        $flux['data'] .= debut_cadre_relief('',true,'',_T('bisous:bisous_recus')); 
        $flux['data'] .= recuperer_fond('prive/bisous_recus',array('id_auteur'=>$flux['args']['id_auteur']));
        $flux['data'] .= fin_cadre_relief(true);
    }
    return $flux;
}

?>