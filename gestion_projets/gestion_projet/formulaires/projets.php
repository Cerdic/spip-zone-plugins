<?php

function definitions(){
	$valeurs=array(
		'nom'=>'',
		'id_parent'=>'',
		'id_chef_projet'=>'',
		'descriptif'=>'',				
		'montant_estime'=>'',	
		'duree_estimee'=>'',
		'date_debut'=>'',	
		'date_fin_estimee'=>'',																	
		);
	return $valeurs;

}

function formulaires_projets_charger_dist(){

$valeurs=definitions();

	$valeurs['_hidden'].='<input type="hidden" name="id_auteur" value="'.$GLOBALS['visiteur_session']['id_auteur'].'"/>';


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