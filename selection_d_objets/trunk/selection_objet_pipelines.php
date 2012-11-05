<?php

function selection_objet_affiche_gauche($flux) {
    include_spip('inc/config');
    
    $exec = $flux["args"]["exec"];
    $contexte=array();
    $objets=lire_config('selection_objet/selection_rubrique_objet',array());
    $args=$flux['args'];
    $objet_contexte=$args['exec'];
    $contexte['objet_dest']='rubrique';
    foreach($objets AS $objet){
        if($objet_contexte==$objet){
            $contexte['objet']=$objet;
            $contexte['id_objet']=$flux["args"]['id_'.$objet]?$flux["args"]['id_'.$objet]:_request('id_'.$objet); 
            
            $contexte['langue']=sql_getfetsel('lang','spip_'.$objet.'s','id_'.$objet.'='.$contexte['id_objet']);
            $contexte['lang'] = $contexte['langue'];
            if ($objet=='rubrique' AND !$trad_rub=test_plugin_actif('tradrub')) $contexte['langue']=lire_config('langues_multilingue');
            elseif(!$contexte['langue']){
                if(!$trad_rub=test_plugin_actif('tradrub')) $contexte['langue']=lire_config('langues_multilingue');
               
            } 

            $fond = recuperer_fond("prive/squelettes/navigation/affiche_gauche", $contexte);
            $flux["data"] .= $fond;
            }
        }
      
    return $flux;
}

function selection_objet_affiche_milieu ($vars="") {
include_spip('inc/config');
    $exec = $vars["args"]["exec"];
   
    $id_rubrique = $vars["args"]["id_rubrique"];
        if (!$id_rubrique)$id_rubrique=0;
        $id_article = $vars["args"]["id_article"];
        $data = $vars["data"];
        
        $active = picker_selected(lire_config('selection_objet/selection_rubrique_dest'),'rubrique');

        if ($exec == "rubrique" && in_array($id_rubrique,$active)) {
            include_spip("inc/utils");
            $contexte = array('id_objet_dest'=>$id_rubrique,'objet_dest'=>'rubrique');

            $page = recuperer_fond('prive/objets/liste/selection_interface', $contexte);
        }

        $data .= $ret;
    
        $vars["data"] .=$page;
        return $vars;
    }

?>