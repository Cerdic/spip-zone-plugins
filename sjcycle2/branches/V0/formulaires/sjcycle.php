<?php 
include_spip('sjcycle_fonctions');

function formulaires_sjcycle_charger_dist(){
	$valeurs = array ();
	//Charger les valeurs par défaut
	$valeurs = lire_config('sjcycle');
	//si rien en base
	if (count($valeurs)==0) {
		/*$valeurs = default_values_assign();*/
		$valeurs = init_sjcycle_default('default_value_list');
		//on ecrit en base
		ecrire_config('sjcycle',serialize($valeurs));
		//on affecte les valeurs de _request
		foreach ($valeurs as $key => $value) {
			set_request($key, $value);
		}
	}						
    return $valeurs;
}

function formulaires_sjcycle_verifier_dist(){
	$valeurs = array ();
	$erreurs = array();	
	$log_modif = '';
		
	// si on a pas poste de formulaire, pas la peine de controler
	// ce qui mettrait de fausses valeurs dans l'environnement
	if  (!_request('_cfg_ok') && !_request('_cfg_delete')) { return true; }

	$valeurs_enregistrees = lire_config('sjcycle');
	
	if (!lire_config('image_process')){
		$erreurs['message_erreur'] = _T('sjcycle:erreur_config_image_process');
			return $erreurs;
	}
	
	
	if (lire_config('creer_preview')!='oui') {//Génération de miniatures des images inactive
		$erreurs['message_erreur'] = _T('sjcycle:erreur_config_creer_preview');
			return $erreurs;
	}
	
	
		
	/*
		$champs = array_keys(default_values_assign());
		*/
	$champs = init_sjcycle_default();
		// stockage des nouvelles valeurs
		foreach ($champs as $name) {
			// enregistrement des valeurs postees
			$oldval = $valeurs_enregistrees[$name];
		    $valeurs[$name] = _request($name);
		    // tracer les modifications
		    if ($oldval != $valeurs[$name]) {
		    	$log_modif .= $name . ':' . var_export($oldval, true) . '/' . var_export($valeurs[$name], true) .', ';
		    }
		}
			
		// si pas de changement, pas la peine de continuer
		if (!$log_modif && !_request('_cfg_delete')) {
		//if (strlen($log_modif)==0) {
			$erreurs['message_erreur'] = _T('cfg:pas_de_changement',array('nom' => 'sjcycle'));
			return $erreurs;
		}

	
	//si reinitialise le plugin	
	if (_request('_cfg_delete')){
		effacer_config('sjcycle');
		ecrire_config('sjcycle',serialize(formulaires_sjcycle_charger_dist()));
		return array("editable" => true,"message_ok" => _T("sjcycle:sjcycle_reinitialise"));
	}
	
	// verifier que les champs obligatoires sont bien la :
	foreach(init_sjcycle_default('obligatoire_list') as $obligatoire){
		if (strlen(_request($obligatoire))==0) { $erreurs[$obligatoire] = 'Le champ "'._T('sjcycle:'.$obligatoire).'" est obligatoire'; }
	}

	// verification des données _request
	


$rulz = init_sjcycle_default('preg_match_rule_list');


foreach ($valeurs as $key => $value) {
	if($rulz[$key]){
		$tmp_clean_value = checkRulz ($value,$rulz[$key]);
		if(strlen($tmp_clean_value)>0){
			set_request($key,$tmp_clean_value);
		} else {
			$erreurs[$key] = _T('sjcycle:sjcycle_champ_erreur',array('champ' => _T('sjcycle:'.$key)));
		}
	}
}
	
	if (count($erreurs)){
		$erreurs['message_erreur'] = _T('sjcycle:sjcycle_message_erreur');
	}
	
	return $erreurs;
}

function formulaires_sjcycle_traiter_dist(){
	$valeurs = array();
	
	foreach (init_sjcycle_default() as $key) {
		$valeurs[$key]=_request($key);
	}
	//CALCUL DE LA TAILLE DU DIAPORAMA
	$valeurs['sjcycle_width'] = (_request('sjcycle_img_margin')*2) + _request('sjcycle_img_width');
	$valeurs['sjcycle_height'] = (_request('sjcycle_img_margin')*2) + _request('sjcycle_img_height');
	
	$taille_spip_preview = lire_config('taille_preview');//Taille maximale des vignettes générées par le système
	
	$taille_reduire_max = _request('sjcycle_img_height');
	
	if(_request('sjcycle_img_width') >_request('sjcycle_img_height')){
		$taille_reduire_max = _request('sjcycle_img_width');
	}
	if($taille_spip_preview < $taille_reduire_max){
		ecrire_config('taille_preview',$taille_reduire_max);
	}
	ecrire_config('sjcycle',serialize($valeurs));
	return array("editable" => true,"message_ok" => _T('cfg:config_enregistree',array('nom' => 'sjcycle')));
}
?>