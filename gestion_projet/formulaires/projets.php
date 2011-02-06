<?php
function formulaires_projets_charger_dist(){

	$valeurs=array('nom'=>'','id_parent'=>'','id_auteur'=>'');
	$valeurs['_hidden'].='<input type="hidden" name="id_auteur" value="'.$GLOBALS['visiteur_session']['id_auteur'].'"/>';


return $valeurs;
}

function formulaires_projets_verifier_dist(){

    $erreurs = array();
    foreach(array('nom') as $champ) {
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

// Effectuer des traitements
	$valeurs=array(
		'nom'=>_request('nom'),
		'id_auteur'=>_request('id_auteur'),
		'date_creation'=>date('Y-m-d G:i:s'),				
		);
		
	$id_projet=sql_insertq('spip_projets',$valeurs)	;
    
    // Valeurs de retours
    return array(
        'message_ok' => $message_ok, // ou bien
        'message_erreur' => $message_erreur);
}
	 
?>