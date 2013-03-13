<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_instituer_objet_selectionne_dist($arg=null){
    
   if (is_null($arg)){
        $securiser_action = charger_fonction('securiser_action', 'inc');
        $arg = $securiser_action();
    }
    

    $statut='publie';
    $verifier_ordre=charger_fonction('verifier_ordre','inc');

	list($id_objet,$objet,$langue,$lang,$objet_dest,$id_objet_dest,$type_lien)=explode('-',$arg);
    
    $table = table_objet_sql($objet_dest);

    if($langue)$langue=explode(',',$langue);
    else{
        $tables=lister_tables_objets_sql();
        if($tables[$table]['field']['lang'])$langue=array(sql_getfetsel('lang','spip_'.$objet_dest.'s','id_'.$objet_dest.'='.$id_objet_dest));
        else $langue=array();
        }
;
        // si l'objet n'est pas définit par langue on l'enregistre pour chaque langue du site
        if(count($langue)>1){
        
            foreach ($langue as $key => $l){
                        
                $where = array(
                    'id_objet_dest='.$id_objet_dest,
                    'objet_dest='.sql_quote($objet_dest),
                    'lang='.sql_quote($l),  
                    );
        
                $ordre=$verifier_ordre($where);
                    
                // on rajoute comme dernier le nouveau objet    
                $ordre=$ordre+1;
            
                $vals=array(
                    'id_objet' => $id_objet,
                    'objet'=>$objet, 
                    'id_objet_dest'=>$id_objet_dest,
                    'objet_dest'=>$objet_dest,              
                    'ordre'=>$ordre, 
                    'lang'=>$l,
                    'statut'=>  $statut,
                    'type_lien'=>$type_lien
                    );
                    
                $id_selection_objet=sql_insertq("spip_selection_objets",$vals);
                }

            }
        // si objet est définit par langue on enregistre pour cette langue  
        else{
            $where = array(
                'id_objet_dest='.$id_objet_dest,
                'objet_dest='.sql_quote($objet_dest),
                'lang='.sql_quote($langue[0]),  
                );
            // on vérifie l'ordre des objets déjà enregistrés et on corrige si beselection_objetin
            
            $ordre=$verifier_ordre($where);
                
            // on rajoute comme dernier le nouveau objet            
            $ordre=$ordre+1;
            
            $vals=array(
                'id_objet' => $id_objet,
                'objet'=>$objet, 
                'id_objet_dest'=>$id_objet_dest,
                'objet_dest'=>$objet_dest,              
                'ordre'=>$ordre, 
                'lang'=>$langue[0],
                'statut'=>  $statut,
                'type_lien'=>$type_lien
                );
                    
            $id_selection_objet=sql_insertq("spip_selection_objets",$vals);
        
            }
return $id_selection_objet;
}

?>
