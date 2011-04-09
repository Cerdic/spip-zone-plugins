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
	$champs = array('pluginxml', 'encodage');
	foreach($champs as $_champ){
		$champs[$_champ] = _request($_champ);
	}

	// Generation du fichier
 	$generer = charger_fonction('plugonet_generer','inc');
 	list($msg, $erreurs, $res) = $generer($champs['pluginxml'], true);

	$msg .= join("<br />\n",  $erreurs) . "<br /><br />" . join("<br />\n",  $res);
	return array('message_ok' => $msg, 'editable' => true);;
}

?>