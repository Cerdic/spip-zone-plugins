<?php

function formulaires_plugonet_verifier_charger(){
	if (!_request('un_xml'))
		return 	array();
	else 
		return formulaires_plugonet_verifier_traiter();
}

function formulaires_plugonet_verifier_verifier(){
	$erreurs = array();
	$obligatoires = array('xml');
	foreach($obligatoires as $_obligatoire){
		if(!_request($_obligatoire)){
			$erreurs[$_obligatoire] = _T('info_obligatoire');
		}
	}
	return $erreurs;
}

function formulaires_plugonet_verifier_traiter(){
	// Recuperation des champs du formulaire
	if (!$pluginxml = _request('xml'))
		$pluginxml = array(_request('un_xml'));

	// Generation du fichier
	$traitement = 'verification_pluginxml';
 	$verifier = charger_fonction('plugonet_traiter','inc');
 	list($erreurs, $duree,) = $verifier($traitement, $pluginxml);

	// Formatage et affichage des resultats
	// -- Message global sur la generation des fichiers : toujours ok aujourd'hui
	// -- Texte des resultats par fichier traite
 	$formater = charger_fonction('plugonet_formater','inc');
	$retour = array();
 	list($resume, $analyse) = $formater($traitement, $erreurs, $duree);
 	$retour['message_ok']['resume'] = $resume;
 	$retour['message_ok']['analyse'] = $analyse;
	$retour['editable'] = true;
	
	return $retour;
}

?>