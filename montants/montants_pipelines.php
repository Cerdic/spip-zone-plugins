<?php
/**
 * Insertion dans le pipeline prix_ht
 * si l'objet n'a pas de prix propre 
 * on cherche son prix dans la table montants
 *
 * @return
 * @param object $flux
 */

//ssi montant existe afficher le prix des objets sur leurs pages
function montants_affiche_milieu($flux){
	
	$pages= array('naviguer'=>'rubrique','articles'=>'article','mots_edit'=>'mot');
	foreach($pages AS $page => $objet){
		if($flux['args']['exec'] == $page) {
			$montant=sql_getfetsel("id_montant","spip_montants","objet='$objet'");
			if($montant)
			$flux['data'] .= recuperer_fond("prive/prixdefaut_$objet",
				array('id_objet'=>$flux['args']['id_'.$objet],	
				'objet'=>$objet
				));
		}
	}
	
return $flux;
}

		
function montants_prix_ht($flux){
	
	$prixdefaut=$flux['data'];
	$type_objet=$flux['args']['type_objet'];
	$id_objet=$flux['args']['id_objet'];
	$ids_objet=array();
	
	//si l'objet n'a pas de prix propre
	if ($prixdefaut==0 && $id_objet>0){
				//Trouver au moins (sinon false) un montant relatif a l'objet
				$ids_objet=sql_allfetsel('id_montant,ids_objet,prix_ht', 'spip_montants',"objet='$type_objet'");
				if(count($ids_objet)<1) {
					spip_log("return pour $type_objet",'montants'); 
					return;
				}
		
			switch ($type_objet){
				case 'article': 
					$id_rubrique=sql_getfetsel("id_rubrique","spip_articles","id_article=".$id_objet);
					//array des rubriques parentes de l'article					
					$parents[] = $id_rubrique;
					while ($id_rubrique = sql_getfetsel("id_parent","spip_rubriques","id_rubrique=" . $id_rubrique)) { 
					$parents[] = $id_rubrique;
					}
					break;
				case 'rubrique': 
					$id_rubrique=sql_getfetsel("id_rubrique","spip_rubriques","id_rubrique=".$id_objet);
					//array des rubriques parentes de la rubrique					
					$parents[] = $id_rubrique;
					while ($id_rubrique = sql_getfetsel("id_parent","spip_rubriques","id_rubrique=" . $id_rubrique)) { 
					$parents[] = $id_rubrique;
					}
					break;
				case 'mot':
					//groupe du mot !obligatoire
					$parents[] =sql_getfetsel("id_groupe","spip_mots","id_mot=".$id_objet);
					break;
				default: 
					$parents[] ='';
					break;
			}
		
	
	
		if(!$prix_ht){
		// l'objet doit appartenir a un parent spŽcifique
		$prix_ht=sql_getfetsel('prix_ht', 'spip_montants',"objet='".$type_objet."' AND le_parent IN (".join(',',$parents).")");
		//spip_log("log 0 $type_objet $id_objet prix $prix_ht parents=".join(',',$parents),'montants');
		}
	
	
	//sinon le prix est-il pour id_objet dans la liste specifique
	foreach($ids_objet as $ids){
					$array_ids = explode(",", $ids['ids_objet']);
					if(count($array_ids)<1) $id_montant=$ids['id_montant'];
						if(in_array($id_objet,$array_ids)){
						//spip_log("log 1 $type_objet $id_objet coute ".$ids['prix_ht'],'montants');
						$prix_ht=$ids['prix_ht'];
						}
						
				}
	
	if(!$prix_ht){
	// sinon son prix est declare par defaut dans spip_montants
	$prix_ht = sql_getfetsel('prix_ht', 'spip_montants','objet='."'$type_objet' AND 'id_montant' = '$id_montant'");	
	//spip_log("log 2 pour montant($id_montant) = $type_objet ($id_objet) est $prixdefaut",'montants');
	}
	
	if ($prix_ht) $flux['data']=$prix_ht;
	//si aucun prix generer une erreur manifeste = trop chere pour etre vrai a revoir?
	else $flux['data']=999.99;
	
	}
	
	return $flux;
}


/**
 *
 * Insertion dans le pipeline prix
 * si l'objet n'a pas de prix, 
 * on cherche son prix et si il existe on lui applique
 *
 * @return
 * @param object $flux
 */
function montants_prix($flux){

	/**/
	
	return $flux;
}

?>
