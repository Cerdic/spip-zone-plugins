<?php
/**
 * vérification des numéros internationaux
 * 
 * @plugin     libphonenumber for SPIP
 * @copyright  2019
 * @author     Anne-lise Martenot
 * @licence    GNU/GPL
 * (c) 2019 - Distribue sous licence GNU/GPL
 *
**/
 
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


function formulaires_libphonenumber_charger_dist(){
	
	$valeurs = array(
		'pays'=>'',
		'telephone'=>'',
		'force_telephone'=>'',
	);

	return $valeurs;
}


function formulaires_libphonenumber_verifier_dist(){
	$erreurs = array();
	
	//vérifier valeur des champs
    $verifier = charger_fonction('verifier', 'inc');
    
    //pays par defaut à FR
    $pays = _request('pays');
    
    //telephone la vérification peut être forcé
    $force_telephone = _request('force_telephone');
    if($force_telephone != 'on'){
		$telephone = _request('telephone');
		$erreur_telephone = $verifier($telephone, 'phone', array('prefixes_pays' => $pays));
		if ($erreur_telephone) {
			$erreurs['telephone'] = $verifier($telephone, 'phone', array('prefixes_pays' => $pays));
		}
	}
	
	//verifier l'existence
	foreach(array('telephone','pays') as $champ) {
        if (!_request($champ)) {
            $erreurs[$champ] = "<span class='erreur'>Cette information est obligatoire !</span>";
        }
    }
	
    if (count($erreurs)) {
       $erreurs['message_erreur'] =  "Une erreur est présente dans votre saisie";
    }
	return $erreurs;
}

function formulaires_libphonenumber_traiter_dist(){
	
	//aucun traitement en demo, cependant on pourrait vouloir utiliser le plugin coordonnées
	//on laisse donc l'exemple de code
	/*
	$id_auteur = 1;
	
	// On recherche le numero
	$id_numero = sql_getfetsel(
		'id_numero',
		'spip_numeros_liens',
		array(
			'objet = '.sql_quote('auteur'),
			'id_objet = '.$id_auteur,
			'type = '.sql_quote('pref')
		)
	);
	
	// S'il n'y a pas de numero de telephone pref, on le crée
	if (!$id_numero){
		$id_numero = 'new';
	}
	
	set_request('objet', 'auteur');
	set_request('id_objet', $id_auteur);
	set_request('type', 'pref');
	set_request('numero', $numero);
		
	$editer_numero = charger_fonction('editer_numero', 'action/');
	$editer_numero($id_numero);
	*/
	
	$res['message_ok'] = "Bravo, vérification réussie en mode démo, aucun traitement !";
	
	return $res;
}