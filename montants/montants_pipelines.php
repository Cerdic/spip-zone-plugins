<?php
/**
 * todo faire descriptif ou 'commentaire' champ supp pour montants
 * Insertion dans le pipeline prix_ht
 * si l'objet n'a pas de prix, 
 * on cherche son prix et si il existe on lui applique
 *
 * @return
 * @param object $flux
 */
 
function montants_affiche_milieu($flux){
	if($flux['args']['exec'] == 'naviguer') {
		$affiche_prixdefaut = recuperer_fond('prive/prixdefaut_rubrique',
			array('id_objet'=>$flux['args']['id_rubrique'],	
			'objet'=>'rubrique'
			));
		$flux['data'] .= $affiche_prixdefaut;
	}
	
	if($flux['args']['exec'] == 'articles') {
		$affiche_prixdefaut = recuperer_fond('prive/prixdefaut_article',
			array('id_objet'=>$flux['args']['id_article'],	
			'objet'=>'article'
			));
		$flux['data'] .= $affiche_prixdefaut;
	}

	if($flux['args']['exec'] == 'mots_edit') {
		$affiche_prixdefaut = recuperer_fond('prive/prixdefaut_mot',
			array('id_objet'=>$flux['args']['id_mot'],	
			'objet'=>'mot'
			));
		$flux['data'] .= $affiche_prixdefaut;
	}
	return $flux;
}

//passer tout a en squelettes?
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
					//groupe du mot
					$parents[] =sql_getfetsel("id_groupe","spip_mots","id_mot=".$id_objet);
					break;
				default: 
					$parents[] ='';
					break;
			}
		
		// l'objet doit appartenir a un parent spŽcifique
		$prix_ht=sql_getfetsel('prix_ht', 'spip_montants',"objet='".$type_objet."' AND le_parent IN (".join(',',$parents).")");
		spip_log("$type_objet $id_objet prix $prix_ht parents=".join(',',$parents),'montants');
	}
	
	//sinon le prix est-il pour id_objet dans la liste specifique
	foreach($ids_objet as $ids){
					$array_ids = explode(",", $ids['ids_objet']);
					if(count($array_ids)<1) $id_montant=$ids['id_montant'];
						if(in_array($id_objet,$array_ids)){
						spip_log("HT SPECIAL $type_objet $id_objet coute ".$ids['prix_ht'],'montants');
						$prix_ht=$ids['prix_ht'];
						}
						
				}
	
	if(!$prix_ht){
	// sinon son prix est declare par defaut dans spip_montants
	$prixdefaut = sql_getfetsel('prix_ht', 'spip_montants','objet='."'$type_objet' AND 'id_montant' = '$id_montant'");
	$prix_ht=$prixdefaut['prix_ht'];
	
	spip_log("HT defaut pour $type_objet ($id_objet) est $prixdefaut",'montants');
	}
	
	if ($prixdefaut==0 && $prix_ht) {
		$flux['data']=$prix_ht;
	}
	
	//erreur manifeste = trop chere pour etre vrai
	if(!$flux['data']) $flux['data']=999.99;
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
