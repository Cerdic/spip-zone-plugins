<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_bouton_objet_charger_dist($id_objet,$objet,$langue,$lang='',$objet_dest='rubrique') {
    
    $valeurs = array(
    	"id_objet"	=> $id_objet,
    	"objet"	=> $objet,	
    	"langue"	=> $langue,	
    	"objet_dest"=>$objet_dest,
        "id_objet_dest"=>$id_objet_dest,		 		
        );
    $valeurs['_hidden'] .= "<input type='hidden' name='id_objet' value='$id_objet' />";
    $valeurs['_hidden'] .= "<input type='hidden' name='objet' value='$objet' />";
    $valeurs['_hidden'] .= "<input type='hidden' name='lang' value='$langue' />";
    $valeurs['_hidden'] .= "<input type='hidden' name='objet_dest' value='$objet_dest' />";

    
    $where=array(
    	'id_objet='.$id_objet,
       	'objet='.sql_quote($objet), 		
    	);
    	
	if($id_objet_dest){
		$where['id_objet_dest'] =_request('id_objet_dest');
		$where['objet_dest'] =_request('objet_dest');					
		}
		
	if($lang)$where[2]='lang='.sql_quote($lang);	
	
        
   	$l= sql_getfetsel('lang','spip_selection_objets',$where);
   	
	$langues=explode(',',$langue);
	

	if(in_array($l,$langues))$valeurs['selectionne']='ok';

    return $valeurs;
}



/* @annotation: Actualisation de la base de donnée */
function formulaires_bouton_objet_traiter_dist($id_objet,$objet,$langue,$lang='',$objet_dest='rubrique'){

    $valeurs='';
    $id_objet_dest=_request('id_objet_dest');
    $verifier_ordre=charger_fonction('verifier_ordre','inc');
    $statut='publie';
	
	/*if(!$id_objet_dest){
		$id_objet_dest ='0';
		$objet_dest ='-';				
		}*/

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
					'statut'=>  $statut
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
				'statut'=>  $statut
				);
					
			sql_insertq("spip_selection_objets",$vals);
		
			}
			
			
			

return $valeurs;
	
}
?>
