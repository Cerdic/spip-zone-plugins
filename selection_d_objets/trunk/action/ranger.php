<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_ranger_dist($arg=null){
    if (is_null($arg)){
        $securiser_action = charger_fonction('securiser_action', 'inc');
        $arg = $securiser_action();
    }   
     $verifier_ordre=charger_fonction('verifier_ordre','inc');
	include_spip("inc/autoriser");
	include_spip("inc/config");

	list($action,$lang,$id_selection_objet,$objet_dest,$id_objet_dest,$load,$nouvel_ordre)=explode('-',$arg);

    switch($action){
        case 'supprimer_ordre' :

            include_spip('formulaires/bouton_article');
    
            if($objet=='rubrique'){
            
                $langues=explode(",",lire_config("langues_utilisees"));
            
                foreach ($langues as $key => $langue){
                
                    $where=array(
                        'id_selection_objet='.$id_selection_objet,                                             
                        );
                                
                    sql_delete("spip_selection_objets",$where);
                        
                    // on vérifie l'ordre des objets déjà enregistrés et on corrige si beselection_objetin
                    
                    $where = array(
                    'id_objet_dest='.$id_objet_dest,
                    'objet_dest='.sql_quote($objet_dest),
                    'lang='.sql_quote($lang)
                    );
                    
                    $ordre=$verifier_ordre($where); 
                    }
                }
            else{
            
            spip_log('eliminer 1','selection');
                $where=array(
                    'id_selection_objet='.$id_selection_objet,
                    );
                                            
                sql_delete("spip_selection_objets",$where);
                        
                // on vérifie l'ordre des objets déjà enregistrés et on corrige si beselection_objetin
                    
                $where = array(
                    'id_objet_dest='.$id_objet_dest,
                    'objet_dest='.sql_quote($objet_dest),
                    'lang='.sql_quote($lang)    
                    );
                    
                $ordre=$verifier_ordre($where); 
                }
            break;
        case 'remonter_ordre':
            $where = array(             
            'lang="'.$lang.'"',
            'objet_dest="'.$objet_dest.'"',
            'id_objet_dest="'.$id_objet_dest.'"',                   
                );
        
            $result = sql_select("*", "spip_selection_objets", $where, "ordre");
            
            while ($row = sql_fetch($result)) {
                $id_selection_objet_row = $row["id_selection_objet"];        
                $ordre_row = $row["ordre"];
                $lang_row = $row["lang"];       
                if ($id_selection_objet  == $id_selection_objet_row) break;
                $ordre_new = $ordre_row;
                $id_selection_objet_prec = $id_selection_objet_row;       
            
            }

            $where = array(             
                    "id_selection_objet='$id_selection_objet'",      
                    );
                    
    
            spip_log('action '.$action.serialize($where).$ordre_new,'selecion_objet');
                
            sql_updateq("spip_selection_objets", array("ordre" => $ordre_new), $where);
            
            $where = array(             
                    "id_selection_objet='$id_selection_objet_prec'",       
                    );      
                    
            spip_log('action '.$action.serialize($where).$ordre_row,'selecion_objet');  
            
            sql_updateq("spip_selection_objets", array("ordre" => $ordre_row), $where);
            break;
                case 'descendre_ordre': 
                $where = array(             
                'lang="'.$lang.'"',
                'objet_dest="'.$objet_dest.'"',
                'id_objet_dest="'.$id_objet_dest.'"',
                "id_objet='$id_objet'", 
                "objet='$objet'",                       
                );
        
    
            $result = sql_select("*", "spip_selection_objets",$where, "ordre");
            
            if ($row = sql_fetch($result)) {
    
                $ordre = $row["ordre"];
                
                $where = array(             
                    'lang="'.$lang.'"',
                    'objet_dest="'.$objet_dest.'"',
                    'id_objet_dest="'.$id_objet_dest.'"',
                    'ordre>"'.$ordre.'"',                   
                    );
                
                $result2 = sql_select("*", "spip_selection_objets",$where, "ordre LIMIT 0,1");
                
                    if ($row2 = sql_fetch($result2)) {
                        $ordre_suiv = $row2["ordre"];
                        $id_objet_suiv = $row2["id_objet"];
                        $objet_suiv = $row2["objet"];                   
                        
                        $where = array(             
                            "lang='$lang'",
                            "objet_dest='$objet_dest'",
                            "id_objet_dest='$id_objet_dest'",
                            "id_objet='$id_objet'", 
                            "objet='$objet'",       
                            );
                        
    
                        sql_updateq("spip_selection_objets", array("ordre" => $ordre_suiv),$where);
    
                        
                        $where = array(             
                            "lang='$lang'",
                            "objet_dest='$objet_dest'",
                            "id_objet_dest='$id_objet_dest'",
                            "id_objet='$id_objet_suiv'",    
                            "objet='$objet_suiv'",      
                            );
                        
                        sql_updateq("spip_selection_objets", array("ordre" => $ordre),$where);
                        }
                }
            break;
        case 'nouvel_ordre':
            $nouvel_ordre=explode(',',_request('nouvel_ordre'));
            $ordre=0;
            foreach($nouvel_ordre AS $id_objet){
                $ordre++;
                sql_updateq("spip_selection_objets", array("ordre" => $ordre),'id_objet='.$id_objet);
                include_spip('inc/invalideur');
                suivre_invalideur("id='selection_objet/$id_selection_objet'");    
            }
            break;
        }

    if($load){
       $contexte = array('id_objet_dest'=>$id_objet_dest,'objet_dest'=>$objet_dest,'l'=>$lang);
        echo recuperer_fond('prive/objets/liste/inc-selection_interface',$contexte);
    } 
return $return;
}

?>
