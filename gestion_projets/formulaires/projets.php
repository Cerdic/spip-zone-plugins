<?php

function definitions($use=''){

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
			'valeur'=>'',
			'fieldset'=>0,
			'rang'=>50,
			'form'=>array(
				'field'=>'input',
				'name'=>'nom',
				'label'=>_T('gestpro:nom'),
				'obligatoire'=>'oui')),
		'id_chef_projet'=>array(
			'valeur'=> session_get('id_auteur'),
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
			'fieldset'=>0,
			'rang'=>250,
			'form'=>array(
				'field'=>'textarea',
				'name'=>'descriptif',
				'label'=>_T('info_descriptif')
				),
			),				
		'montant_estime'=>array(
			'fieldset'=>1,	
			'rang'=>50,	
			'form'=>array(
					'field'=>'input',
					'name'=>'montant_estime',
					'label'=>_T('gestpro:montant_estime').' €',	
				),								
			),	
		'duree_estimee'=>array(
			'fieldset'=>1,	
			'rang'=>150,	
			'form'=>array(
					'field'=>'input',
					'name'=>'duree_estimee',
					'label'=>_T('gestpro:duree_estimee'),
					'explication'=>_T('gestpro:explication_heure'),						
				),								
			),	
		'date_debut'=>array(
			'fieldset'=>1,	
			'rang'=>250,	
			'form'=>array(
					'field'=>'date',
					'name'=>'date_debut',
					'label'=>_T('gestpro:date_debut'),	
				),								
			),	
		'date_fin_estimee'=>array(
			'fieldset'=>1,	
			'rang'=>350,	
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
			$formulaire[$fieldsets[$valeur['fieldset']]['titre']][$valeur['rang']]=$valeur;
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

function formulaires_projets_charger_dist(){

	//On charge les définitions

	$definitions=definitions($use='charger');
	
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

function formulaires_projets_verifier_dist(){

	$obligatoire=definitions($use='verifier');


    $erreurs = array();
    foreach($obligatoire as $champ) {
        if (!_request($champ)) {
            $erreurs[$champ] = "Cette information est obligatoire !";
        }
    }
    if (count($erreurs)) {
        $erreurs['message_erreur'] = "Une erreur est présente dans votre saisie";
    }
    
    return $erreurs;
}

function formulaires_projets_traiter_dist(){
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
	$valeurs['statut']= 'desactive';

	// Effectuer des traitements

		
	$id_projet=sql_insertq('spip_projets',$valeurs);
	
	header('Location:'.generer_url_ecrire("projets","voir=projet&id_projet=$id_projet",true));

return;
    
}
	 
?>