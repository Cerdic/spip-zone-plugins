<?php

function definitions_taches($use='',$valeurs=''){

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
				'obligatoire'=>'oui')),	
		'id_projet'=>array(
			'valeur'=> $valeurs['id_projet'],
			'fieldset'=>0,	
			'rang'=>150,		
			'form'=>array(
				'field'=>'selection',
				'name'=>'id_projet',
				'label'=>_T('gestpro:projet'),
				'datas'=>$valeurs['projets'],
				'obligatoire'=>'oui'
				),			
			),
		'id_parent'=>array(
			'valeur'=> $valeurs['id_parent'],
			'fieldset'=>0,	
			'rang'=>250,		
			'form'=>array(
				'field'=>'selection',
				'name'=>'id_parent',
				'label'=>_T('gestpro:tache_parente'),
				'datas'=>$valeurs['taches_projet']
				),			
			),
		'descriptif'=>array(
			'valeur'=>$valeurs['descriptif'],		
			'fieldset'=>0,
			'rang'=>350,
			'form'=>array(
				'field'=>'textarea',
				'name'=>'descriptif',
				'label'=>_T('info_descriptif')
				),
			),	
		'participants'=>array(
			'valeur'=>$valeurs['participants'],		
			'fieldset'=>0,
			'rang'=>450,
			'form'=>array(
				'field'=>'auteurs_limites',
				'name'=>'participants',
				'label'=>_T('gestpro:participants'),
				'datas'=>$valeurs['participants_parent'],
				'obligatoire'=>'oui',				
				'multiple'=>'oui'
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
		'montant_reel'=>array(
			'valeur'=>$valeurs['montant_reel'],		
			'fieldset'=>1,	
			'rang'=>250,	
			'form'=>array(
					'field'=>'input',
					'name'=>'montant_reel',
					'label'=>_T('gestpro:montant_reel').' €',	
				),								
			),				
		'duree_estimee'=>array(
			'valeur'=>$valeurs['duree_estimee'],		
			'fieldset'=>1,	
			'rang'=>350,	
			'form'=>array(
					'field'=>'input',
					'name'=>'duree_estimee',
					'label'=>_T('gestpro:duree_estimee'),
					'explication'=>_T('gestpro:explication_heure'),						
				),								
			),	
		'duree_reelle'=>array(
			'valeur'=>$valeurs['duree_reelle'],		
			'fieldset'=>1,	
			'rang'=>450,	
			'form'=>array(
					'field'=>'input',
					'name'=>'duree_reelle',
					'label'=>_T('gestpro:duree_reelle'),
					'explication'=>_T('gestpro:explication_heure'),						
				),								
			),				
		'date_debut'=>array(
			'valeur'=>$valeurs['date_debut'],		
			'fieldset'=>1,	
			'rang'=>550,	
			'form'=>array(
					'field'=>'date',
					'name'=>'date_debut',
					'label'=>_T('gestpro:date_debut'),	
				),								
			),	
		'date_fin_estimee'=>array(
			'valeur'=>$valeurs['date_fin_estimee'],	
			'fieldset'=>1,	
			'rang'=>650,	
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

function formulaires_taches_charger_dist(){

	$id_projet=_request('id_projet');
	$id_tache=_request('id_tache');
	if($id_tache){
		$valeurs_taches=sql_fetsel('*','spip_projets_taches','id_tache='.sql_quote($id_tache));
		$id_projet=$valeurs_taches['id_projet'];
		}
	else{
		// on détermine les sommes des montants estimés et duree estimée des autres tâches du projet	
		$champs=array('montant_heure','montant_estime','duree_estimee','date_debut','date_fin_estimee');
		$projet =sql_fetsel($champs,'spip_projets','id_projet='.sql_quote($id_projet));
		$taches =sql_select('montant_estime,duree_estimee','spip_projets_taches','id_projet='.sql_quote($id_projet));
		
		$compteur=array();
		while($data = sql_fetch($taches)){
			foreach($data as $champ=>$valeur){
				$compteur[$champ][]=$valeur;
				}
			}
				
		foreach($projet as $champ=>$valeur){
			$val[$champ]=$valeur;
			}
			
		if(is_array($compteur['montant_estime']))$val['montant_estime']=$val['montant_estime']-array_sum($compteur['montant_estime']);
		if(is_array($compteur['duree_estimee']))$val['duree_estimee']=$val['duree_estimee']-array_sum($compteur['duree_estimee']);			
		}

	
	//On charge les définitions
	
	// Si l'id tache est connu, on charge les données de la tâche

	if($id_tache){	
		$val=$valeurs_taches;
		if($val['participants'])$val['participants_parent'] = unserialize($val['participants']);
		else $val['participants_parent'] = array(0=>$val['id_chef_projet']);
		$val['participants']	= unserialize($val['participants']);
		}
	else{
		//sinon le donées parentes
		
		$projet=sql_fetsel('participants,id_chef_projet','spip_projets','id_projet='.sql_quote($id_projet));
		
		if($projet['participants'])$val['participants_parent'] = unserialize($projet['participants']);
		else $val['participants_parent'] = array(0=>$projet['id_chef_projet']);
		
		$val['participants']=$val['participants_parent'];	
		}
		
	$taches= sql_select('id_tache,nom','spip_projets_taches','id_projet='.sql_quote($id_projet));
	
	//Les projets existants
	
	$projets=sql_select('id_projet,nom','spip_projets');
		
	$val['projets']=array();
	
	while($data=sql_fetch($projets)){
		$val['projets'][$data['id_projet']]=$data['nom'];
		}	
	
	$val['id_projet']=$id_projet;
	//Les taches existants du projet

	$val['taches_projet']=array();
	
	while($data=sql_fetch($taches)){
		if($id_tache!=$data['id_tache'])$val['taches_projet'][$data['id_tache']]=$data['nom'];
		}		

	$dates= array('date_debut','date_fin_estimee');
	
	foreach($dates as $champ){
		if(idate('U',$val[$champ])==0)$val[$champ]='';
	}
	
	//On définit les valeurs du formulaire
	
	$definitions=definitions_taches($use='charger',$val);
	

	$valeurs=$definitions['valeurs'];
	
	
	foreach($valeurs AS $key=>$champs){
		$valeurs[$key]=$valeurs[$key]['valeur'];
	}
	//Le valeurs pour la construction du formulaire
	$valeurs['formulaire']=$definitions['formulaire'];
	
	if(!$id_tache){
		$valeurs['formulaire'][1][250]='';
		$valeurs['formulaire'][1][450]='';		
		}
	

	
	// Les fieldsets
	$valeurs['fieldsets']=$definitions['fieldsets'];	
	
	$valeurs['_hidden'].='<input type="hidden" name="id_projet" value="'.$id_projet.'"/>';

return $valeurs;
}

function formulaires_taches_verifier_dist(){
	include_spip('gestion_projets_fonctions');
	
	$obligatoire=definitions_taches($use='verifier');

    $erreurs = array();
    foreach($obligatoire as $champ) {
        if (!_request($champ)) {
            $erreurs[$champ] = "Cette information est obligatoire !";
        }
    }
    
    if(_request('date_debut') OR _request('date_fin_estimee') ){
   	 $date_debut = explode('/',_request('date_debut'));
  	$date_debut= $date_debut[2].'-'.$date_debut[1].'-'.$date_debut[0];
 	$date_fin_estimee = explode('/',_request('date_fin_estimee'));
  	$date_fin_estimee= $date_fin_estimee[2].'-'.$date_fin_estimee[1].'-'.$date_fin_estimee[0];	
  	if (difference($date_debut,$date_fin_estimee,3600)<=0)$erreurs['date_fin_estimee'] = _T('gestpro:erreur_date');
    }
    
    if (count($erreurs)) {
        $erreurs['message_erreur'] = _T('spip:avis_erreur');
    }
    
    return $erreurs;
}

function formulaires_taches_traiter_dist(){
   	refuser_traiter_formulaire_ajax(); 	
   	
   	$id_projet=_request('id_projet');
	$id_tache=_request('id_tache');
	   	
	$champs=definitions_taches();
	$valeurs=array();
	
	foreach($champs as $key=>$val){
			$valeurs[$key]=_request($key);
		}
	$valeurs['id_projet']=$id_projet;
	$date_debut = explode('/',$valeurs['date_debut']);
	$valeurs['date_debut']= $date_debut[2].'-'.$date_debut[1].'-'.$date_debut[0];
	
	$date_fin_estimee = explode('/',$valeurs['date_fin_estimee']);
	$valeurs['date_fin_estimee']= $date_fin_estimee[2].'-'.$date_fin_estimee[1].'-'.$date_fin_estimee[0];
	$valeurs['date_creation']= date('Y-m-d G-i-s');
	$valeurs['statut']= 'active';
	$valeurs['participants']=serialize(_request('participants'));
	$valeurs['id_projet']=$id_projet;	
	

	// Effectuer des traitements

	//Si il n'ya pas l'id_projet on crée un nouveau
	if(!$id_tache)	$id_tache=sql_insertq('spip_projets_taches',$valeurs);
		
	//Sinon on modifie
	
	else sql_updateq('spip_projets_taches',$valeurs,'id_tache='.sql_quote($id_tache));
	
	if(_request('montant_reel') or _request('duree_reelle')){
		$taches=sql_select('montant_reel,duree_reelle','spip_projets_taches','id_projet='.sql_quote($id_projet));
	
		$compteur=array();
		while($data = sql_fetch($taches)){
			foreach($data as $champ=>$valeur){
				$compteur[$champ][]=$valeur;
				}
			}

		if(is_array($compteur['montant_reel']))$montant_reel=array_sum($compteur['montant_reel']);
		if(is_array($compteur['duree_reelle']))$duree_reelle=array_sum($compteur['duree_reelle']);		
		
		$val_projet=array(
			'montant_reel'=>$montant_reel,
			'duree_reelle'=>$duree_reelle,			
			);
		sql_updateq('spip_projets',$val_projet,'id_projet='.sql_quote($id_projet));
		}
	
	header('Location:'.generer_url_ecrire("projets","voir=projet&id_projet=$id_projet&deplie_taches=true#taches",true));

return;
    
}
	 
?>