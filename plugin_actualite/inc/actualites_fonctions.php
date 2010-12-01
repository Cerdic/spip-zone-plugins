<?php

// utilisé dans action/editer_objets
//permet de récupérer les liaisons d'un objet du plugin avec les autres objets  
function actualites_get_parents($id_actualite,$serveur=''){
	if(!$id_actualite || $id_actualite=="new" || $id_actualite=="oui") return array();
	
	$where = "id_actualite=".$id_actualite;
	$id_parents = sql_allfetsel(array("id_objet","objet"),"spip_actualites_liens",$where,"","","","",$serveur);

	$retour=array();
	foreach ($id_parents as $ligne) {
		$retour[]=$ligne['objet']."|".$ligne['id_objet'];
	}
	
	return $retour;
}


// utilisé dans action/editer_objets
//permet de d'associer les objets du plugin avec les autres objets  
function actualites_set_parents($objet,$id_objet,$id_parents,$serveur=''){
	
	//reprise d'une grosse partie du code de polyhierarchie de la fonction du même nom
  if (is_string($id_parents))
		$id_parents = explode(',',$id_parents);
	if (!is_array($id_parents))
		$id_parents = array();
		
	$nom_objet='actualite';	

	$id_parents = array_unique($id_parents);

	$changed = array('remove'=>array(),'add'=>array());

	//on va modifier le tableau des parents pour avoir $tableau['articles']=array('1','2');
	//on pourra plus facilement faire les requetes par la suite
	$parents=array();
	foreach ($id_parents as $parent) {
		$parent = explode("|",$parent);
		$parents[reset($parent)][]= intval(end($parent));
	}
	
	
	$where = "id_".$nom_objet."=".intval($id_objet);
	// supprimer les anciens parents plus utilises
	// en les notant auparavant
	
	
	$ins = array();
	foreach ($parents as $type_objet=>$tab_ids){
		//sur chaque type d'objet
		//on va supprimer les liens qui n'existent plus
		$changed['remove'] = sql_allfetsel("id_".$nom_objet,"spip_".$objet."_liens","$where AND objet=".sql_quote($type_objet)." AND ".sql_in('id_objet',$tab_ids,"NOT",$serveur),$serveur);
		$changed['remove'] = array_map('reset',$changed['remove']);
		sql_delete("spip_".$objet."_liens","$where AND objet=".sql_quote($type_objet)." AND ".sql_in('id_objet',$tab_ids,"NOT",$serveur),$serveur);
		
		// selectionner l'intersection entre base et tableau
		$restants = sql_allfetsel('id_'.$nom_objet,"spip_".$objet."_liens","$where AND objet=".sql_quote($type_objet)." AND ".sql_in('id_objet',$tab_ids,"",$serveur),"","","","",$serveur);
		$restants = array_map('reset',$restants);
	
		$tab_ids = array_diff($tab_ids,$restants);
		
		foreach($tab_ids as $p){
			if ($p) {
				$ins[] = array('id_objet'=>$p,'id_'.$nom_objet=>$id_objet,'objet'=>$type_objet);
				$changed['add'][] = $p;
			}
		}
	}
	if (count($ins))
			sql_insertq_multi("spip_".$objet."_liens",$ins,"",$serveur);

	
	

	return $changed;
}

?>