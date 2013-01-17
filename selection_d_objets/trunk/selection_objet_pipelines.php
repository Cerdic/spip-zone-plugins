<?php

function selection_objet_affiche_gauche($flux) {
    include_spip('inc/config');
    $objet = $flux["args"]["exec"];
    $args=$flux['args'];
    
    $objets_selection=lire_config('selection_objet/selection_rubrique_objet',array());
    $exceptions=charger_fonction('exceptions','inc');
    $exception_objet=$exceptions('objet');
    if($exception_objet[$objet]){
         $objet=$exception_objet[$objet];
         $table='spip_'.$objet;
        }
    else $table='spip_'.$objet.'s';
    
    $contexte['id_objet']=$flux["args"]['id_'.$objet]?$flux["args"]['id_'.$objet]:_request('id_'.$objet); 

    if(in_array($objet,$objets_selection)){
        $contexte['objet']=$objet;
        $objets_cibles=lire_config('selection_objet/objets_cible',array());
        if($objet=='rubrique' OR $objet=='article'){
            $contexte['langue']=sql_getfetsel('lang',$table,'id_'.$objet.'='.$contexte['id_objet']);
            $contexte['lang'] = $contexte['langue'];
            }
        if($objet=='rubrique'){
            if (!$trad_rub=test_plugin_actif('tradrub')) $contexte['langue']=lire_config('langues_multilingue');
            elseif(!$contexte['langue']){
                if(!$trad_rub=test_plugin_actif('tradrub')) $contexte['langue']=lire_config('langues_multilingue');
                } 
            }
            foreach ($objets_cibles as $objet_dest) {
                $contexte['objet_dest']=$objet_dest;
                $flux["data"].= recuperer_fond("prive/squelettes/navigation/affiche_gauche", $contexte);
            }
             
        }
      
    return $flux;
}

function selection_objet_affiche_milieu ($vars="") {
    include_spip('inc/config');
    $objet = $vars["args"]["exec"];
    $args=$vars["args"];
    $objets_cibles=lire_config('selection_objet/objets_cible',array());

    if(in_array($objet,$objets_cibles)){
        //Les tables non conforme
        $exceptions=charger_fonction('exceptions','inc');
        $exception_objet=$exceptions('objet');
        if($exception_objet[$objet]){
             $objet=$exception_objet[$objet];
            }        
        $id_objet=$args['id_'.$objet];
        $data = $vars["data"];
        $special=array('article','rubrique');
        if(in_array($objet,$special)) $choisies= picker_selected(lire_config('selection_objet/selection_'.$objet.'_dest',array()),$objet);
        else $choisies=lire_config('selection_objet/selection_'.$objet.'_dest',array());
        
        if(in_array($id_objet,$choisies)){
           $contexte = array('id_objet_dest'=>$id_objet,'objet_dest'=>$objet);
           $vars["data"] .= recuperer_fond('prive/objets/liste/selection_interface', $contexte);
            }
        }
        return $vars;
    }

?>