<?php


if (!defined("_ECRIRE_INC_VERSION")) return; 
      
// Définition des champs pour le détail du formulaire promotion du plugin promotions (https://github.com/abelass/promotions)          
function promotions_multiples_evenements_dist($flux=array()){

	$date=date('Y-m-d H:i:s');
	$objet_promotion=_request('objet_promotion')?_request('objet_promotion'):(isset($flux['valeurs_promotion']['objet_promotion'])?$flux['valeurs_promotion']['objet_promotion']:'');
	$objets=array();

	//Déterminer les objets à assembler
	if($objet_promotion=='evenement'){
		$sql=sql_select(
			'id_evenement,titre, date_debut,date_fin',
			'spip_evenements',
			'statut!="poubelle" AND inscription=1 AND id_evenement_source=0 AND date_fin>'.sql_quote($date),
			'','date_debut'
			);

		while($data=sql_fetch($sql)){
			$date_fin=sql_getfetsel('date_fin','spip_evenements','id_evenement_source='.$data['id_evenement'],'','date_fin DESC');
			$date_debut=$data['date_debut'];
			
			if(!$date_fin and (affdate($date_debut,'d-m-Y')<affdate($data['date_fin'],'d-m-Y'))) $date_fin='/'.affdate($data['date_fin'],'d-m-Y');
			elseif($date_debut<$date_fin) $date_fin='-'.affdate($date_fin,'d-m-Y');
			$objets[$data['id_evenement']]=$data['titre'].' - '.affdate($date_debut,'d-m-Y').$date_fin;		
			}
		}		
	elseif($objet_promotion=='article'){
		$sql=sql_select(
			'spip_evenements.id_article,spip_articles.titre',
			'spip_evenements LEFT JOIN spip_articles ON spip_evenements.id_article=spip_articles.id_article',
			'spip_evenements.statut!="poubelle" AND spip_evenements.inscription=1 AND spip_evenements.id_evenement_source=0 AND spip_evenements.date_fin>'.sql_quote($date),
			'','date_debut'
			);	

		while($data=sql_fetch($sql)){
		
			$objets[$data['id_article']]=$data['titre'];		
			}	
		}
		
	$return=array(
		'nom'=>_T('reservation:nom_reservation_multiples_evenements'),
		'saisies'=>	 array(
								    array(
								        'saisie' => 'radio',
								        'options' => array(
								            'nom' => 'type_selection',
								            'datas' => array('simple'=>_T('reservation:simple'),'choix_precis'=>_T('agenda:choix_precis')),
								            'label' => _T('reservation:label_type_selection'), 
								            'explication' => _T('reservation:explication_type_selection'), 								            
								        		'obligatoire'=>'oui'	                   
								        	),	                	
								    ),
								    array(
								        'saisie' => 'input',
								        'options' => array(
								            'nom' => 'nombre_evenements',
								            'label' => _T('reservation:label_nombre_evenements'), 						            
								            'class'=>'auto_submit',
								        		'obligatoire'=>'oui',
								        		'afficher_si'=>'@type_selection@=="simple"'		                   
								        	),	                	
								    ),								     		
								    array(
								        'saisie' => 'selection',
								        'options' => array(
								            'nom' => 'objet_promotion',
								            'datas' => array('article'=>_T('public:article'),'evenement'=>_T('agenda:info_evenement')),
								            'label' => _T('reservation:label_objet_promotion'), 
								            'explication' => _T('reservation:explication_objet_promotion'), 								            
								            'class'=>'auto_submit',
								        		'obligatoire'=>'oui',
								        		'afficher_si'=>'@type_selection@=="choix_precis"'		                   
								        	),	                	
								    ),  
									array(
												'saisie' => 'selection_multiple',
												'options' => array(
													'nom' => 'id_objet',
													'label' => _T('reservation:label_objet_'.$objet_promotion),
													'datas'=>$objets, 
													'class'=>'chosen',
													'obligatoire'=>'oui',
													'afficher_si'=>'@type_selection@=="choix_precis"'									
												)
											),
								    array(
								        'saisie' => 'input',
								        'options' => array(
								            'nom' => 'nombre_evenements_article',
								            'label' => _T('reservation:label_nombre_evenements_article'),
								            'explication' => _T('reservation:explication_nombre_evenements_article'),	
								            'class'=>'auto_submit',
								        		'afficher_si'=>'@type_selection@=="choix_precis" && @objet_promotion@=="article"'		                   
								        	),	                	
								    ),
								    array(
								        'saisie' => 'input',
								        'options' => array(
								            'nom' => 'nombre_evenements_evenement',
								            'label' => _T('reservation:label_nombre_evenements_choix'),
								            'explication' => _T('reservation:explication_nombre_evenements_choix'),	
								            'class'=>'auto_submit',
								        		'afficher_si'=>'@type_selection@=="choix_precis" && @objet_promotion@=="evenement"'		                   
								        	),	                	
								    ),																				
									)	
							);
			
    return $return;
}

// Définition de l'action de la promotion  
function promotions_multiples_evenements_action_dist($flux,$promotion){
		
	$prix_original=isset($flux['data']['prix_original'])?$flux['data']['prix_original']:'';
	
	$prix_base=isset($flux['data']['prix_base'])?$flux['data']['prix_base']:$prix_original;

	$reduction=$promotion['valeurs_promotion']['reduction'];
	$type_reduction=$promotion['valeurs_promotion']['type_reduction'];
	
	$nr_auteur=_request('nr_auteur');
	$nombre_auteurs=_request('nombre_auteurs');
	
	//Si on est en présence de la première réservation d'une réservation multiple 
	if($nombre_auteurs and !$nr_auteur){
		
			//On applique les réductions prévues
			if($type_reduction=='pourcentage')$flux['data']['prix_ht']=$prix_original-($prix_original/100*$reduction);
			elseif($type_reduction=='absolu')$flux['data']['prix_ht']=$prix_original-$reduction;
	}
		$flux['data']['objet']='reservations_detail';
		$flux['data']['table']='spip_reservations_details';			

    return $flux;
}


?>
