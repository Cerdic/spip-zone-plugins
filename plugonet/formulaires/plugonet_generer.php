<?php

function formulaires_plugonet_generer_charger(){
	$valeurs = array();
	return $valeurs;
}

function formulaires_plugonet_generer_verifier(){
	$erreurs = array();
	$obligatoires = array('pluginxml');
	foreach($obligatoires as $_obligatoire){
		if(!_request($_obligatoire)){
			$erreurs[$_obligatoire] = _T('langonet:message_nok_champ_obligatoire');
		}
	}
	return $erreurs;
}

function formulaires_plugonet_generer_traiter(){
	// Recuperation des champs du formulaire
	$pluginxml = _request('pluginxml');
	$forcer = (_request('forcer')) ? true : false;

	// Generation du fichier
 	$generer = charger_fonction('plugonet_generer','inc');
 	list($erreurs, $commandes) = $generer($pluginxml, $forcer);

	// Traitement et affichage des resultats
	var_dump('erreurs', $erreurs);
	var_dump('commandes', $commandes);
	
	return array('message_ok' => $msg, 'editable' => true);;
}

?>