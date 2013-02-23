<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function selection_objet_affiche_gauche($flux) {
    include_spip('inc/config');
    $objet = $flux["args"]["exec"];
    /*Desactivé car il y a u problème  avec les cadres et block_depliables dans l'inclure
     * //Exception pour les documents
    if($objet=='document_edit')$objet='document' ;    */
    $args=$flux['args'];
    
    $objets_selection=lire_config('selection_objet/selection_rubrique_objet',array());
    $exceptions=charger_fonction('exceptions','inc');
    $exception_objet=$exceptions('objet');
    $objet_ancien=$objet;
    if($exception_objet[$objet]){
         $objet=$exception_objet[$objet];
         $table='spip_'.$objet;
        }
    else $table='spip_'.$objet.'s';
    
    $contexte['id_objet']=$flux["args"]['id_'.$objet]?$flux["args"]['id_'.$objet]:_request('id_'.$objet); 
    if(in_array($objet_ancien,$objets_selection)){
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
        $contexte['objet_dest']=$objet_dest;
        $contexte['objets_cibles']=$objets_cibles;                
        $flux["data"].= recuperer_fond("prive/squelettes/navigation/affiche_gauche", $contexte);
        }
      
    return $flux;
}

function selection_objet_affiche_milieu ($flux="") {
    include_spip('inc/config');
    $objet = $flux["args"]["exec"];
    //Exception pour les documents
    if($objet=='document_edit')$objet='document' ;    
    $args=$flux["args"];
    $objets_cibles=lire_config('selection_objet/objets_cible',array());
    


    if(in_array($objet,$objets_cibles)){
        //Les tables non conforme
        $exceptions=charger_fonction('exceptions','inc');
        $exception_objet=$exceptions();
        if($exception_objet['objet'][$objet]){
             $objet=$exception_objet['objet'][$objet];
             $table='spip_'.$objet;
            } 
       else $table='spip_'.$objet.'s';  
       if(!$champ_titre=$exception_objet['titre'][$objet]) $champ_titre='titre'; 
                
        $id_objet=$args['id_'.$objet];
        if($objet=='site')$id_objet=$args['id_syndic'];
        $data = $flux["data"];
        $special=array('article','rubrique');
        if(in_array($objet,$special)) $choisies= picker_selected(lire_config('selection_objet/selection_'.$objet.'_dest',array()),$objet);
        else $choisies=lire_config('selection_objet/selection_'.$objet.'_dest',array());
        
        if(in_array($id_objet,$choisies) OR !$choisies){
           $contexte = array('id_objet_dest'=>$id_objet,'objet_dest'=>$objet,'champ_titre'=>$champ_titre);
            $tables=lister_tables_objets_sql();
           if($tables[$table]['field']['lang']) $contexte['langue']=array(sql_getfetsel('lang',$table,'id_'.$objet.'='.$id_objet));
           elseif($objet!='document') $contexte['langue']=array($args['lang']);
           else $contexte['langue']=array();
        if($objet=='rubrique'){
            if (!$trad_rub=test_plugin_actif('tradrub')) $contexte['langue']=explode(',',lire_config('langues_multilingue'));
            }
           $flux["data"] .= recuperer_fond('prive/objets/liste/selection_interface', $contexte);
            }
        }
        return $flux;
    }

function selection_objet_formulaire_charger($flux){
    $form=$flux['args']['form'];
    if($form == 'configurer_selection_objet'){
 //emprunté de a2a  preparé les type_liens pour le formulaire     
    include_spip('inc/config');
    if(!$cfg['type_liens']=_request('type_liens')){
        $types_lien=lire_config('selection_objet/type_liens',array());
            $flux['data']['type_liens']='';
            foreach ($types_lien as $key => $value) {
                if($key)$flux['data']['type_liens'].="$key,$value\n";
            }
        
    }

    //également les  type_liens_OBJET   
 
    $objets_cibles=lire_config('selection_objet/objets_cible',array());
    
    foreach($objets_cibles as $objet){
        if($types_lien=lire_config('selection_objet/type_liens_'.$objet)){
            $flux['data']['type_liens_'.$objet]='';
            foreach ($types_lien as $key => $value) {
                if($key)$flux['data']['type_liens_'.$objet].="$key,$value\n";
                }
            }
            
        }
    }
    return $flux;    
}


function selection_objet_formulaire_traiter($flux){
    // Si on est sur le formulaire client qui est sur la page identification
    $form=$flux['args']['form'];
    if($form == 'configurer_selection_objet'){
         include_spip('inc/config');
        $cfg=lire_config('selection_objet');
        $cfg['type_liens']  = types_liaisons2array(_request('type_liens'));
        
    if(!$objets_cibles=$cfg['objets_cible'])$objets_cibles=array();
    
    foreach($objets_cibles as $objet){
        if(_request('type_liens_'.$objet))$cfg['type_liens_'.$objet]  = types_liaisons2array(_request('type_liens_'.$objet));
        }
        
    ecrire_config('selection_objet',$cfg);
    }
    return $flux;
}

function selection_objet_jqueryui_plugins($scripts){
   $scripts[] = 'jquery.ui.autocomplete';
   $scripts[] = "jquery.ui.widget";
   $scripts[] = "jquery.ui.mouse";
   $scripts[] = "jquery.ui.sortable";
   return $scripts;
}

function types_liaisons2array($type){
    $tableau    = array();
    $lignes     = explode("\n",$type);
    foreach ($lignes as $l){
        $donnees= explode(',',$l);
        if ($donnees[1])
            $tableau[trim($donnees[0])] = trim ($donnees[1]);
        else
            $tableau[trim($donnees[0])] = '';
    }

    return $tableau;
}
?>