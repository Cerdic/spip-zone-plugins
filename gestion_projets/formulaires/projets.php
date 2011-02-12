<?php

function definitions($tout=''){

	//Définition des fieldset

	$fieldsets=array(
		0=>_T('donnees'),
		1=>_T('options')
		);	
		

	//Définition des champs

	$champs=array(
		'nom'=>array(
			'valeur'=>'',
			'fieldset'=>_T('donnees'),
			'rang'=>0,
			'form'=>array(
				'field'=>'input',
				'name'=>'nom',
				'label'=>_T('gestpro:nom'),
				'obligatoire'=>'oui')),
		'id_chef_projet'=>array(
			'valeur'=> session_get('id_auteur'),
			'fieldset'=>_T('donnees'),	
			'rang'=>2,		
			'form'=>array(
				'field'=>'auteurs',
				'name'=>'id_chef_projet',
				'option_statut'=>'oui',
				'obligatoire'=>'oui',
				'label'=>_T('gestpro:chef_projet'),
				),			
			),
		'descriptif'=>array(
			'fieldset'=>_T('donnees'),
			'rang'=>1,
			'form'=>array(
				'field'=>'textarea',
				'name'=>'descriptif',
				'label'=>_T('info_descriptif')
				),
			),				
		'montant_estime'=>array(
			'fieldset'=>_T('options'),	
			'rang'=>0,	
			'form'=>array(
					'field'=>'input',
					'name'=>'montant_estime',
					'label'=>_T('gestpro:montant_estime').' €',	
				),								
			),	
		'duree_estimee'=>array(
			'fieldset'=>_T('options'),	
			'rang'=>1,	
			'form'=>array(
					'field'=>'input',
					'name'=>'duree_estimee',
					'label'=>_T('gestpro:duree_estimee'),
					'explication'=>_T('gestpro:explication_heure'),						
				),								
			),	
		'date_debut'=>array(
			'fieldset'=>_T('options'),	
			'rang'=>2,	
			'form'=>array(
					'field'=>'date',
					'name'=>'date_debut',
					'label'=>_T('gestpro:date_debut'),	
				),								
			),	
		'date_fin_estimee'=>array(
			'fieldset'=>_T('options'),	
			'rang'=>3,	
			'form'=>array(
					'field'=>'date',
					'name'=>'date_fin_estimee',
					'label'=>_T('gestpro:date_fin_estimee'),	
				),								
			),		
		);
		

	$formulaire=array();
		
	foreach ($champs AS $champ =>$valeur){

		if(in_array($valeur['fieldset'],$fieldsets)){
			$formulaire[$valeur['fieldset']][$valeur['rang']]=$valeur;
			}
	
		}	
	if(!$tout)	$valeurs=$champs;
	else $valeurs=array(
		'valeurs'=>$champs,
		'formulaire'=>$formulaire,
		'fieldsets'=>$fieldsets		
		);
	return $valeurs;

}

function formulaires_projets_charger_dist(){

	$defs='tout';
	$definitions=definitions($defs);
	
	$valeurs=$definitions['valeurs'];
	
	foreach($valeurs AS $key=>$champs){
		$valeurs[$key]=$valeurs[$key]['valeur'];
	}
	
	$valeurs['formulaire']=$definitions['formulaire'];
	$valeurs['fieldsets']=$definitions['fieldsets'];	
	

	$valeurs['_hidden'].='<input type="hidden" name="id_auteur" value="'.$GLOBALS['visiteur_session']['id_auteur'].'"/>';
	
	$valeurs['essai']=array('field'=>'input',
							'name'=>'nom',
							'label'=>_T('gestpro:nom'),
							'obligatoire'=>'oui');


return $valeurs;
}

function formulaires_projets_verifier_dist(){

    $erreurs = array();
    foreach(array('nom','id_chef_projet') as $champ) {
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

//$message_ok='ok';

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


	
	    
    // Valeurs de retours
    return array(
        'message_ok' => $message_ok, // ou bien
        'message_erreur' => $message_erreur);
    
}
	 
?>