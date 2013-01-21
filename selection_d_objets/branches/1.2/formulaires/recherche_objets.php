<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_recherche_objets_charger_dist($objet_dest='rubrique',$id_objet_dest,$lang='') {
    include_spip('inc/config');
    //Les objets destinataires choisies
     $special=array('article','rubrique');
     if(in_array($objet_dest,$special)) $choisies= picker_selected(lire_config('selection_objet/selection_'.$objet_dest.'_dest',array()),$objet_dest);
     else $choisies=lire_config('selection_objet/selection_'.$objet_dest.'_dest',array());
    
    $lang=$langue?explode(',',$langue):'';
    
    //Quelques objets ne sont pas conforme, on adapte
    $exceptions=charger_fonction('exceptions','inc');
    $exception_objet=$exceptions('objet');
    
    //On grade l'objet original pour la détection des données de l'objet
    $objet_dest_original=$objet_dest;
    
    if($exception_objet[$objet_dest]){
         $objet_dest=$exception_objet[$objet_dest];
          $table_dest='spip_'.$objet_dest;
    }
    else $table_dest='spip_'.$objet_dest.'s';
    
    if($exception_objet[$objet]){
         $objet=$exception_objet[$objet];
        }
    
    // Les information des objets destinataires
    $tables=lister_tables_objets_sql();
    $titre_objet_dest=_T($tables[$table_dest]['texte_objet']);
    $where='id_'.$objet_dest.' IN ('.implode(',',$choisies).')';
    $where_lang='';
    if($tables[$table_dest]['field']['lang'] and $lang)$where_lang=' AND lang IN ('.sql_quote($lang).')';

    if($choisies)$objets_choisies=tableau_objet($objet_dest_original,'','*',$where.$where_lang,array('titre','id_'.$objet_dest,true));
    
    //Les types liens pour l'objet concerné
    if(!$types=lire_config('selection_objet/type_liens_'.$objet_dest_original,array()))$types=lire_config('selection_objet/type_liens',array());
    
    
    $types_lien=array();
    foreach($types as $cle => $valeur){
        $types_lien[$cle]=_T($valeur);
        }
    $valeurs = array(
    	"id_objet"	=> $id_objet,
    	"objet"	=> $objet,	
    	"langue"	=> $langue,	
    	"objet_dest"=>$objet_dest,
        "id_objet_dest"=>$id_objet_dest,
        "table_dest"=>$table_dest,	
        "titre_objet_dest"=>$titre_objet_dest,
        'objets_choisies'=>$objets_choisies,
        'types_lien' =>$types_lien, 
        'objet_sel' =>'',              	 		
        );
        
    $valeurs['_hidden'] .= "<input type='hidden' name='id_objet' value='$id_objet' />";
    $valeurs['_hidden'] .= "<input type='hidden' name='objet' value='$objet' />";
    $valeurs['_hidden'] .= "<input type='hidden' name='lang' value='$langue' />";
    $valeurs['_hidden'] .= "<input type='hidden' name='objet_dest' value='$objet_dest' />";



    return $valeurs;
}



/* @annotation: Actualisation de la base de donnée */
function formulaires_recherche_objets_traiter_dist($objet_dest='rubrique',$id_objet_dest,$lang=''){
    $type_lien=_request('type_lien');    
        
    $instituer_objet=charger_fonction('instituer_objet_selectionne','action/');
    
    list($id_objet,$objet)=explode('-',_request('objet_sel'));
    
    $id_selection_objet=$instituer_objet($id_objet.'-'.$objet.'-'.$langue.'-'.$lang.'-'.$objet_dest.'-'.$id_objet_dest.'-'.$type_lien);
    
    if($id_selection_objet)$valeurs['message_ok']='ok';

return $valeurs;
	
}
?>
