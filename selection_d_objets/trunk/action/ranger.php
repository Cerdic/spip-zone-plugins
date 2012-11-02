<?php

function action_ranger_dist(){
	include_spip("inc/autoriser");
	include_spip("inc/config");


    
  
	list($action,$lang,$id_objet,$objet,$objet_dest,$id_objet_dest,$load)=explode('-',_request('arg'));

	if ($action=="supprimer_ordre") {
	    $verifier_ordre=charger_fonction('verifier_ordre','inc');
	
		include_spip('formulaires/bouton_article');

		if($objet=='rubrique'){
		
			$langues=explode(",",lire_config("langues_utilisees"));
		
			foreach ($langues as $key => $langue){
			
				$where=array(
					'id_objet='.$id_objet,
					'objet="'.$objet.'"',
					'lang="'.$langue.'"',	
					'id_objet_dest="'.$id_objet_dest.'"',
					'objet_dest="'.$objet_dest.'"',												  
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
				'id_objet='.$id_objet,
				'objet='.sql_quote($objet),
				'id_objet_dest='.$id_objet_dest,
				'objet_dest='.sql_quote($objet_dest),
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
	
	if ($action=='remonter_ordre') {
	

	
		$where = array( 			
			'lang="'.$lang.'"',
			'objet_dest="'.$objet_dest.'"',
			'id_objet_dest="'.$id_objet_dest.'"',					
				);
		
		$result = sql_select("*", "spip_selection_objets", $where, "ordre");
		
		while ($row = sql_fetch($result)) {
			$id_objet_row = $row["id_objet"];
			$objet_row = $row["objet"];			
			$ordre_row = $row["ordre"];
			$lang_row = $row["lang"];		
			if ($id_objet  == $id_objet_row AND $objet_row == $objet) break;
			$ordre_new = $ordre_row;
			$id_objet_prec = $id_objet_row;
			$objet_prec = $objet_row;			
		
		}
		

		
		$where = array( 			
				"lang='$lang'",
				"objet_dest='$objet_dest'",
				"id_objet_dest='$id_objet_dest'",
				"id_objet='$id_objet'",	
				"objet='$objet'",		
				);
				

		spip_log('action '.$action.serialize($where).$ordre_new,'selecion_objet');
			
 		sql_updateq("spip_selection_objets", array("ordre" => $ordre_new), $where);
		
		$where = array( 			
				"lang='$lang'",
				"objet_dest='$objet_dest'",
				"id_objet_dest='$id_objet_dest'",
				"id_objet='$id_objet_prec'",	
				"objet='$objet_prec'",		
				);		
				
		spip_log('action '.$action.serialize($where).$ordre_row,'selecion_objet');	
		
 		sql_updateq("spip_selection_objets", array("ordre" => $ordre_row), $where);
		
	}
	
	if ($action=='descendre_ordre') {
	

	
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

	
	}
    if($load){
        $contexte = array('id_objet_dest'=>$id_objet_dest,'objet_dest'=>$objet_dest);
       echo recuperer_fond('prive/objets/liste/selection_interface',$contexte);
    } 
return $return;
}

?>
