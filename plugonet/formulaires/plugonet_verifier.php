<?php

function formulaires_plugonet_verifier_charger(){
	$valeurs = array('pluginxml' => _request('pluginxml'));
	return $valeurs;
}

function formulaires_plugonet_verifier_verifier(){
	$erreurs = array();
	$obligatoires = array('pluginxml');
	foreach($obligatoires as $_obligatoire){
		if(!_request($_obligatoire)){
			$erreurs[$_obligatoire] = _T('langonet:message_nok_champ_obligatoire');
		}
	}
	return $erreurs;
}

function formulaires_plugonet_verifier_traiter(){
	// Recuperation des champs du formulaire
	$champs = array('pluginxml');
	foreach($champs as $_champ){
		$champs[$_champ] = _request($_champ);
	}

	// Verification du fichier
	
	return array('message_ok' => $msg, 'editable' => true);;
}

?>