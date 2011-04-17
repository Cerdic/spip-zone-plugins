<?php

function formulaires_plugonet_generer_charger() {
	if (!_request('un_pluginxml'))
		return 	array();
	else 
		return formulaires_plugonet_generer_traiter();
}

function formulaires_plugonet_generer_verifier() {
	$erreurs = array();
	$obligatoires = array('pluginxml');
	foreach ($obligatoires as $_obligatoire){
		if(!_request($_obligatoire)){
			$erreurs[$_obligatoire] = _T('langonet:message_nok_champ_obligatoire');
		}
	}
	return $erreurs;
}

function formulaires_plugonet_generer_traiter() {
	// Recuperation des champs du formulaire
	if (!$pluginxml = _request('pluginxml'))
		$pluginxml = array(_request('un_pluginxml'));
	$forcer = (_request('forcer')) ? true : false;
	$simuler = (_request('simuler')) ? true : false;

	// Generation du fichier
 	$generer = charger_fonction('plugonet_generer','inc');
 	list($erreurs, $commandes) = $generer($pluginxml, $forcer, $simuler);

	// Formatage et affichage des resultats
	// -- Message global sur la generation des fichiers : toujours ok aujourd'hui
	// -- Texte des resultats par fichier traite
 	$formater = charger_fonction('plugonet_formater','inc');
	$retour = array();
 	list($resume, $analyse) = $formater($erreurs);
 	$retour['message_ok']['resume'] = $resume;
 	$retour['message_ok']['analyse'] = $analyse;
	$retour['editable'] = true;
	
	return $retour;
}

?>