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
			'spip_evenements.statut!="poubelle" AND spip_evenements.id_evenement_source=0',
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
								            'datas' => array('simple'=>_T('reservation:simple'),'choix_precis'=>_T('reservation:choix_precis')),
								            'label' => _T('reservation:label_type_selection'), 
								            'obligatoire'=>'oui'	                   
								        	),	                	
								    ),
								    array(
								        'saisie' => 'input',
								        'options' => array(
								            'nom' => 'nombre_evenements',
								            'label' => _T('reservation:label_nombre_evenements'),
								            'explication' => _T('reservation:explication_nombre_evenements'),
								            'defaut'=>'2',
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
								            'nom' => 'nombre_evenements_choix',
								            'label' => _T('reservation:label_nombre_evenements'),
								            'explication' => _T('reservation:explication_nombre_evenements').' '._T('reservation:explication_nombre_evenements_choix',array('objet_promotion'=>$objet_promotion)),	
								        	'afficher_si'=>'@type_selection@=="choix_precis"',
								        	'obligatoire'=>'oui',
								        	'defaut'=>'0'		                   
								        	),	                	
								    ),																			
								)	
							);
			
    return $return;
}

// Définition de l'action de la promotion  
function promotions_multiples_evenements_action_dist($flux,$promotion){
	
	//Les événements sélectionnés
	$evenements=_request('evenements');
	
	//Les données de la promotion
	$valeurs_promotion=$promotion['valeurs_promotion'];
	
	$type_selection=$valeurs_promotion['type_selection'];
	$nombre_evenements=isset($valeurs_promotion['nombre_evenements'])?$valeurs_promotion['nombre_evenements']:'';
	$objet_promotion=isset($valeurs_promotion['objet_promotion'])?$valeurs_promotion['objet_promotion']:'';		
	$id_objet=isset($valeurs_promotion['id_objet'])?$valeurs_promotion['id_objet']:'';		
	$nombre_evenements_choix=isset($valeurs_promotion['nombre_evenements_choix'])?$valeurs_promotion['nombre_evenements_choix']:'';	
	
	//promotion simple

	if($type_selection=='simple' AND count($evenements)>=$nombre_evenements)$flux['data']['applicable']='oui';
	//promotion avec choix précis des évenements
	elseif($type_selection=='choix_precis'){

		//Le nombre de conicidence requise
		//Par défaut le nombre de objets sélecctionnes
		$nombre_requis=count($id_objet);
		//Si un nombre spécifique est indiqué, on le prend
		

		$i=0;
		//Choix d'événements
		if($objet_promotion=='evenement'){
			foreach($evenements AS $id_evenement){
				if(in_array($id_evenement,$id_objet) AND in_array($flux['data']['id_evenement'],$id_objet))$i++;
				}			
			}
		//Choix d'article
		elseif($objet_promotion=='article'){
			if(!isset($flux['data']['donnees_evenements'])){
				$sql=sql_select('spip_articles.id_article,spip_articles.id_trad,id_evenement','spip_evenements LEFT JOIN spip_articles ON spip_evenements.id_article=spip_articles.id_article','spip_evenements.id_evenement IN ('.implode(',',$evenements).')');
				
				$flux['data']['donnees_evenements']=array();
				while($data=sql_fetch($sql)){
					$i=verifier($data,$id_objet,$i,$flux['data']['id_evenement']);
					$flux['data']['donnees_evenements'][]=$data;
				}				
			}
			else{
				foreach($flux['data']['donnees_evenements'] AS $data){
					$i=verifier($data,$id_objet,$i,$flux['data']['id_evenement']);
				}
			}
			
		}
		if($nombre_evenements_choix>0)$nombre_requis=$nombre_evenements_choix;

		if($i==$nombre_requis)$flux['data']['applicable']='oui';	

	}

    return $flux;
}

function verifier($data,$id_objet,$i,$id_evenement){
	$id_article=$data['id_article'];
	if($data['id_trad']>0)$id_article=$data['id_trad'];
	$id_article_evenement=sql_getfetsel('id_article','spip_evenements','id_evenement='.$id_evenement);
	$articles=array();
	if(in_array($id_article,$id_objet) AND !in_array($id_article,$articles) AND in_array($id_article_evenement,$id_objet)){
		$i++;
		$articles[]=$id_article;	
	}
	return $i;	
}

?>
