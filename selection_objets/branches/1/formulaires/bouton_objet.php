<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_bouton_objet_charger_dist($id_objet,$objet,$langue,$lang='',$objet_dest='rubrique') {
    include_spip('inc/config');
    include_spip('inc/presentation');    
    include_spip('inc/layer');      
     
    //Les objets destinataires choisies
     $special=array('article','rubrique');
     if(in_array($objet_dest,$special)) $choisies= picker_selected(lire_config('selection_objet/selection_'.$objet_dest.'_dest',array()),$objet_dest);
     else $choisies=lire_config('selection_objet/selection_'.$objet_dest.'_dest',array());
    
    $lang=$langue?explode(',',$langue):'';

    //On garde l'objet original pour la détection des données de l'objet
    $objet_dest_original=$objet_dest;

    $e = trouver_objet_exec($objet_dest);

    $objet_dest=$e['type']?$e['type']:$objet_dest;
    
    // Les information des objets destinataires
    $table_dest = table_objet_sql($objet_dest);
    $tables=lister_tables_objets_sql();
    $titre_objet_dest=_T($tables[$table_dest]['texte_objet']);
    
    //Préparer la requette
    $where=array();
    if(isset($tables[$table_dest]['statut'][0]['publie']))$statut=$tables[$table_dest]['statut'][0]['publie'];
    if($objet=='auteur') $where[]='statut !='.sql_quote('5poubelle');
    elseif($statut AND $objet_dest !='rubrique')  $where[]='statut='.sql_quote($statut);
    if($choisies)$where[]='id_'.$objet_dest.' IN ('.implode(',',$choisies).')';
    if($tables[$table_dest]['field']['lang'] and $lang){
        if($objet_dest!='rubrique')$where[]='lang IN ('.sql_quote($lang).')';
        elseif(test_plugin_actif('tradrub'))$where[]='lang IN ('.sql_quote($lang).')';
        }

    $objets_choisies=tableau_objet($objet_dest_original,'','*',$where);
    
    //Les types liens pour l'objet concerné
    if(!$types=lire_config('selection_objet/type_liens_'.$objet_dest_original,array()))$types=lire_config('selection_objet/type_liens',array());
    
    $types_lien=array();
    foreach($types as $cle => $valeur){
        if($valeur)$types_lien[$cle]=_T($valeur);
        }

    $valeurs = array(
    	"id_objet"	=> $id_objet,
    	"objet"	=> $objet,	
    	"langue"	=> $langue,	
    	"objet_dest"=>$objet_dest,
        "objet_dest_original"=>$objet_dest_original,
        "id_objet_dest"=>$id_objet_dest,        
        "table_dest"=>$table_dest,	
        "titre_objet_dest"=>$titre_objet_dest,
        'objets_choisies'=>$objets_choisies,
        'types_lien' =>$types_lien,      	 		
        );
        
    $valeurs['_hidden'] .= "<input type='hidden' name='id_objet' value='$id_objet' />";
    $valeurs['_hidden'] .= "<input type='hidden' name='objet' value='$objet' />";
    $valeurs['_hidden'] .= "<input type='hidden' name='lang' value='$langue' />";
    $valeurs['_hidden'] .= "<input type='hidden' name='objet_dest' value='$objet_dest' />";

    return $valeurs;
}

/* @annotation: Actualisation de la base de donnée */
function formulaires_bouton_objet_traiter_dist($id_objet,$objet,$langue,$lang='',$objet_dest='rubrique'){
    $valeurs=array();
    $id_objet_dest=_request('id_objet_dest');
    $instituer_objet=charger_fonction('instituer_objet_selectionne','action/');
    $objet_dest=_request('objet_dest');
    $type_lien=_request('type_lien');


    $id_selection_objet=$instituer_objet($id_objet.'-'.$objet.'-'.$langue.'-'.$lang.'-'.$objet_dest.'-'.$id_objet_dest.'-'.$type_lien);

	if($id_selection_objet)$valeurs['message_ok']='ok';

return $valeurs;
	
}
?>
