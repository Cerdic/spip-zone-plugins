<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_bouton_objet_charger_dist($id_objet,$objet,$langue,$lang='',$objet_dest='rubrique') {
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
        );
        
    $valeurs['_hidden'] .= "<input type='hidden' name='id_objet' value='$id_objet' />";
    $valeurs['_hidden'] .= "<input type='hidden' name='objet' value='$objet' />";
    $valeurs['_hidden'] .= "<input type='hidden' name='lang' value='$langue' />";
    $valeurs['_hidden'] .= "<input type='hidden' name='objet_dest' value='$objet_dest' />";



    return $valeurs;
}



/* @annotation: Actualisation de la base de donnée */
function formulaires_bouton_objet_traiter_dist($id_objet,$objet,$langue,$lang='',$objet_dest='rubrique'){

    $valeurs='';
    $id_objet_dest=_request('id_objet_dest');
    $verifier_ordre=charger_fonction('verifier_ordre','inc');
    $statut='publie';
    $objet_dest=_request('objet_dest');
    $type_lien=_request('type_lien');
	


	if($langue)$langue=explode(',',$langue);
	else $langue=array(0=>sql_getfetsel('lang','spip_'.$objet_dest.'s','id_'.$objet_dest.'='.$id_objet_dest));


		// si objet pas définit par langue on enrgistre pour chaque langue du site
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
					
				sql_insertq("spip_selection_objets",$vals);
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
					
			sql_insertq("spip_selection_objets",$vals);
		
			}
			
			
			$valeurs['message_ok']='ok';

return $valeurs;
	
}
?>
