<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function verifier_ordre($where){
	$result_num = sql_select("*","spip_selection_objets", $where,'', "ordre,id_objet");
	$ordre = 0;
				
	// on vérifie l'ordre des objets déjà enregistrés et on corrige si besoin
				
	while ($row = sql_fetch($result_num)) {
		$ordre++;
		$where = array(
			'id_objet='.$row['id_objet'],					
			'id_objet_dest='.$row['id_objet_dest'],
			'objet_dest="'.$row['objet_dest'].'"',
			'lang="'.$row['lang'].'"',	
			);

		sql_updateq("spip_selection_objets",array("ordre" => $ordre),$where) ;
		}
		
	return $ordre;
}

function formulaires_bouton_article_charger_dist($id_objet,$objet,$langue='') {
    $valeurs = array(
	"id_objet"	=> $id_objet,
	"objet"	=> $objet,	
	"langue"	=> $langue,		 		
    );
    $valeurs['_hidden'] .= "<input type='hidden' name='id_objet' value='$id_objet' />";
    $valeurs['_hidden'] .= "<input type='hidden' name='objet' value='$objet' />";
    $valeurs['_hidden'] .= "<input type='hidden' name='lang' value='$langue' />";
    
    $where=array(
    	'id_objet='.$id_objet,
       	'objet="'.$objet.'"', 		
    	);
    	
	if($id_objet_dest){
		$where['id_objet_dest'] =_request('id_objet_dest');
		$where['objet_dest'] =_request('objet_dest');					
		}
		
	
        
   	$sql= sql_fetsel('lang','spip_selection_objets',$where);
    
  	if($sql= sql_fetsel('lang','spip_selection_objets',$where)) $langues =  explode($sql['lang'],',');
    
   	if(is_array($langues) AND in_array($langue,$langues))$valeurs['selectionne']='ok';
   	elseif($sql)$valeurs['selectionne']='ok';

    return $valeurs;
}



/* @annotation: Actualisation de la base de donnée */
function formulaires_bouton_article_traiter_dist($id_objet,$objet,$langue=''){
	$id_objet_dest=_request('id_objet_dest');
	$objet_dest=_request('objet_dest');
	
	if(!$id_objet_dest){
		$id_objet_dest ='0';
		$objet_dest ='-';				
		}

	if (_request('save')){
			
		// si objet pas définit par langue on enrgistre pour chaque langue du site
		if(is_array($langue)){
		
			foreach ($langue as $key => $langue){
						
				$where = array(
					'id_objet_dest='.$id_objet_dest,
					'objet_dest="'.$objet_dest.'"',
					'lang="'.$langue.'"',	
					);
		
				$ordre=verifier_ordre($where);
					
				// on rajoute comme dernier le nouveau objet	
				$ordre=$ordre+1;
			
				$valeurs=array(
					'id_objet' => $id_objet,
					'objet'=>$objet, 
					'id_objet_dest'=>$id_objet_dest,
					'objet_dest'=>$objet_dest,			 	
					'ordre'=>$ordre, 
					'lang'=>$langue
					);
					
				sql_insertq("spip_selection_objets",$valeurs);
				}

			}
		// si objet est définit par langue on enregistre pour cette langue	
		else{
			$where = array(
				'id_objet_dest='.$id_objet_dest,
				'objet_dest="'.$objet_dest.'"',
				'lang="'.$langue.'"',	
				);
			// on vérifie l'ordre des objets déjà enregistrés et on corrige si besoin
			
			$ordre=verifier_ordre($where);
				
			// on rajoute comme dernier le nouveau objet			
			$ordre=$ordre+1;
			
			$valeurs=array(
				'id_objet' => $id_objet,
				'objet'=>$objet, 
				'id_objet_dest'=>$id_objet_dest,
				'objet_dest'=>$objet_dest,			 	
				'ordre'=>$ordre, 
				'lang'=>$langue
				);
					
			sql_insertq("spip_selection_objets",$valeurs);
		
			}
			
			
			


			}
	elseif(_request('delete')){
	
		if(is_array($langue)){
			foreach ($langue as $key => $langue){
				$where=array(
					'id_objet='.$id_objet,
					'objet="'.$objet.'"',
					'lang="'.$langue.'"',	
					'id_objet_dest="'.$id_objet_dest.'"',
					'objet_dest="'.$objet_dest.'"',												  
					);
							
				sql_delete("spip_selection_objets",$where);
					
				// on vérifie l'ordre des objets déjà enregistrés et on corrige si besoin
				
				$where = array(
				'id_objet_dest='.$id_objet_dest,
				'objet_dest="'.$objet_dest.'"',
				'lang="'.$langue.'"',	
				);
				
				$ordre=verifier_ordre($where);	
				}
			}
		else{
			$where=array(
				'id_objet='.$id_objet,
				'objet="'.$objet.'"',
				'lang="'.$langue.'"',	
				'id_objet_dest="'.$id_objet_dest.'"',
				'objet_dest="'.$objet_dest.'"',
				);
							
			sql_delete("spip_selection_objets",$where);
					
			// on vérifie l'ordre des objets déjà enregistrés et on corrige si besoin
				
			$where = array(
				'id_objet_dest='.$id_objet_dest,
				'objet_dest="'.$objet_dest.'"',
				'lang="'.$langue.'"',	
				);
				
			verifier_ordre($where);	
			}
		}
	
}
?>