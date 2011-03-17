<?php

function definitions($use='',$valeurs=''){

	//Définition des fieldset

	$fieldsets=array(
		0=>array(
			'titre'=>_T('gestpro:infos_obligatoires'),
			'id'=>'obligatoire'
			),
		1=>array(
			'titre'=>_T('gestpro:options_avancees'),
			'id'=>'avance'
			),			
		);	
		

	//Définition des champs

	$champs=array(
		'nom'=>array(
			'valeur'=>$valeurs['nom'],
			'fieldset'=>0,
			'rang'=>50,
			'form'=>array(
				'field'=>'input',
				'name'=>'nom',
				'label'=>_T('gestpro:nom'),
				'obligatoire'=>'oui'),
				),
		'id_chef_projet'=>array(
			'valeur'=> $valeurs['id_chef_projet'],
			'fieldset'=>0,	
			'rang'=>150,		
			'form'=>array(
				'field'=>'auteurs',
				'name'=>'id_chef_projet',
				'option_statut'=>'oui',
				'obligatoire'=>'oui',
				'label'=>_T('gestpro:chef_projet'),
				),			
			),
		'descriptif'=>array(
			'valeur'=>$valeurs['descriptif'],		
			'fieldset'=>0,
			'rang'=>250,
			'form'=>array(
				'field'=>'textarea',
				'name'=>'descriptif',
				'label'=>_T('info_descriptif')
				),
			),	
		'montant_heure'=>array(
			'valeur'=>$valeurs['montant_heure'],		
			'fieldset'=>1,
			'rang'=>50,
			'form'=>array(
					'field'=>'input',
					'name'=>'montant_heure',
					'label'=>_T('gestpro:montant_heure').' €',	
				),					
			),			
		'montant_estime'=>array(
			'valeur'=>$valeurs['montant_estime'],		
			'fieldset'=>1,	
			'rang'=>150,	
			'form'=>array(
					'field'=>'input',
					'name'=>'montant_estime',
					'label'=>_T('gestpro:montant_estime').' €',	
				),								
			),	
		'duree_estimee'=>array(
			'valeur'=>$valeurs['duree_estimee'],		
			'fieldset'=>1,	
			'rang'=>250,	
			'form'=>array(
					'field'=>'input',
					'name'=>'duree_estimee',
					'label'=>_T('gestpro:duree_estimee'),
					'explication'=>_T('gestpro:explication_heure'),						
				),								
			),	
		'date_debut'=>array(
			//'valeur'=>$valeurs['date_debut'],		
			'fieldset'=>1,	
			'rang'=>350,	
			'form'=>array(
					'field'=>'date',
					'name'=>'date_debut',
					'label'=>_T('gestpro:date_debut'),	
				),								
			),	
		'date_fin_estimee'=>array(
			//'valeur'=>$valeurs['date_fin_estimee'],	
			'fieldset'=>1,	
			'rang'=>450,	
			'form'=>array(
					'field'=>'date',
					'name'=>'date_fin_estimee',
					'label'=>_T('gestpro:date_fin_estimee'),	
				),								
			),		
		);
	
	switch($use){
		case 'charger':
		//Préparation d l'array qui sert à construire le formulaire
		$formulaire=array();
			
		foreach ($champs AS $champ =>$valeur){
			$formulaire[$valeur['fieldset']][$valeur['rang']]=$valeur;
			}	
		
		$valeurs=array(
			'valeurs'=>$champs,
			'formulaire'=>$formulaire,
			'fieldsets'=>$fieldsets
			);		
		break;
		case 'verifier' :
		
		$valeurs=array();
		
		foreach ($champs AS $champ =>$valeur){
			if($valeur['form']['obligatoire']=='oui')$valeurs[]=$champ;
			}
		break;
		default:	$valeurs=$champs;
		}
	
	return $valeurs;
}

function formulaires_projets_charger_dist($id_projet=''){

	//On charge les définitions
	
	// Si l'id projet est connu, on charge les données

	if($id_projet){	
		$val=sql_fetsel('*','spip_projets','id_projet='.sql_quote($id_projet));	
		}
	else $val['id_chef_projet']=session_get('id_auteur');

	$dates= array('date_debut','date_fin_estimee');
	
	foreach($dates as $champ){
		if(idate('U',$val[$champ])==0)$val[$champ]='';
	}
	
	$definitions=definitions($use='charger',$val);
	
	//On définit les valeurs du formulaire
	$valeurs=$definitions['valeurs'];
	
	foreach($valeurs AS $key=>$champs){
		$valeurs[$key]=$valeurs[$key]['valeur'];
	}
	
	//Le valeurs pour la construction du formulaire
	$valeurs['formulaire']=$definitions['formulaire'];
	
	// Les fieldsets
	$valeurs['fieldsets']=$definitions['fieldsets'];	
	
	
	// Les valeurs spécifiques
	$valeurs['_hidden'].='<input type="hidden" name="id_auteur" value="'.$GLOBALS['visiteur_session']['id_auteur'].'"/>';
	

return $valeurs;
}

function formulaires_projets_verifier_dist($id_projet=''){
	include_spip('gestion_projets_fonctions');

	$obligatoire=definitions($use='verifier');


    $erreurs = array();
    foreach($obligatoire as $champ) {
        if (!trim(_request($champ))) {
            $erreurs[$champ] = _T('spip:info_obligatoire');
        }
    }
    if($date_debut=trim(_request('date_debut')) AND $date_fin_estimee=trim(_request('date_fin_estimee'))){
    echo _request('date_debut').'ok' ;
   	 $date_debut = explode('/',$date_debut);
  	$date_debut= $date_debut[2].'-'.$date_debut[1].'-'.$date_debut[0];
 	$date_fin_estimee = explode('/',$date_fin_estimee);
  	$date_fin_estimee= $date_fin_estimee[2].'-'.$date_fin_estimee[1].'-'.$date_fin_estimee[0];	
  	if (difference($date_debut,$date_fin_estimee,3600)<=0)$erreurs['date_fin_estimee'] = _T('gestpro:erreur_date');
    }
    if (count($erreurs)) {
        $erreurs['message_erreur'] = _T('spip:avis_erreur');
    }
    
    return $erreurs;
}

function formulaires_projets_traiter_dist($id_projet=''){
   	refuser_traiter_formulaire_ajax(); 	
	$champs=definitions();
	$valeurs=array();
	
	foreach($champs as $key=>$val){
			$valeurs[$key]=_request($key);
		}
	
	$date_debut = explode('/',$valeurs['date_debut']);
	$valeurs['date_debut']= $date_debut[2].'-'.$date_debut[1].'-'.$date_debut[0];
	
	$date_fin_estimee = explode('/',$valeurs['date_fin_estimee']);
	$valeurs['date_fin_estimee']= $date_fin_estimee[2].'-'.$date_fin_estimee[1].'-'.$date_fin_estimee[0];
	$valeurs['date_creation']= date('Y-m-d G-i-s');
	$valeurs['statut']= '10incomplet';

	// Effectuer des traitements

	//Si il n'ya pas l'id_projet on crée un nouveau
	if(!$id_projet)	$id_projet=sql_insertq('spip_projets',$valeurs);
		
	//Sinon on modifie
	
	else sql_updateq('spip_projets',$valeurs,'id_projet='.sql_quote($id_projet));
	
	header('Location:'.generer_url_ecrire("projets","voir=projet&id_projet=$id_projet",true));

return;
    
}
	 
?>